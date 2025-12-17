<?php

if (isset($_POST['guestId'], $_POST['transfercode'], $_POST['arrival'], $_POST['departure'], $_POST['room'])) {

    $guestId = htmlspecialchars($_POST['guestId'] ?? '');
    $transfercode = htmlspecialchars($_POST['transfercode'] ?? '');
    $arrival = htmlspecialchars($_POST['arrival'] ?? '');
    $departure = htmlspecialchars($_POST['departure'] ?? '');
    $room = htmlspecialchars($_POST['room']);

    $featuresUsed = $_POST['features'] ?? [];

    $database = new PDO('sqlite:database/database.db');

    $statement = $database->query('SELECT * FROM rooms');
    $rooms = $statement->fetchAll(PDO::FETCH_ASSOC);

    var_dump($rooms);

    //     foreach ($rooms as $room) {
    //         var_dump($room);
    //     }
}
