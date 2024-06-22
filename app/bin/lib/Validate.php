<?php

include(__DIR__ . "/StringUtils.php");

/**
 * Validate card struct
 */
class Validate
{

    private static bool $isGen = false;
    public static array $CardsAllowed = [3, 4, 5, 6];

    /**
     * Establecer si se está validando un input para generar
     */
    public static function SetGen(bool $isGen)
    {
        self::$isGen = $isGen;
    }

    /**
     * Luhn Checket algorithm
     */
    public static function luhn(string $str): bool
    {
        $sum = 0;
        $alt = false;
        for ($i = strlen($str) - 1; $i >= 0; --$i) {
            $digit = intval($str[$i]);
            if ($alt) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = $digit % 10 + 1;
                }
            }
            $sum += $digit;
            $alt = !$alt;
        }
        return $sum % 10 == 0;
    }

    /**
     * Validar el mes y año
     */
    public static function ValidateExpired(?string $month = null, ?string $year = null): array
    {
        $month = StringUtils::RemoveStrings($month);
        $year = StringUtils::RemoveStrings($year);

        if (empty($year) && empty($month) && self::$isGen) return ['ok' => true];

        $year = (strlen($year) == 2) ? '20' . $year : $year;
        $month = (strlen($month) == 1) ? '0' . $month : $month;

        if (!empty($month) && ($month < 1 || $month > 12)) {
            return ['ok' => false, 'msg' => 'Invalid month: ' . $month]; // Invalid month
        } elseif (!empty($year) && $year < date('Y')) {
            return ['ok' => false, 'msg' => 'Expired Year: ' . $year]; // Expired year
        } elseif (!empty($year) && $year > (date('Y') + 15)) {
            return ['ok' => false, 'msg' => 'Invalid Year: ' . $year]; // Invalid year
        } elseif (!empty($year) && $year == date('Y')) {
            if (!empty($month) && $month < date('m')) return ['ok' => false, 'msg' => 'Expired Month: ' . $month . '|' . $year]; // Expired month
        }

        return ['ok' => true, 'msg' => ''];
    }

    /**
     * Validar el cvv
     */
    public static function Cvv(?string $cvv = null, string $prefix = null)
    {

        if (empty($cvv) && self::$isGen) {
            return ['ok' => true];
        }

        $lenght = ($prefix[0] == 3) ? 4 : 3;
        if (strlen($cvv) != $lenght) {
            return ['ok' => false, 'msg' => 'Invalid CVV: ' . $cvv];
        }

        return ['ok' => true, 'msg' => ''];
    }

    /**
     * Validar la longitud de un cc
     */
    public static function CardLenght(string $card = null)
    {
        $lenght = strlen($card);
        $a = ($card[0] == 3) ? 15 : 16;

        if (self::$isGen) {
            if ($lenght >= 6 && $lenght < $a) {
                return ['ok' => true];
            } if ($lenght > $a) {
                return ['ok' => false, 'msg' => 'Too large'];
            }

        }
        if ($lenght != $a) {
            return ['ok' => false, 'msg' => 'Invalid card lenght: ' . $card . '(' . $lenght . ')'];
        }

        return ['ok' => true, 'msg' => ''];
    }

    /**
     * Validar que empiecen con 3,4,5, 6
     */
    public static function CardType(string $card): bool
    {
        return in_array($card[0], self::$CardsAllowed);
    }

}