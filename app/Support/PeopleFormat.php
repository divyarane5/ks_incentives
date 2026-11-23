<?php

namespace App\Support;

use Carbon\Carbon;

/** Small display helpers for the KeyPeople employee pages. */
class PeopleFormat
{
    /** Indian grouping: 1234567.5 -> ₹ 12,34,568 (or with paise when $decimals = 2). */
    public static function inr($amount, int $decimals = 0, bool $symbol = true): ?string
    {
        if ($amount === null || $amount === '') {
            return null;
        }
        $amount = (float) $amount;
        $neg = $amount < 0;
        $parts = explode('.', number_format(abs($amount), $decimals, '.', ''));
        $int = $parts[0];
        if (strlen($int) > 3) {
            $last3 = substr($int, -3);
            $rest = substr($int, 0, -3);
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $int = $rest.','.$last3;
        }
        $out = $int.(isset($parts[1]) ? '.'.$parts[1] : '');

        return ($neg ? '-' : '').($symbol ? '₹ ' : '').$out;
    }

    public static function date($value, string $format = 'd M Y'): ?string
    {
        if (! $value) {
            return null;
        }
        try {
            return Carbon::parse($value)->format($format);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    /** "2 yrs 3 mos" between two dates (end defaults to today). */
    public static function tenure($from, $to = null): ?string
    {
        if (! $from) {
            return null;
        }
        try {
            $diff = Carbon::parse($from)->diff($to ? Carbon::parse($to) : now());
        } catch (\Throwable $e) {
            return null;
        }
        $bits = [];
        if ($diff->y) {
            $bits[] = $diff->y.' yr'.($diff->y > 1 ? 's' : '');
        }
        if ($diff->m) {
            $bits[] = $diff->m.' mo'.($diff->m > 1 ? 's' : '');
        }

        return $bits ? implode(' ', $bits) : ($diff->d.' days');
    }

    /** Show only the last 4 characters: XXXX XXXX 1234. */
    public static function mask(?string $value, int $keep = 4): ?string
    {
        if (! $value) {
            return null;
        }
        $clean = preg_replace('/\s+/', '', $value);
        if (strlen($clean) <= $keep) {
            return $clean;
        }

        return str_repeat('•', min(8, strlen($clean) - $keep)).' '.substr($clean, -$keep);
    }

    /** Phone in international form for tel:/WhatsApp links: 9876543210 -> 919876543210. */
    public static function intlPhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === '') {
            return null;
        }
        $digits = ltrim($digits, '0');
        if (strlen($digits) === 10) {
            $digits = '91'.$digits;
        }

        return $digits;
    }

    public static function initials(?string $name): string
    {
        $words = preg_split('/\s+/', trim((string) $name)) ?: [];
        $words = array_values(array_filter($words, fn ($w) => ! in_array(strtolower(rtrim($w, '.')), ['mr', 'mrs', 'ms', 'miss', 'dr'], true)));
        $first = $words[0][0] ?? '';
        $last = count($words) > 1 ? ($words[count($words) - 1][0] ?? '') : '';

        return strtoupper($first.$last) ?: '?';
    }
}
