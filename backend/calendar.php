<?php

declare(strict_types=1);

function renderCalendar(int $roomId, array $bookedDays): void
{
    $roomBookings = $bookedDays[$roomId] ?? [];

    for ($day = 1; $day <= 31; $day++) {
        $class = 'day';

        if (in_array($day, $roomBookings, true)) {
            $class .= ' booked';
        }

        echo "<div class=\"$class\">$day</div>";
    }
}
