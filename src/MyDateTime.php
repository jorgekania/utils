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
    public static function formatDate($date, $format = 'Y-m-d')
    {
        return Carbon::parse($date)->format($format);
    }
}
