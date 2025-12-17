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
            <label for="guestId">Your name</label>
            <input type="text" name="guestId" id="guestId" required>

            <label for="transfercode">Transfer-Code</label>
            <input type="text" name="transfercode" id="transfercode" required>

            <label for="arrival">Arrival</label>
            <input type="date" name="arrival" id="arrival" min="2026-01-01" max="2026-01-31">

            <label for="departure">Departure</label>
            <input type="date" name="departure" id="departure" min="2026-01-01" max="2026-01-31">

            <label for="room">Economy</label>
            <input type="radio" name="room" id="economy" value="economy">
            <label for="room">Standard</label>
            <input type="radio" name="room" id="standard" value="standard">
            <label for="room">Luxury</label>
            <input type="radio" name="room" id="luxury" value="luxury">

            <p class="features">cozy</p>
            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="bad book">
                bad book (Economy, $2)
            </label>
            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="good book">
                good book (Basic, $5)
            </label>


            <p class="features">games</p>
            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="yahtzee">
                yahtzee (Economy, $2)
            </label>


            <p class="features">water</p>
            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="bathtub">
                bathtub (Economy, $2)
            </label>
            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="pool">
                pool (Basic, $5)
            </label>
            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="waterpark with fire and minibar">
                waterpark with fire and minibar (Superior, $17)
            </label>


            <p class="features">wheels</p>
            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="trike">
                trike (Premium, $10)
            </label>


            <button type="submit">Submit</button>
        </form>
    </section>




    <?php require __DIR__ . '/frontend/footer.php' ?>

    </html>