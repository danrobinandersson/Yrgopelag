<?php
if (isset($_POST['name'], $_POST['transfercode'])) {
    $guest_name = htmlspecialchars($_POST['name']);
    $transfercode = htmlspecialchars($_POST['transfercode']);
};

var_dump($_POST);

// $database = new PDO('backend/database/database.db');
