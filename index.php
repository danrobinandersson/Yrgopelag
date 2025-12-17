<?php

declare(strict_types=1);
require_once __DIR__ . '/backend/vendor/autoload.php';
require __DIR__ . '/frontend/header.php';

?>

<body>

    <h1>Yrgopelag</h1>

    <!-- Calendar -->

    <?php require __DIR__ . '/frontend/rooms.php' ?>

    <!-- Booking form -->
    <section class="booking-container">
        <form action="/backend/booking.php" method="POST">
            <label for="name">Your name</label>
            <input type="text" name="name" id="name" required>

            <label for="transfercode">Transfer-Code</label>
            <input type="text" name="transfercode" id="transfercode" required>

            <label for="arrival">Arrival</label>
            <input type="date" name="arrival" id="arrival" min="2026-01-01" max="2026-01-31">

            <label for="departure">Departure</label>
            <input type="date" name="departure" id="departure" min="2026-01-01" max="2026-01-31">

            <label for="rooms">Economy</label>
            <input type="radio" name="rooms" id="economy" value="economy">
            <label for="rooms">Standard</label>
            <input type="radio" name="rooms" id="standard" value="standard">
            <label for="rooms">Luxury</label>
            <input type="radio" name="rooms" id="luxury" value="luxury">

            <button type="submit">Submit</button>
        </form>
    </section>




    <?php require __DIR__ . '/frontend/footer.php' ?>

    </html>