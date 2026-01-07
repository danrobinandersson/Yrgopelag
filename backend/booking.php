<?php

declare(strict_types=1);

session_start();

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

// PACKAGE DEAL DEFINITIONS
$packageDeals = [
    [
        'room' => 'standard',
        'features' => ['water', 'hotel-specific'],
        'discount_type' => 'percent',
        'value' => 10,
    ],
];

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

// PRICE CALCULATION

$nights = (int) (new DateTime($arrival))->diff(new DateTime($departure))->days;
$roomTotal = $roomPrice * $nights;

$featureIds = array_map('intval', $featuresUsed);

$featureTotal   = 0;
$featureObjects = [];

if (!empty($featureIds)) {
    $placeholders = implode(',', array_fill(0, count($featureIds), '?'));
    $stmt = $database->prepare(
        "SELECT id, category, tier, price
         FROM features
         WHERE id IN ($placeholders)"
    );
    $stmt->execute($featureIds);

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $feature) {
        $featureTotal += (float) $feature['price'];
        $featureObjects[] = [
            'activity' => $feature['category'],
            'tier'     => $feature['tier'],
        ];
    }
}

$totalPrice = $roomTotal + $featureTotal;

// PACKAGE DISCOUNT LOGIC

$discountAmount = 0;
$appliedPackage = null;

foreach ($packageDeals as $deal) {
    if ($deal['room'] !== $roomTier) {
        continue;
    }

    $bookedActivities = array_column($featureObjects, 'activity');

    $hasAllRequiredFeatures = true;
    foreach ($deal['features'] as $requiredFeature) {
        if (!in_array($requiredFeature, $bookedActivities, true)) {
            $hasAllRequiredFeatures = false;
            break;
        }
    }

    if (!$hasAllRequiredFeatures) {
        continue;
    }

    if ($deal['discount_type'] === 'fixed') {
        $discountAmount = $deal['value'];
    } else {
        $discountAmount = ($totalPrice * $deal['value']) / 100;
    }

    $appliedPackage = $deal;
    break;
}

$totalPrice = max(0, $totalPrice - $discountAmount);

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
        'SELECT id FROM guests WHERE name = :name LIMIT 1'
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

// SESSION CONFIRMATION DATA

$_SESSION['booking_confirmation'] = [
    'guestName'      => $guestName,
    'arrival'        => $arrival,
    'departure'      => $departure,
    'totalPrice'     => $totalPrice,
    'transferCode'   => $transferCode,
    'features'       => $featureObjects,
    'package'        => $appliedPackage,
    'discountAmount' => $discountAmount,
];

header('Location: booking-confirmed.php');
exit;
