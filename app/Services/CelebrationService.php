<?php

namespace App\Services;

use App\Models\User;
use App\Support\PeopleFormat;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/** Birthdays and work anniversaries of active people, and what goes on their post. */
class CelebrationService
{
    public const DESIGNS = [
        'birthday' => [4 => 'Keystone', 1 => 'Navy & gold', 2 => 'Confetti', 3 => 'Gold banner'],
        'anniversary' => [4 => 'Keystone', 1 => 'Big number', 2 => 'Thank you', 3 => 'Badge'],
    ];

    /** Birthdays and anniversaries from today to $days ahead, soonest first. */
    public static function upcoming(int $days = 30): Collection
    {
        $today = now()->startOfDay();
        $until = $today->copy()->addDays($days);
        $out = collect();
        $people = User::with(['designation', 'businessUnit'])->where('status', 'Active')
            ->where(fn ($q) => $q->whereNotNull('dob')->orWhereNotNull('joining_date'))->get();

        foreach ($people as $u) {
            foreach (['birthday' => $u->dob, 'anniversary' => $u->joining_date] as $type => $raw) {
                if (! $raw) {
                    continue;
                }
                try {
                    $from = Carbon::parse($raw)->startOfDay();
                } catch (\Throwable $e) {
                    continue;
                }
                $next = self::nextOccurrence($from, $today);
                if ($next->gt($until)) {
                    continue;
                }
                $years = $next->year - $from->year;
                if ($type === 'anniversary' && $years < 1) {
                    continue; // joined this year
                }
                $out->push([
                    'type' => $type, 'user' => $u, 'date' => $next,
                    'years' => $type === 'anniversary' ? $years : null,
                    'days' => (int) $today->diffInDays($next),
                ]);
            }
        }

        return $out->sortBy(fn ($r) => $r['date']->format('Ymd').($r['type'] === 'birthday' ? 0 : 1).$r['user']->name)->values();
    }

    /** This year's date (or next year's if it has passed). 29 Feb falls on 28 Feb in other years. */
    public static function nextOccurrence(Carbon $from, Carbon $today): Carbon
    {
        foreach ([$today->year, $today->year + 1] as $y) {
            $day = ($from->month === 2 && $from->day === 29 && ! Carbon::create($y)->isLeapYear()) ? 28 : $from->day;
            $d = Carbon::create($y, $from->month, $day)->startOfDay();
            if ($d->gte($today)) {
                return $d;
            }
        }

        return $today;
    }

    /** 1 -> "st", 2 -> "nd", 3 -> "rd", 11 -> "th" ... */
    public static function suffix(?int $n): string
    {
        $n = (int) $n;
        if (in_array($n % 100, [11, 12, 13], true)) {
            return 'th';
        }

        return [1 => 'st', 2 => 'nd', 3 => 'rd'][$n % 10] ?? 'th';
    }

    public static function yearsWord(?int $n): string
    {
        return $n.' '.($n === 1 ? 'year' : 'years');
    }

    public static function defaultMessage(string $type, User $u, ?int $years): string
    {
        return $type === 'birthday'
            ? 'May your day be filled with happiness, good health and new opportunities.'
            : 'Thank you for your dedication, hard work and valuable contributions to the team.';
    }

    /** "zoom,x,y" -> [zoom 1-4, x 0-100, y 0-100]; centred and filling the circle by default. */
    public static function photoPos(?string $pos): array
    {
        $p = array_map('floatval', explode(',', (string) $pos));
        if (count($p) !== 3) {
            return [1.0, 50.0, 50.0];
        }

        return [max(1, min(4, $p[0])), max(0, min(100, $p[1])), max(0, min(100, $p[2]))];
    }

    /** Background size/position that fills the circle (like "cover") at zoom 1. Percentages, so it fits every design. */
    public static function photoCss(float $aspect, float $z, float $x, float $y, float $box = 1): string
    {
        // $aspect = photo width / height, $box = frame width / height.
        $w = $aspect >= $box ? 100 * $z * $aspect / $box : 100 * $z;
        $h = $aspect >= $box ? 100 * $z : 100 * $z * $box / $aspect;

        return 'background-size:'.round($w, 2).'% '.round($h, 2).'%;background-position:'.round($x, 2).'% '.round($y, 2).'%;';
    }

    public static function isKeystone(string $company): bool
    {
        return (bool) preg_match('/keystone\s+real\s+estate|^keystone$|^ksrea?$/i', trim($company)) || trim($company) === '';
    }

    /** Everything the card needs. */
    public static function card(string $type, int $design, User $u, ?int $years, ?string $message, ?Carbon $date = null, ?string $postPhoto = null, ?string $photoPos = null): array
    {
        $u->loadMissing(['designation', 'businessUnit']);
        $profile = $u->photo && is_file(public_path('storage/'.$u->photo)) ? asset('storage/'.$u->photo) : null;
        $own = $postPhoto && is_file(public_path('storage/'.$postPhoto)) ? asset('storage/'.$postPhoto) : null;
        $photo = $own ?: $profile;
        $file = $own ? $postPhoto : ($profile ? $u->photo : null);
        $size = $file ? @getimagesize(public_path('storage/'.$file)) : false;
        $aspect = $size && $size[1] > 0 ? round($size[0] / $size[1], 4) : 1;
        [$z, $x, $y] = self::photoPos($photoPos);
        $pSize = $profile ? @getimagesize(public_path('storage/'.$u->photo)) : false;
        $profileAspect = $pSize && $pSize[1] > 0 ? round($pSize[0] / $pSize[1], 4) : 1;

        return [
            'type' => $type,
            'design' => isset(self::DESIGNS[$type][$design]) ? $design : 4,
            'name' => trim(implode(' ', array_filter([$u->first_name, $u->last_name]))) ?: (string) $u->name,
            'designation' => (string) ($u->designation?->name ?? ''),
            'photo' => $photo,
            'profile_photo' => $profile,
            'own_photo' => $own,
            'aspect' => $aspect, 'profile_aspect' => $profileAspect, 'zoom' => $z, 'px' => $x, 'py' => $y,
            'photo_css' => self::photoCss($aspect, $z, $x, $y),
            'initials' => PeopleFormat::initials($u->name),
            'years' => $years,
            'message' => $message ?? self::defaultMessage($type, $u, $years),
            'company' => $company = (string) ($u->entity ?: ($u->businessUnit?->name ?? config('app.name'))),
            // Keystone Real Estate Advisory logo; other companies show their name in its place.
            'logo_dark' => self::isKeystone($company) ? asset('assets/celebrations/keystone-logo-dark.png') : null,
            'logo_light' => self::isKeystone($company) ? asset('assets/celebrations/keystone-logo-light.png') : null,
            'suffix' => self::suffix($years),
            'date' => ($date ?? now())->format('d F'),
            'file' => \Illuminate\Support\Str::slug(($type === 'birthday' ? 'birthday-' : 'work-anniversary-').$u->name),
        ];
    }
}
