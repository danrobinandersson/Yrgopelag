<?php
session_start();

if (!isset($_SESSION['booking_confirmation'])) {
    header('Location: /');
    exit;
}

$data = $_SESSION['booking_confirmation'];
unset($_SESSION['booking_confirmation']); // prevent refresh reuse
?>

<h2>Booking confirmed</h2>

<p>Thank you, <?= htmlspecialchars($data['guestName']) ?>!</p>
<p><strong>Arrival:</strong> <?= htmlspecialchars($data['arrival']) ?></p>
<p><strong>Departure:</strong> <?= htmlspecialchars($data['departure']) ?></p>
<p><strong>Total price:</strong> $<?= number_format($data['totalPrice'], 2) ?></p>
<p><strong>Booking reference:</strong> <?= htmlspecialchars($data['transferCode']) ?></p>

<?php if (!empty($data['features'])): ?>
    <p>
        <strong>Features:</strong>
        <?= htmlspecialchars(
            implode(', ', array_map(
                fn($f) => $f['activity'] . ' (' . $f['tier'] . ')',
                $data['features']
            ))
        ) ?>
    </p>
<?php endif; ?>

<?php if (!empty($data['package'])): ?>
    <p>
        <strong>Package discount applied:</strong>
        −$<?= number_format($data['discountAmount'], 2) ?>
        (<?= $data['package']['value'] ?>% off)
    </p>
<?php endif; ?>

<p>
    <a href="/?refresh=1">Back to main page</a>
</p>