<?php

declare(strict_types=1);
require_once __DIR__ . '/backend/vendor/autoload.php';
require __DIR__ . '/frontend/header.php';

?>

<!-- Calendar -->
<?php require __DIR__ . '/frontend/calendar.php' ?>

<?php require __DIR__ . '/frontend/rooms.php' ?>

<!-- Booking form -->
<section class="booking-container">
    <form action="/backend/booking.php" method="POST">
        <label for="guest_name">Your name</label>
        <input type="text" name="guest_name" id="guest_name" required>

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

        <p class="features">Adventure</p>
        <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="marshmallow roasting">
            Marshmallow roasting over lava stream (Economy, $2)
        </label>
        <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="island trails">
            Access to marked island trails (Basic, $5)
        </label>
        <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="volcano tour">
            Volcano tour with expert guide (Basic, $8)
        </label>
        <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="skydiving">
            Skydiving over volcano (Basic, $10)
        </label>


        <!-- <p class="features">games</p>
        <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="yahtzee">
            yahtzee (Economy, $2)
        </label> -->


        <p class="features">water</p>
        <!-- <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="bathtub">
            bathtub (Economy, $2)
        </label> -->
        <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="pool">
            pool (Basic, $5)
        </label>
        <!-- <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="waterpark with fire and minibar">
            waterpark with fire and minibar (Superior, $17)
        </label> -->


        <!-- <p class="features">wheels</p>
        <label class="block ml-2">
            <input class="mr-2" type="checkbox" name="features[]" value="trike">
            trike (Premium, $10)
        </label> -->


        <button type="submit">Submit</button>
    </form>
</section>


<?php require __DIR__ . '/frontend/footer.php' ?>

</html>