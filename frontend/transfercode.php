<h2>Create transfer code</h2>

<form action="/backend/create-transfercode.php" method="POST">

    <label for="user">Username</label>
    <input type="text" name="user" id="user" required>

    <label for="api_key">API Key</label>
    <input type="text" name="api_key" id="api_key" required>

    <label for="amount">Amount</label>
    <input type="number" name="amount" id="amount" min="1" required>

    <button type="submit">Create transfer code</button>

</form>