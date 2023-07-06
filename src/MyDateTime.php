<?php

namespace Project\Utils;

use Carbon\Carbon;

/**
 * Class MyDateTime
 *
 * This class provides helper methods for datetime manipulation and formatting.
 *
 * @package Project\Utils
 */
class MyDateTime
{
    /**
     * Format a date according to the given format
     * 
     * @param string $date | "01/01/2023" > "2023-01-01"
     * @param string $format | "d/m/Y" > "Y-m-d"
     */
    public static function formatDate($date, $format = 'Y-m-d')
    {
        return Carbon::parse($date)->format($format);
    }

    /**
     * Returns the difference in months and days between the current date and a date in the past
     *
     * @param string $startDate | "Y-m-d"
     * @param string|null $endDate | "Y-m-d"
     * @return array
     */
    public static function diffInYearsMonthsDays(string $startDate, string | null $endDate = null)
    {
        $start = Carbon::parse($startDate);
        $end   = !$endDate ? $start : Carbon::parse($endDate);
        $diff  = $start->diff($endDate);
        $months = 0;
        if ($diff->m > 0) {
            $months += $diff->m;
        }
        $days = $diff->d;
        if ($diff->y > 0) {
            $months += ($diff->y * 12);
        }
        $weeks = ($diff->format('%a') / 7);
        $weeks = $weeks < 1 ? 0 : $weeks;
        return [
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'years'         => $diff->format('%y'),
            'months'        => $months,
            'weeks'         => $weeks,
            'days'          => $days,
            'hours'         => $diff->format('%h'),
            'minutes'       => $diff->format('%i'),
            'seconds'       => $diff->format('%s'),
            "total_days"    => $diff->format('%a'),
            'total_seconds' => $start->floatDiffInSeconds($end),
        ];
    }
}
