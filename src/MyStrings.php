<?php

namespace Project\Utils;

/**
 * Class MyStrings
 *
 * This class provides helper methods for strings manipulation and formatting.
 *
 * @package Project\Utils
 */
class MyStrings
{
    public static function capitalize($string)
    {
        return ucwords($string);
    }
}
