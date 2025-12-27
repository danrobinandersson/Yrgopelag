<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/centralBankClient.php';

use App\Services\CentralBankClient;

if (isset(
    $_POST['guest_name'],
    $_POST['transfercode'],
    $_POST['arrival'],
    $_POST['departure'],
    $_POST['room']
)) {

    // Connect to database
    try {
        $database = new PDO('sqlite:database/database.db');
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }

    // ROOM LOOKUP
    $roomTier = $_POST['room'];

    $stmt = $database->prepare('SELECT id, price_per_night FROM rooms WHERE tier = 
    :tier LIMIT 1');
    $stmt->execute([
        ':tier' => $roomTier
    ]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room === false) {
        echo 'Invalid room selected';
        exit;
    }

    $roomId = (int) $room['id'];
    $roomPrice = (float) $room['price_per_night'];

    // DATE VALIDATION
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];

    if ($arrival === '' || $departure === '') {
        echo 'You must select both arrival and departure dates';
        exit;
    }

    if ($arrival >= $departure) {
        echo 'Departure must be after arrival';
        exit;
    }

    // AVAILABILITY CHECK
    $stmt = $database->prepare(
        'SELECT COUNT(*) FROM bookings WHERE room_id = :room_id 
            AND arrival_date < :departure 
            AND departure_date > :arrival'
    );
    $stmt->execute([
        ':room_id'   => $roomId,
        ':arrival'   => $arrival,
        ':departure' => $departure
    ]);
    $overlapCount = (int) $stmt->fetchColumn();
    if ($overlapCount > 0) {
        echo 'Selected room is not available for these dates';
        exit;
    }

    // GUEST LOOKUP
    $guestName = trim($_POST['guest_name']);
    $stmt = $database->prepare('SELECT id, visits FROM guests WHERE 
    name = :name LIMIT 1');
    $stmt->execute([
        ':name' => $guestName
    ]);
    $guest = $stmt->fetch(PDO::FETCH_ASSOC);

    // INSERT / UPDATE GUEST
    if ($guest !== false) {
        $guestId = (int) $guest['id'];
        $newVisits = (int) $guest['visits'] + 1;
        $stmt = $database->prepare('UPDATE guests SET visits = :visits WHERE id = :id');
        $stmt->execute([
            ':visits' => $newVisits,
            ':id' => $guestId
        ]);
    } else {
        $stmt = $database->prepare('INSERT INTO guests (name, visits) VALUES (:name, 1)');
        $stmt->execute([
            ':name' => $guestName
        ]);
        $guestId = (int) $database->lastInsertId();
    }

    // PRICE CALCULATION
    $arrivalDate = new DateTime($arrival);
    $departureDate = new DateTime($departure);
    $nights = (int) $arrivalDate->diff($departureDate)->days;

    $roomTotal = $roomPrice * $nights;

    // FEATURES CALCULATION
    $featuresUsed = $_POST['features'] ?? [];
    $featureTotal = 0;
    $featureIds = [];

    if (!empty($featuresUsed)) {
        $placeholders = implode(',', array_fill(0, count($featuresUsed), '?'));
        $stmt = $database->prepare("SELECT id, price FROM features WHERE feature_name IN ($placeholders)");
        $stmt->execute($featuresUsed);
        $features = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($features as $feature) {
            $featureTotal += (float) $feature['price'];
            $featureIds[] = (int) $feature['id'];
        }
    }

    $totalPrice = $roomTotal + $featureTotal;

    $transferCode = trim($_POST['transfercode']);
    $centralBank = new CentralBankClient();
    $hotelOwner = 'Robin'; // replace with your hotel owner username

    // VALIDATE TRANSFER CODE
    if (!$centralBank->validateTransferCode($transferCode, (int)$totalPrice)) {
        echo 'Invalid or insufficient transfer code';
        exit;
    }

    // DEPOSIT MONEY
    if (!$centralBank->deposit($hotelOwner, $transferCode)) {
        echo 'Payment failed. Booking not completed.';
        exit;
    }

    // INSERT BOOKING
    $stmt = $database->prepare(
        'INSERT INTO bookings (guest_id, room_id, arrival_date, departure_date, total_price, transfercode)
         VALUES (:guest_id, :room_id, :arrival, :departure, :total_price, :transfercode)'
    );
    $stmt->execute([
        ':guest_id'    => $guestId,
        ':room_id'     => $roomId,
        ':arrival'     => $arrival,
        ':departure'   => $departure,
        ':total_price' => $totalPrice,
        ':transfercode' => $transferCode
    ]);
    $bookingId = (int)$database->lastInsertId();

    // INSERT FEATURES
    if (!empty($featureIds)) {
        $stmt = $database->prepare('INSERT INTO bookings_features (booking_id, feature_id) VALUES (:booking_id, :feature_id)');
        foreach ($featureIds as $featureId) {
            $stmt->execute([':booking_id' => $bookingId, ':feature_id' => $featureId]);
        }
    }

    // SEND RECEIPT
    $featureObjects = array_map(fn($f) => ['activity' => $f, 'tier' => ''], $featuresUsed);
    $centralBank->sendReceipt($hotelOwner, $guestName, $arrival, $departure, $featureObjects, 1);

    // BOOKING CONFIRMATION
    echo '<h2>Booking confirmed</h2>';
    echo '<p>Thank you, ' . htmlspecialchars($guestName) . '!</p>';
    echo '<p><strong>Arrival:</strong> ' . $arrival . '</p>';
    echo '<p><strong>Departure:</strong> ' . $departure . '</p>';
    echo '<p><strong>Total price:</strong> $' . number_format($totalPrice, 2) . '</p>';
    echo '<p><strong>Booking reference:</strong> ' . htmlspecialchars($transferCode) . '</p>';
    if (!empty($featuresUsed)) {
        echo '<p><strong>Features:</strong> ' . htmlspecialchars(implode(', ', $featuresUsed)) . '</p>';
    }
}
