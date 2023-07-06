<?php

namespace Project\Utils;

use DateTime;
use Project\Utils\MyNumbers;

class Validations
{

    /**
     * Validade date in formats Y-m-d or d/m/Y
     *
     * @param string $date
     */
    public static function validateDate($date): bool
    {
        // Verify  format "d/m/Y"
        if (self::isValidFormat($date, 'd/m/Y') && self::isDayValid($date, 'd/m/Y')) {
            return true;
        }

        // Verify  format "Y-m-d"
        if (self::isValidFormat($date, 'Y-m-d') && self::isDayValid($date, 'Y-m-d')) {
            return true;
        }

        return false;
    }

    /**
     * Validates that the password sent is within the mandatory parameters
     *
     * @param string $password
     */
    public static function validatePassword(string $password): bool
    {
        // Minimum of 6 characters
        if (strlen($password) < 6) {
            return false;
        }

        // At least one capital letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // At least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // At least one special character
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            return false;
        }

        // at least one number
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        // Password meets all restrictions
        return true;
    }

    /**
     * Federal document validation (CFP/CNPJ)
     *
     * This method validates a document number based on its type (CPF or CNPJ)
     *
     * @param string $document The document number to validate.

     */
    public static function validateDocument(string $document)
    {
        if (empty($document)) {
            return false;
        }

        // Extracts only the numbers from the document
        $document = MyNumbers::onlyNumber($document);

        if (strlen($document) == 14) {
            // ==================================
            // VALIDATE CNPJ
            // ==================================

            // Checks for repeated digits
            if (preg_match('/(\d)\1{13}/', $document)) {
                return false;
            }

            $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            // Calculates the first verification digit
            for ($i = 0, $n = 0; $i < 12; $n += $document[$i] * $b[++$i]);

            if ($document[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                return false;
            }

            // Calculates the second verification digit
            for ($i = 0, $n = 0; $i <= 12; $n += $document[$i] * $b[$i++]);

            if ($document[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                return false;
            }

            return true;

        } else {
            // ==================================
            // VALIDATE CPF
            // ==================================

            // Checks the length of the CPF
            if (strlen($document) != 11) {
                return false;
            }

            // Checks for repeated digits
            if (preg_match('/(\d)\1{10}/', $document)) {
                return false;
            }

            // Calculates the CPF validation digits
            for ($t = 9; $t < 11; $t++) {
                $sum = 0;
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $sum += $document[$c] * (($t + 1) - $c);
                }
                $sum = ((10 * $sum) % 11) % 10;
                if ($document[$c] != $sum) {
                    return false;
                }
            }

            return true;
        }

    }

    /**
     * Validate credit card
     *
     * @param string $creditCardNumber
     */
    public static function validateCreditCard($creditCardNumber)
    {
        // Remova espaços em branco e traços do número do cartão
        $creditCardNumber = str_replace([' ', '-'], '', $creditCardNumber);

        // Verifique se o número do cartão é um número válido
        if (!is_numeric($creditCardNumber)) {
            return false;
        }

        // Validação básica do número do cartão
        $cardLength = strlen($creditCardNumber);
        $parity     = $cardLength % 2;
        $sum        = 0;

        for ($i = 0; $i < $cardLength; $i++) {
            $digit = $creditCardNumber[$i];
            if ($i % 2 === $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return $sum % 10 === 0;
    }

    /**
     * Validates the zip code sent
     *
     * @param string $cep
     * @return void
     */
    public static function validateZipCode(string $cep)
    {
        $cep = MyNumbers::onlyNumber($cep);

        if (strlen($cep) == 8 && preg_match('/^[0-9]{5,5}([- ]?[0-9]{3,3})?$/', $cep)) {
            return true;
        }
        return false;
    }

    /**
     * Validate the email sent
     *
     * @param string $email
     * @return void
     */
    public static function validateEmail(string $email)
    {
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * Validate phone number with ddd
     * accepted formats
     *
     * 11965453789
     * (11)965453789
     * (11) 965453789
     * (11) 96545-3789
     *
     * @param string $phone
     * @return void
     */
    public static function validatePhone(string $phone)
    {
        if (preg_match("/\(?\d{2}\)?\s?\d{5}\-?\d{4}/", $phone)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if a string contains only letters.
     *
     * @param string $value
     */
    public static function validateAlpha(string $value): bool
    {
        if (preg_match('/^[a-zA-Z]+$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * Validates that a string contains only letters and numbers.
     *
     * @param string $value
     */
    public static function validateAlphaNumeric(string $value): bool
    {
        if (preg_match('/^[a-zA-Z0-9]+$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if a file has an allowed extension.
     *
     * @param string $filename | "document.pdf"
     * @param array $allowedExtensions | ["pdf", "doc", "docx"]
     */
    public static function validateFileExtension(string $filename, array $allowedExtensions): bool
    {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array($fileExtension, $allowedExtensions)) {
            return true;
        }

        return false;
    }

    /**
     * Validates the size of a file, checking that it is within acceptable limits.
     *
     * @param string $filename | "document.pdf"
     * @param float $maxFileSize | 1024
     */
    public static function validateFileSize(string $filename, float $maxFileSize): bool
    {
        $fileSize = filesize($filename);

        if ($fileSize <= $maxFileSize) {
            return true;
        }

        return false;
    }

    /**
     * Checks if an image has valid dimensions.
     *
     * @param string $filename | "image.jpg"
     * @param float $maxWidth | 800
     * @param float $maxHeight | 800
     */
    public static function validateImageDimensions(string $filename, float $maxWidth, float $maxHeight): bool
    {
        list($width, $height) = getimagesize($filename);

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return true;
        }

        return false;
    }

    /**
     * Checks whether a string represents a valid time.
     *
     * @param string $time | "image.jpg"
     */
    public static function validateTime(string $time): bool
    {
        $pattern = '/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/';

        if (preg_match($pattern, $time)) {
            return true;
        }

        return false;
    }

    private static function isValidFormat($date, $format)
    {
        $dateTime = DateTime::createFromFormat($format, $date);

        return $dateTime && $dateTime->format($format) === $date;
    }

    private static function isDayValid($date, $format)
    {
        $dateTime = DateTime::createFromFormat($format, $date);
        $day      = (int) $dateTime->format('d');
        $month    = (int) $dateTime->format('m');
        $year     = (int) $dateTime->format('Y');

        return checkdate($month, $day, $year);
    }
}
