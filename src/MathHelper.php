<?php

namespace Project\Utils;

use Carbon\Carbon;
use GuzzleHttp\Client;

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

    /**
     * Calculate the future value of a value or reverse the value based on the future value
     *
     * @param float $vp - current value
     * @param float $tax - taxes or fees
     * @param bollean $reverse - If you want to calculate the value in reverse
     *
     */
    public static function calculateVf(float $vp, float $tax, bool $reverse = false): float
    {
        $vf = $vp * (1 + $tax);

        if ($reverse) {
            $vf = $vf / (1 + $tax);
        }

        return $vf;
    }

    /**
     * Calculate Inflation Adjusted Value
     *
     * @param string $startDate
     * @param string $endDate
     * @param float $originalValu
     */
    public static function calculateInflationAdjustedValue(string $startDate, string $endDate, float $originalValue): string|array
    {

        $cumulativeInflation = self::calculateCumulativeInflation($startDate);

        if(!$cumulativeInflation){
            return "Erro ao calcular inflação acumulada! Método calculateCumulativeInflation()";
        }

        $endValue            = $originalValue * (1 + ($cumulativeInflation / 100));
        return [
            "start_date"           => $startDate,
            "end_date"             => $endDate,
            "cumulative_inflation" => $cumulativeInflation,
            "end_value"            => $endValue,
        ];
    }

    /**
     * Calculate Cumulative Inflation
     *
     * @param $startDate
     */
    public static function calculateCumulativeInflation(string $startDate)
    {
        $endDate             = Carbon::now()->format('Y-m-d');
        $url                 = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.433/dados?dados?formato=json&dataInicial={$startDate}&dataFinal={$endDate}";
        $client              = new Client();
        $response            = $client->request('GET', $url);
        $status              = $response->getStatusCode();
        $body                = $response->getBody()->getContents();
        $accumulated         = json_decode($body, true);
        $monthlyInflation    = 0;
        $index               = 0;
        $cumulativeInflation = 0;

        if($status != 200){
            return false;
        }

        foreach ($accumulated as $fessMOnth) {

            $monthlyInflation = (float) $fessMOnth["valor"];

            if ($index == 0) {
                $cumulativeInflation = $monthlyInflation;
            } else {
                $cumulativeInflation = ((1 + ($monthlyInflation / 100)) * $cumulativeInflation) + $monthlyInflation;
            }
            $index++;
        }

        return $cumulativeInflation;
    }
}
