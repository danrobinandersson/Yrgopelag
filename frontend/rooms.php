<section class="room_section">

    <h2>Our Tents</h2>

    <div class="rooms_container">
        <div class="room_card">
            <img src="/resources/Images/Economy-tent.png" alt="Economy tent">
            <p>Tent description</p>
            <section class="calendar">
                <?php
                for ($i = 1; $i <= 31; $i++) :
                ?>
                    <div class="day"><?= $i; ?></div>
                <?php endfor; ?>
            </section>

        </div>
        <div class="room_card">
            <img src="/resources/Images/Standard-tent.png" alt="Standard tent">
            <p>Tent description</p>
            <section class="calendar">
                <?php
                for ($i = 1; $i <= 31; $i++) :
                ?>
                    <div class="day"><?= $i; ?></div>
                <?php endfor; ?>
            </section>
        </div>
        <div class="room_card">
            <img src="/resources/Images/Standard-tent.png" alt="Standard tent">
            <p>Tent description</p>
            <section class="calendar">
                <?php
                for ($i = 1; $i <= 31; $i++) :
                ?>
                    <div class="day"><?= $i; ?></div>
                <?php endfor; ?>
            </section>
        </div>
    </div>

</section>
<br>
<br>