<!-- Booking form -->
<section class="forms-layout">

    <section class="booking-container">
        <form action="/backend/booking.php" method="POST">
            <div class="booking_info">
                <h2>Make a booking</h2>
                <label for="guest_name">Name</label>
                <input type="text" name="guest_name" id="guest_name" required>

                <label for="transfercode">Transfer-Code</label>
                <input type="text" name="transfercode" id="transfercode" required>

                <label for="arrival">Arrival</label>
                <input type="date" name="arrival" id="arrival" min="2026-01-01" max="2026-01-31">

                <label for="departure">Departure</label>
                <input type="date" name="departure" id="departure" min="2026-01-01" max="2026-01-31">
                <div class="room-options">
                    <label for="room">Economy</label>
                    <input type="radio" name="room" id="economy" value="economy">
                    <label for="room">Standard</label>
                    <input type="radio" name="room" id="standard" value="standard">
                    <label for="room">Luxury</label>
                    <input type="radio" name="room" id="luxury" value="luxury">
                </div>

                <div class="booking_features">
                    <h3 class="features">Adventure</h3>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="13">
                        Marshmallow roasting over lava stream (Economy, $2)
                    </label>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="14">
                        Access to marked island trails (Basic, $5)
                    </label>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="15">
                        Volcano tour with expert guide (Premium, $8)
                    </label>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="16">
                        Skydiving over volcano (Superior, $10)
                    </label>

                    <h3 class="features">Water</h3>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="pool">
                        pool (Basic, $5)
                    </label>

                    <button type="submit">Submit</button>
        </form>
        </div>
    </section>

    <div class="transfer-container">

        <form action="/backend/create-transfercode.php" method="POST">
            <h2>Create transfer code</h2>
            <label for="user">Username</label>
            <input type="text" name="user" id="user" required>

            <label for="api_key">API Key</label>
            <input type="text" name="api_key" id="api_key" required>

            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" min="1" required>

            <button type="submit">Create transfer code</button>
        </form>
    </div>

</section>