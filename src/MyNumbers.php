<?php

namespace Project\Utils;

/**
 * Class MyNumbers
 *
 * This class provides helper methods for numbers manipulation and formatting.
 *
 * @package Project\Utils
 */
class MyNumbers
{
    private $locale;

    /**
     * MyNumbers constructor.
     *
     * @param string $locale The locale to use for formatting (default: en_US | portuguese-brazilian | pt_BR).
     */
    public function __construct(string $locale = 'en_US')
    {
        // Set the desired locale for number formatting
        $this->locale = $locale;
        setlocale(LC_ALL, $this->locale);
    }

    /**
     * Format a number as a currency string.
     *
     * @param float  $number    The number to format as currency.
     * @param string $prefix    The prefix to add before the number (e.g., currency symbol).
     * @param string $suffix    The suffix to add after the number (e.g., percentage symbol).
     *
     * @return string The formatted currency string.
     */
    public function formatCurrency(float $number, string $prefix = '', string $suffix = ''): string
    {
        $formattedNumber = number_format($number, 2);

        // Format the number as currency using number_format()
        if ($this->locale == 'pt_BR' || $this->locale == 'Portuguese_Brazil.1252') {
            $formattedNumber = number_format($number, 2, ',', '.');
        }

        // Add prefix, currency symbol, and suffix to the formatted number
        $formattedCurrency = $prefix . $formattedNumber . $suffix;

        return $formattedCurrency;
    }

    /**
     * Format a number using abbreviated notation (e.g., 1K, 1M, 1B, etc.).
     *
     * @param float $number The number to format.
     * @return string The formatted abbreviated number string.
     */
    public static function formatNumberAbbreviated(float $number): string
    {
        if ($number >= 1e12) {
            return round($number / 1e12, 1) . 'T';
        } elseif ($number >= 1e9) {
            return round($number / 1e9, 1) . 'B';
        } elseif ($number >= 1e6) {
            return round($number / 1e6, 1) . 'M';
        } elseif ($number >= 1e3) {
            return round($number / 1e3, 1) . 'K';
        }

        return (string) $number;
    }

    /**
     * Convert a number to its word representation.
     *
     * @param float $number      The number to convert.
     * @param bool $bolDisplayCurrency  is it coin or not.
     * @param bool$bolFeminineWord shows how feminine
     *
     * @return string The word representation of the number.
     */
    public static function convertToWords($value = 0, $bolDisplayCurrency = true, $bolFeminineWord = false)
    {

        $value = self::removeNumberFormatting($value);

        $singular = null;
        $plural   = null;

        if ($bolDisplayCurrency) {
            $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural   = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
        } else {
            $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
            $plural   = array("", "", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
        }

        $c   = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d   = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezessete", "dezoito", "dezenove");
        $u   = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");

        if ($bolFeminineWord) {

            if ($value == 1) {
                $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
            } else {
                $u = array("", "um", "duas", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
            }

            $c = array("", "cem", "duzentas", "trezentas", "quatrocentas", "quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");

        }

        $z = 0;

        $value   = number_format($value, 2, ".", ".");
        $integer = explode(".", $value);

        for ($i = 0; $i < count($integer); $i++) {
            for ($ii = mb_strlen($integer[$i]); $ii < 3; $ii++) {
                $integer[$i] = "0" . $integer[$i];
            }
        }

        // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
        $rt  = null;
        $fim = count($integer) - ($integer[count($integer) - 1] > 0 ? 1 : 2);
        for ($i = 0; $i < count($integer); $i++) {
            $value = $integer[$i];
            $rc    = (($value > 100) && ($value < 200)) ? "cento" : $c[$value[0]];
            $rd    = ($value[1] < 2) ? "" : $d[$value[1]];
            $ru    = ($value > 0) ? (($value[1] == 1) ? $d10[$value[2]] : $u[$value[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = count($integer) - 1 - $i;
            $r .= $r ? " " . ($value > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($value == "000") {
                $z++;
            } elseif ($z > 0) {
                $z--;
            }

            if (($t == 1) && ($z > 0) && ($integer[0] > 0)) {
                $r .= (($z > 1) ? " de " : "") . $plural[$t];
            }

            if ($r) {
                $rt = $rt . ((($i > 0) && ($i <= $fim) && ($integer[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
            }

        }

        $rt = mb_substr($rt, 1);

        return ($rt ? trim($rt) : "zero");

    }

    /**
     * remove formatting from a number
     *
     * @param string  $strNumber
     */
    public static function removeNumberFormatting(string $strNumber)
    {

        $strNumber = trim(str_replace("R$", '', $strNumber));

        $vetComma = explode(",", $strNumber);
        if (count($vetComma) == 1) {
            $accents = array(".");
            $result  = str_replace($accents, "", $strNumber);
            return $result;
        } else if (count($vetComma) != 2) {
            return $strNumber;
        }

        $strNumber  = $vetComma[0];
        $strDecimal = mb_substr($vetComma[1], 0, 2);

        $accents = array(".");
        $result  = str_replace($accents, "", $strNumber);
        $result  = $result . "." . $strDecimal;

        return $result;

    }

    /**
     * Convert Bytes
     *
     * @param float $bytes
     * @param string $forceUnit
     * @param string $format
     * @param string $useSiPrefix
     */
    public static function convertNumbersToBytes(float $bytes, string $forceUnit = null, string $format = '%01.2f %s', bool $useSiPrefix = true)
    {
        $forceUnit = false;
        if (!is_null($forceUnit)) {
            $forceUnit = strpos($forceUnit, 'i');
        }
        if (!$useSiPrefix || $forceUnit) {
            $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
            $mod   = 1024;
        } else {
            $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
            $mod   = 1000;
        }

        if (($power = array_search((string) $forceUnit, $units)) === false) {
            $power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
        }
        return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
    }

    /**
     * Removes all special characters from
     * a numeric string, keeping only the numbers
     *
     * @param string $number
     * @return string
     */
    public static function onlyNumber(string $number = null)
    {
        return preg_replace('/[^0-9]/', '', $number);
    }

}
