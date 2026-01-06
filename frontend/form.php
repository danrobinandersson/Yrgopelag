<section class="section">
    <h2 class="section-title">Book your stay</h2>

    <div class="forms-container">

        <div class="form-column">
            <form action="/backend/booking.php" method="POST">
                <h2>Make a booking</h2>

                <div class="booking-info">
                    <label for="guest_name">Name</label>
                    <input type="text" name="guest_name" id="guest_name" required>

                    <label for="transfercode">Transfercode</label>
                    <input type="text" name="transfercode" id="transfercode" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" required>

                    <label for="arrival">Arrival</label>
                    <input type="date" name="arrival" id="arrival" min="2026-01-01" max="2026-01-31">

                    <label for="departure">Departure</label>
                    <input type="date" name="departure" id="departure" min="2026-01-01" max="2026-01-31">
                </div>

                <div class="booking-room-options">
                    <h3>Select tent type</h3>
                    <div class="form-room-options">
                        <div>
                            <input type="radio" name="room" id="economy" value="economy" data-price="4">
                            <label for="room">Economy $4</label>
                        </div>
                        <div>
                            <input type="radio" name="room" id="standard" value="standard" data-price="6">
                            <label for="room">Standard $6</label>
                        </div>
                        <div>
                            <input type="radio" name="room" id="luxury" value="luxury" data-price="8">
                            <label for="room">Luxury $8</label>
                        </div>
                    </div>
                </div>


                <div class="booking-features">
                    <h3>Select features</h3>
                    <h4 class="features">Adventure</h4>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="13" data-price="2">
                        Marshmallow roasting over lava stream (Economy, $2)
                    </label>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="14" data-price="5">
                        Access to marked island trails (Basic, $5)
                    </label>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="15" data-price="10">
                        Volcano tour with expert guide (Premium, $10)
                    </label>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="16" data-price="17">
                        Skydiving over volcano (Superior, $17)
                    </label>
                    <h3 class="features">Water</h3>
                    <label class="block ml-2">
                        <input class="mr-2" type="checkbox" name="features[]" value="1" data-price="2">
                        Pool (Basic, $2)
                </div>
                <button type="submit">Submit</button>
            </form>
        </div>

        <div class="form-column">
            <div class="transfer-container">
                <form action="/backend/create-transfercode.php" method="POST">
                    <h2>Create Transfercode</h2>

                    <label for="user">Username</label>
                    <input type="text" name="user" id="user" required>

                    <label for="api_key">API Key</label>
                    <input type="text" name="api_key" id="api_key" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" required>

                    <label for="amount">Amount</label>
                    <input type="number" name="amount" id="amount" min="1" required>

                    <button type="submit">Create transfercode</button>
                </form>
            </div>

            <div class="total-container">
                <h3>Price Summary</h3>
                <p>Tent: $<span id="room-price">0</span> × <span id="nights">0</span> nights</p>
                <p> Features: $<span id="features-price">0</span></p>
                <h2>Your Total: $<span id="total-price">0</span></h2>
            </div>

        </div>
    </div>
</section>