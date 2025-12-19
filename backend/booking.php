<?php

declare(strict_types=1);

if (!isset(
    $_POST['guest_name'],
    $_POST['transfercode'],
    $_POST['arrival'],
    $_POST['departure'],
    $_POST['room']
)) {
    throw new Exception('Missing required booking data');
}

// Connect to database
try {
    $database = new PDO('sqlite:database/database.db');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}

$guestName = trim($_POST['guest_name']);

if ($guestName === '') {
    throw new Exception('Guest name is required');
}

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
