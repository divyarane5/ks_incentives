<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;

/** Fill-ins for HR letter templates, and turning the plain text into simple HTML. */
class LetterText
{
    /** Fill-in => what it means (shown to HR next to the template editor). */
    public const FIELDS = [
        '{title}' => 'Mr. / Ms.', '{name}' => 'Full name', '{first_name}' => 'First name', '{employee_code}' => 'Employee code',
        '{designation}' => 'Designation', '{department}' => 'Department', '{location}' => 'Work location', '{manager}' => 'Reporting manager',
        '{company}' => 'Company', '{joining_date}' => 'Joining date', '{confirm_date}' => 'Confirmation date', '{leaving_date}' => 'Leaving date',
        '{probation_days}' => 'Probation days', '{notice_days}' => 'Notice days', '{annual_ctc}' => 'Annual CTC (₹)',
        '{annual_ctc_words}' => 'Annual CTC in words', '{monthly_ctc}' => 'Monthly CTC (₹)', '{salary_effective_from}' => 'Latest CTC from',
        '{address}' => 'Current address', '{today}' => "Today's date",
    ];

    public static function values(User $user): array
    {
        $user->loadMissing(['designation', 'department', 'location', 'businessUnit', 'reportingManager', 'latestSalaryHistory']);
        $date = fn ($v) => $v ? Carbon::parse($v)->format('d F Y') : '';
        $title = trim((string) $user->title);

        return [
            '{title}' => $title ?: (strcasecmp((string) $user->gender, 'female') === 0 ? 'Ms.' : 'Mr.'),
            '{name}' => trim(implode(' ', array_filter([$user->first_name, $user->middle_name, $user->last_name]))) ?: (string) $user->name,
            '{first_name}' => (string) ($user->first_name ?: $user->name),
            '{employee_code}' => (string) $user->employee_code,
            '{designation}' => (string) ($user->designation?->name ?? ''),
            '{department}' => (string) ($user->department?->name ?? ''),
            '{location}' => (string) ($user->location?->name ?? ''),
            '{manager}' => (string) ($user->reportingManager?->name ?? ''),
            '{company}' => (string) ($user->entity ?: ($user->businessUnit?->name ?? config('app.name'))),
            '{joining_date}' => $date($user->joining_date),
            '{confirm_date}' => $date($user->confirm_date),
            '{leaving_date}' => $date($user->leaving_date),
            '{probation_days}' => (string) ($user->probation_period_days ?? ''),
            '{notice_days}' => (string) ($user->notice_period_days ?? ''),
            '{annual_ctc}' => $user->annual_ctc ? PeopleFormat::inr($user->annual_ctc) : '',
            '{annual_ctc_words}' => $user->annual_ctc ? IndianWords::rupees($user->annual_ctc) : '',
            '{monthly_ctc}' => $user->current_ctc ? PeopleFormat::inr($user->current_ctc) : '',
            '{salary_effective_from}' => $date($user->latestSalaryHistory?->effective_from),
            '{address}' => (string) ($user->communication_address ?? ''),
            '{today}' => now()->format('d F Y'),
        ];
    }

    public static function fill(?string $text, User $user): string
    {
        return strtr((string) $text, self::values($user));
    }

    /** Fill-ins left empty for this person (so HR can fix the profile first). */
    public static function blanks(?string $text, User $user): array
    {
        $out = [];
        foreach (self::values($user) as $k => $v) {
            if ($v === '' && str_contains((string) $text, $k)) {
                $out[] = self::FIELDS[$k] ?? $k;
            }
        }

        return $out;
    }

    /** Plain text -> HTML: blank line = new paragraph, "- " = bullet, **bold**. */
    public static function html(?string $text): string
    {
        $blocks = preg_split("/\R{2,}/", trim((string) $text));
        $html = '';
        foreach ($blocks as $block) {
            $lines = preg_split("/\R/", $block);
            $isList = count(array_filter($lines, fn ($l) => preg_match('/^\s*[-•]\s+/', $l))) === count($lines);
            $fmt = fn ($s) => preg_replace('/\*\*(.+?)\*\*/s', '<b>$1</b>', e($s));
            if ($isList) {
                $html .= '<ul>'.implode('', array_map(fn ($l) => '<li>'.$fmt(preg_replace('/^\s*[-•]\s+/', '', $l)).'</li>', $lines)).'</ul>';
            } else {
                $html .= '<p>'.implode('<br>', array_map($fmt, $lines)).'</p>';
            }
        }

        return $html;
    }
}
