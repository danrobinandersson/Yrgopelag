<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Services/CentralBankClient.php';

use Dotenv\Dotenv;
use App\Services\CentralBankClient;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$centralBank = new CentralBankClient();

// Basic POST validation
if (
    !isset(
        $_POST['guest_name'],
        $_POST['transfercode'],
        $_POST['arrival'],
        $_POST['departure'],
        $_POST['room']
    )
) {
    echo 'Invalid booking request';
    exit;
}

// Connect to database
try {
    $database = new PDO('sqlite:' . __DIR__ . '/database/database.db');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Database connection failed';
    exit;
}

// INPUT VALIDATION

$guestName    = trim($_POST['guest_name']);
$transferCode = trim($_POST['transfercode']);
$arrival      = $_POST['arrival'];
$departure    = $_POST['departure'];
$roomTier     = $_POST['room'];
$featuresUsed = $_POST['features'] ?? [];
$hotelOwner   = 'Robin';

if ($guestName === '') {
    echo 'Guest name is required';
    exit;
}

if ($arrival === '' || $departure === '') {
    echo 'You must select both arrival and departure dates';
    exit;
}

if ($arrival >= $departure) {
    echo 'Departure must be after arrival';
    exit;
}

// ROOM LOOKUP

$stmt = $database->prepare(
    'SELECT id, price_per_night FROM rooms WHERE tier = :tier LIMIT 1'
);
$stmt->execute([':tier' => $roomTier]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if ($room === false) {
    echo 'Invalid room selected';
    exit;
}

$roomId    = (int) $room['id'];
$roomPrice = (float) $room['price_per_night'];

// AVAILABILITY CHECK

$stmt = $database->prepare(
    'SELECT COUNT(*) FROM bookings
     WHERE room_id = :room_id
     AND arrival_date < :departure
     AND departure_date > :arrival'
);

$stmt->execute([
    ':room_id'   => $roomId,
    ':arrival'   => $arrival,
    ':departure' => $departure,
]);

if ((int) $stmt->fetchColumn() > 0) {
    echo 'Selected room is not available for these dates';
    exit;
}

// GUEST LOOKUP (for loyalty discount)

$stmt = $database->prepare(
    'SELECT id, visits FROM guests WHERE name = :name LIMIT 1'
);
$stmt->execute([':name' => $guestName]);
$guest = $stmt->fetch(PDO::FETCH_ASSOC);

$visits = $guest ? (int) $guest['visits'] : 0;


// PRICE CALCULATION

$nights     = (int) (new DateTime($arrival))->diff(new DateTime($departure))->days;
$roomTotal  = $roomPrice * $nights;
$featureTotal = 0;
$featureIds = [];

if (!empty($featuresUsed)) {
    $placeholders = implode(',', array_fill(0, count($featuresUsed), '?'));
    $stmt = $database->prepare(
        "SELECT id, price FROM features WHERE feature_name IN ($placeholders)"
    );
    $stmt->execute($featuresUsed);

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $feature) {
        $featureTotal += (float) $feature['price'];
        $featureIds[] = (int) $feature['id'];
    }
}

$totalPrice = $roomTotal + $featureTotal;

// Loyalty discount for returning guests
$discountPercent = 0;

if ($visits >= 1) {
    $discountPercent = 8;
}

if ($discountPercent > 0) {
    $discountAmount = ($totalPrice * $discountPercent) / 100;
    $totalPrice -= $discountAmount;
}


// CENTRAL BANK

if (!$centralBank->validateTransferCode($transferCode, (int) $totalPrice)) {
    echo 'Invalid or insufficient transfer code';
    exit;
}

if (!$centralBank->deposit($hotelOwner, $transferCode)) {
    echo 'Payment failed. Booking not completed.';
    exit;
}

// DATABASE TRANSACTION

try {
    $database->beginTransaction();

    // Guest lookup
    $stmt = $database->prepare(
        'SELECT id, visits FROM guests WHERE name = :name LIMIT 1'
    );
    $stmt->execute([':name' => $guestName]);
    $guest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($guest) {
        $guestId = (int) $guest['id'];
        $stmt = $database->prepare(
            'UPDATE guests SET visits = visits + 1 WHERE id = :id'
        );
        $stmt->execute([':id' => $guestId]);
    } else {
        $stmt = $database->prepare(
            'INSERT INTO guests (name, visits) VALUES (:name, 1)'
        );
        $stmt->execute([':name' => $guestName]);
        $guestId = (int) $database->lastInsertId();
    }

    // Insert booking
    $stmt = $database->prepare(
        'INSERT INTO bookings
        (guest_id, room_id, arrival_date, departure_date, total_price, transfercode)
        VALUES
        (:guest_id, :room_id, :arrival, :departure, :total_price, :transfercode)'
    );

    $stmt->execute([
        ':guest_id'     => $guestId,
        ':room_id'      => $roomId,
        ':arrival'      => $arrival,
        ':departure'    => $departure,
        ':total_price'  => $totalPrice,
        ':transfercode' => $transferCode,
    ]);

    $bookingId = (int) $database->lastInsertId();

    // Booking features
    if (!empty($featureIds)) {
        $stmt = $database->prepare(
            'INSERT INTO bookings_features (booking_id, feature_id)
             VALUES (:booking_id, :feature_id)'
        );

        foreach ($featureIds as $featureId) {
            $stmt->execute([
                ':booking_id' => $bookingId,
                ':feature_id' => $featureId,
            ]);
        }
    }

    $database->commit();
} catch (Throwable $e) {
    $database->rollBack();
    echo 'Booking failed. Please try again.';
    exit;
}

// SEND RECEIPT (AFTER COMMIT)

// Build features_used for receipt (category + tier)
$featureObjects = [];

if (!empty($featuresUsed)) {
    $placeholders = implode(',', array_fill(0, count($featuresUsed), '?'));

    $stmt = $database->prepare(
        "SELECT category, tier
         FROM features
         WHERE feature_name IN ($placeholders)"
    );

    $stmt->execute($featuresUsed);

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $feature) {
        $featureObjects[] = [
            'activity' => $feature['category'],
            'tier'     => $feature['tier'],
        ];
    }
}

// CONFIRMATION

echo '<h2>Booking confirmed</h2>';
echo '<p>Thank you, ' . htmlspecialchars($guestName) . '!</p>';
echo '<p><strong>Arrival:</strong> ' . $arrival . '</p>';
echo '<p><strong>Departure:</strong> ' . $departure . '</p>';
echo '<p><strong>Total price:</strong> $' . number_format($totalPrice, 2) . '</p>';
echo '<p><strong>Booking reference:</strong> ' . htmlspecialchars($transferCode) . '</p>';

if (!empty($featuresUsed)) {
    echo '<p><strong>Features:</strong> ' . htmlspecialchars(implode(', ', $featuresUsed)) . '</p>';
}
if ($discountPercent > 0) {
    echo '<p><strong>Loyalty discount:</strong> ' . $discountPercent . '%</p>';
}
