<?php

namespace App\Support;

/** 1234567 -> "Twelve lakh thirty-four thousand five hundred sixty-seven rupees only". */
class IndianWords
{
    private const ONES = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
        'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];

    private const TENS = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

    public static function rupees($amount): string
    {
        $n = (int) round((float) $amount);
        if ($n === 0) {
            return 'Zero rupees only';
        }
        $words = self::number(abs($n));

        return ucfirst(($n < 0 ? 'minus ' : '').$words.' rupees only');
    }

    public static function number(int $n): string
    {
        $parts = [];
        foreach ([[10000000, 'crore'], [100000, 'lakh'], [1000, 'thousand'], [100, 'hundred']] as [$div, $name]) {
            if ($n >= $div) {
                $q = intdiv($n, $div);
                $parts[] = ($div === 10000000 ? self::number($q) : self::upto99($q)).' '.$name;
                $n %= $div;
            }
        }
        if ($n > 0) {
            $parts[] = self::upto99($n);
        }

        return implode(' ', $parts);
    }

    private static function upto99(int $n): string
    {
        if ($n < 20) {
            return self::ONES[$n];
        }

        return self::TENS[intdiv($n, 10)].($n % 10 ? '-'.self::ONES[$n % 10] : '');
    }
}
