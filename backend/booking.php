<?php

declare(strict_types=1);

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
    }

    // ROOM LOOKUP
    $roomTier = $_POST['room'];

    $query = 'SELECT id FROM rooms WHERE tier = :tier LIMIT 1';
    $stmt = $database->prepare($query);
    $stmt->bindParam(':tier', $roomTier, PDO::PARAM_STR);
    $stmt->execute();

    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room === false) {
        echo 'Invalid room selected';
        exit;
    }

    $roomId = (int) $room['id'];

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
    $query = ' SELECT COUNT(*) FROM bookings
      WHERE room_id = :room_id
      AND arrival_date < :departure
      AND departure_date > :arrival
';

    $stmt = $database->prepare($query);
    $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $stmt->bindParam(':arrival', $arrival);
    $stmt->bindParam(':departure', $departure);
    $stmt->execute();

    $overlapCount = (int) $stmt->fetchColumn();

    if ($overlapCount > 0) {
        echo 'Selected room is not available for these dates';
        exit;
    }

    $guestName = trim($_POST['guest_name']);

    // Check if guest exists
    $query = 'SELECT id, visits FROM guests WHERE name = :name LIMIT 1';
    $stmt = $database->prepare($query);
    $stmt->bindParam(':name', $guestName, PDO::PARAM_STR);
    $stmt->execute();

    $guest = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update or insert guest
    if ($guest !== false) {
        // Existing guest
        $guestId = (int) $guest['id'];
        $newVisits = (int) $guest['visits'] + 1;

        $update = 'UPDATE guests SET visits = :visits WHERE id = :id';
        $updateStmt = $database->prepare($update);
        $updateStmt->bindParam(':visits', $newVisits, PDO::PARAM_INT);
        $updateStmt->bindParam(':id', $guestId, PDO::PARAM_INT);
        $updateStmt->execute();
    } else {
        // New guest
        $insert = 'INSERT INTO guests (name, visits) VALUES (:name, 1)';
        $insertStmt = $database->prepare($insert);
        $insertStmt->bindParam(':name', $guestName, PDO::PARAM_STR);
        $insertStmt->execute();

        $guestId = (int) $database->lastInsertId();
    }

    // $arrivalDate = new DateTime($arrival);
    // $departureDate = new DateTime($departure);

    // $nights = (int) $arrivalDate->diff($departureDate)->days;
}
