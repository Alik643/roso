<?php

namespace App\Services;

use Carbon\Carbon;

class EventService
{
    public function calculateDate($eventDate)
    {
        $now = Carbon::now();

        if ($eventDate->diffInYears($now) >= 1) {
            $periodType = 'год';
            $period = $eventDate->diffInYears($now);
        } elseif ($eventDate->diffInMonths($now) >= 1) {
            $period = $eventDate->diffInMonths($now);
            $periodType = 'месяц';
        } elseif ($eventDate->diffInWeeks($now) >= 1) {
            $periodType = 'неделя';
            $period = $eventDate->diffInWeeks($now);
        } else {
            $periodType = 'день';
            $period = $eventDate->diffInDays($now);
        }
        if ($eventDate->gt($now)) {
            $period = $period * (-1);
        }

        return ['period' => $period, 'period_type' => $periodType];
    }
}
