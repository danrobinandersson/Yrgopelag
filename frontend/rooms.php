<?php
$bookedDays = require __DIR__ . '/../backend/availability.php';
require __DIR__ . '/../backend/calendar.php';
?>
<section class="section">
    <h2 class="section-title ">Our Tents</h2>

    <div class="rooms-container">
        <div class="room-card">
            <h3 class="room-title">Economy Tent ($4)</h3>
            <p class="room-info">Our most basic tent, don't let the bed bugs bite!</p>
            <img src="resources/Images/Economy-tent.png" alt="Economy tent">
            <p class="room-availability">Check availability for January 2026</p>
            <section class="calendar">
                <?php renderCalendar(1, $bookedDays); ?>
            </section>

        </div>
        <div class="room-card">
            <h3 class="room-title">Standard Tent ($6)</h3>
            <p class="room-info">A bit more comfy, and hey.. you even get a mosquito net!</p>
            <img src="resources/Images/Standard-tent.png" alt="Standard tent">
            <p class="room-availability">Check availability for January 2026</p>
            <section class="calendar">
                <?php renderCalendar(2, $bookedDays); ?>
            </section>
        </div>
        <div class="room-card">
            <h3 class="room-title">Luxury Tent ($8)</h3>
            <p class="room-info">Our most luxurious tent, enjoy your holiday in comfort and style.</p>
            <img src="resources/Images/Luxury tent.png" alt="Luxury tent">
            <p class="room-availability">Check availability for January 2026</p>
            <section class="calendar">
                <?php renderCalendar(3, $bookedDays); ?>
        </div>
    </div>

</section>
<br>
<br>