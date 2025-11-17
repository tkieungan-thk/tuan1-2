<?php

namespace App\Helpers;

class PriceHelper
{
    /**
     * Format price với currency
     *
     * @param float $price
     * @param string $currency
     * @param string $decimalSeparator
     * @param string $thousandsSeparator
     * @return string
     */
    public static function format(float $price, string $currency = '₫', string $decimalSeparator = ',', string $thousandsSeparator = '.'): string
    {
        return number_format($price, 0, $decimalSeparator, $thousandsSeparator) . ' ' . $currency;
    }

    /**
     * Format price với VND
     *
     * @param float $price
     * @return string
     */
    public static function formatVND(float $price): string
    {
        return self::format($price, '₫');
    }
}
