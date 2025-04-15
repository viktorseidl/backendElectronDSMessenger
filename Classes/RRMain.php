<?php
//Helper Function
function dayToNumber($day) {
    $days = [
        'SU' => 0,
        'MO' => 1,
        'TU' => 2,
        'WE' => 3,
        'TH' => 4,
        'FR' => 5,
        'SA' => 6
    ];
    return $days[$day] ?? null;
}
//MAIN FUNCTION
function generateRecurrences($startTime, $freq, $interval = 1, $byday = null, $until = null, $count = null) {
    $occurrences = [];
    $start = new DateTime($startTime);
    $current = clone $start;
    $total = 0;
    $max = $count ?? PHP_INT_MAX;

    $untilDate = $until ? new DateTime($until) : null;
    $bydays = $byday ? explode(',', $byday) : [];

    while ($total < $max) {
        if ($untilDate && $current > $untilDate) break;

        if ($freq === 'DAILY') {
            $occurrences[] = $current->format('Y-m-d H:i:s');
            $current->modify("+{$interval} day");

        } elseif ($freq === 'WEEKLY') {
            // Generate the week range
            $weekStart = clone $current;
            foreach ($bydays as $day) {
                $target = clone $weekStart;
                $target->modify("this week " . strtoupper($day));
                if ($target < $start) continue;
                if ($untilDate && $target > $untilDate) continue;
                if ($total >= $max) break;
                $occurrences[] = $target->format('Y-m-d H:i:s');
                $total++;
            }
            $current->modify("+{$interval} week");

        } elseif ($freq === 'MONTHLY') {
            $occurrences[] = $current->format('Y-m-d H:i:s');
            $current->modify("+{$interval} month");
        }

        if ($freq !== 'WEEKLY') $total++;
    }

    return $occurrences;
}
?>