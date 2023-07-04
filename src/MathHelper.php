<?php

namespace Project\Utils;

/**
 * Class MathHelper
 *
 * This class provides helper methods for various mathematical calculations.
 *
 * @package Project\Utils
 */
class MathHelper
{
    /**
     * Calculates the percentage reached of a proposed goal
     *
     * @param float $goalToAchieve
     * @param float $totalAchieved
     */
    public static function getPercent(float $goalToAchieve, float $totalAchieved): float
    {
        if ($totalAchieved == 0) {
            return 0;
        }
        return 100 * ($totalAchieved / $goalToAchieve);
    }
}
