<?php

declare(strict_types=1);

if (isset(
    $_POST['guest_name'],
    $_POST['transfercode'],
    $_POST['arrival'],
    $_POST['departure'],
    $_POST['room']
)) {

    $guestName = trim($_POST['guest_name']);
    $transfercode = trim($_POST['transfercode']);
    $arrival = ($_POST['arrival']);
    $departure = ($_POST['departure']);
    $room = ($_POST['room']);

    $featuresUsed = $_POST['features'] ?? [];

    //database logic:
    try {
        $database = new PDO('sqlite:database/database.db');
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }


    $query = 'INSERT INTO guests (name, visits) VALUES (:name, :visits)';
    $statement = $database->prepare($query);

    $guestName = trim($_POST['guest_name']);
    $visits = 0;

    $statement->bindParam(':name', $guestName, PDO::PARAM_STR);
    $statement->bindParam(':visits', $visits, PDO::PARAM_INT);

    $statement->execute();

    $guestId = (int) $database->lastInsertId();
};

var_dump($guestId);
