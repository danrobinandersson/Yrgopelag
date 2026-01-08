<?php

declare(strict_types=1);

$database = new PDO('sqlite:' . __DIR__ . '/database/database.db');
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*
 Updates the calendar to show to the user which dates are already booked.
 Result format:
[
  room_id => [1, 2, 5, 6, 10],
  room_id => [...]
]
*/

$bookedDays = [];

$stmt = $database->query(
  'SELECT room_id, arrival_date, departure_date
     FROM bookings
     WHERE arrival_date <= "2026-01-31"
       AND departure_date >= "2026-01-01"'
);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
  $roomId = (int) $row['room_id'];

  $arrival = new DateTime($row['arrival_date']);
  $departure = new DateTime($row['departure_date']);

  // Loop day-by-day (checkout day excluded)
  while ($arrival < $departure) {
    $day = (int) $arrival->format('j'); // day of month (1–31)
    $bookedDays[$roomId][] = $day;
    $arrival->modify('+1 day');
  }
}

return $bookedDays;
