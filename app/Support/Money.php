<?php

namespace App\Support;

class Money
{
    /** Indian short form: 4500000 -> ₹45 L, 12500000 -> ₹1.25 Cr. */
    public static function short(?int $amount): string
    {
        if (! $amount) {
            return '—';
        }
        if ($amount >= 10000000) {
            return '₹'.rtrim(rtrim(number_format($amount / 10000000, 2), '0'), '.').' Cr';
        }
        if ($amount >= 100000) {
            return '₹'.rtrim(rtrim(number_format($amount / 100000, 2), '0'), '.').' L';
        }

        return '₹'.number_format($amount);
    }
}
