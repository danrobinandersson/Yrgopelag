<?php

declare(strict_types=1);
require_once __DIR__ . '/backend/vendor/autoload.php';
require __DIR__ . '/frontend/header.php';

?>

<?php require __DIR__ . '/frontend/rooms.php' ?>

<?php require __DIR__ . '/frontend/feature-info.php' ?>

<!-- Package discount banner -->
<div class="discount-banner">
    <div class="discount-banner-content">
        <h1>Package offer: get 10% discount on your booking!</h1>
        <p>Our Standard tent + Pool feature + At least one adventure feature of your choice</p>
    </div>
</div>

<?php require __DIR__ . '/frontend/form.php' ?>

<?php require __DIR__ . '/frontend/footer.php' ?>

</html>