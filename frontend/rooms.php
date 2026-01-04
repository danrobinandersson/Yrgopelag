<?php
$bookedDays = require __DIR__ . '/../backend/availability.php';
require __DIR__ . '/../backend/calendar.php';
?>
<section class="room_section">

    <h2>Our Tents</h2>

    <div class="rooms_container">
        <div class="room_card">
            <h3 class="price">Price: $4</h3>
            <img src="resources/Images/Economy-tent.png" alt="Economy tent">
            <p>Tent description</p>
            <section class="calendar">
                <?php renderCalendar(1, $bookedDays); ?>
            </section>

        </div>
        <div class="room_card">
            <h3 class="price">Price: $6</h3>
            <img src="resources/Images/Standard-tent.png" alt="Standard tent">
            <p>Tent description</p>
            <section class="calendar">
                <?php renderCalendar(2, $bookedDays); ?>
            </section>
        </div>
        <div class="room_card">
            <h3 class="price">Price: $8</h3>
            <img src="resources/Images/Luxury tent.png" alt="Luxury tent">
            <p>Tent description</p>
            <section class="calendar">
                <?php renderCalendar(3, $bookedDays); ?>
        </div>
    </div>

</section>
<br>
<br>