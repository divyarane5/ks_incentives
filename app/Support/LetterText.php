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
        '{joining_date_long}' => 'Joining date (22nd October 2026)', '{annual_ctc_figure}' => 'Annual CTC without ₹ (3,84,000)',
        '{ctc_in_words}' => 'Annual CTC as "Rupees … Only"',
    ];

    /** Blocks HR can put on a line of their own. */
    public const BLOCKS = [
        '[[to]]' => 'Date, "To" name and subject here (instead of at the top)',
        '[[salary-table]]' => 'Salary structure table (monthly and yearly) from the profile',
        '[[company-sign]]' => 'Space for the company signature (image, or blank for the DSC token)',
        '[[page]]' => 'Start a new page',
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
            '{joining_date_long}' => $user->joining_date ? Carbon::parse($user->joining_date)->format('jS F Y') : '',
            '{annual_ctc_figure}' => $user->annual_ctc ? PeopleFormat::inr($user->annual_ctc, 0, false) : '',
            '{ctc_in_words}' => $user->annual_ctc ? 'Rupees '.ucwords(str_replace(' rupees only', '', strtolower(IndianWords::rupees($user->annual_ctc)))).' Only' : '',
        ];
    }

    public static function fill(?string $text, User $user): string
    {
        $text = strtr((string) $text, self::values($user));

        return str_replace('[[salary-table]]', self::salaryTable($user), $text);
    }

    /**
     * The salary structure as a "| a | b | c |" table (frozen into the letter, so HR can still edit it).
     * Monthly figures from the profile; yearly = 12 months (Maharashtra professional tax ₹200 a month = ₹2,500 a year).
     */
    public static function salaryTable(User $user): string
    {
        $n = fn ($v) => PeopleFormat::inr(round((float) $v), 0, false);
        $parts = [
            'Basic' => $user->monthly_basic, 'HRA' => $user->monthly_hra, 'Special Allowance' => $user->special_allowance,
            'Conveyance Allowance' => $user->conveyance_allowance, 'Medical Reimbursement' => $user->medical_reimbursement,
        ];
        $rows = ['| **Particulars** | **Monthly** | **Annually** |'];
        $gross = array_sum(array_map('floatval', $parts));
        if ($gross <= 0) {
            $ctc = (float) $user->annual_ctc;
            $rows[] = '| **Cost to Company** | '.$n($ctc / 12).' | '.$n($ctc).' |';

            return implode("\n", $rows);
        }
        foreach ($parts as $label => $v) {
            if ((float) $v > 0) {
                $rows[] = "| {$label} | ".$n($v).' | '.$n($v * 12).' |';
            }
        }
        $pt = (float) $user->professional_tax;
        $ptYear = abs($pt - 200) < 0.01 ? 2500 : $pt * 12;
        $pf = (float) $user->pf_employer;
        $rows[] = '| **Gross Earning (A)** | **'.$n($gross).'** | **'.$n($gross * 12).'** |';
        $rows[] = '| Professional Tax (B) - deduction | '.$n($pt).' | '.$n($ptYear).' |';
        $rows[] = '| **Net Salary** | **'.$n($gross - $pt).'** | **'.$n($gross * 12 - $ptYear).'** |';
        $rows[] = '| PF (12% of basic) - Employer Contribution (C) | '.$n($pf).' | '.$n($pf * 12).' |';
        $rows[] = '| **Cost to Company (A+C)** | **'.$n($gross + $pf).'** | **'.$n(($gross + $pf) * 12).'** |';

        return implode("\n", $rows);
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
        if (str_contains((string) $text, '[[salary-table]]') && ! (float) $user->monthly_basic) {
            $out[] = 'Salary breakup (Basic, HRA …) on the salary page';
        }

        return $out;
    }

    /**
     * Plain text -> HTML: blank line = new paragraph, "- " = bullet, **bold**, "# " = centred heading,
     * "## " = sub-heading, "| a | b |" lines = table. A line holding only a [[block]] is swapped for $blocks[block].
     */
    public static function html(?string $text, array $blocks = []): string
    {
        $blocks += ['[[page]]' => '<div class="pb"></div>'];
        $text = (string) $text;
        $parts = preg_split("/\R{2,}/", trim($text));
        $fmt = fn ($s) => preg_replace('/\*\*(.+?)\*\*/s', '<b>$1</b>', e($s));
        $html = '';
        foreach ($parts as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }
            if (preg_match('/^\[\[[a-z-]+\]\]$/', $block)) {
                $html .= $blocks[$block] ?? '';
                continue;
            }
            $lines = preg_split("/\R/", $block);
            if (preg_grep('/^\s*\[\[[a-z-]+\]\]\s*$/', $lines)) {
                // A paragraph with a block inside (e.g. "For company / [[company-sign]] / name"): keep it on one page.
                $html .= '<div class="keep">'.implode('', array_map(fn ($l) => preg_match('/^\s*(\[\[[a-z-]+\]\])\s*$/', $l, $m)
                    ? ($blocks[$m[1]] ?? '') : '<div>'.$fmt($l).'</div>', $lines)).'</div>';
                continue;
            }
            $all = fn ($re) => count(array_filter($lines, fn ($l) => preg_match($re, $l))) === count($lines);
            if ($all('/^\s*[-•]\s+/')) {
                $html .= '<ul>'.implode('', array_map(fn ($l) => '<li>'.$fmt(preg_replace('/^\s*[-•]\s+/', '', $l)).'</li>', $lines)).'</ul>';
            } elseif ($all('/^\s*\|.*\|\s*$/')) {
                $html .= '<table class="lt">';
                foreach ($lines as $l) {
                    $cells = array_map('trim', explode('|', trim(trim($l), '|')));
                    $html .= '<tr>'.implode('', array_map(fn ($c, $i) => '<td'.($i ? ' class="n"' : '').'>'.$fmt($c).'</td>', $cells, array_keys($cells))).'</tr>';
                }
                $html .= '</table>';
            } elseif ($all('/^#\s+/')) {
                $html .= implode('', array_map(fn ($l) => '<h1>'.$fmt(preg_replace('/^#\s+/', '', $l)).'</h1>', $lines));
            } elseif (preg_match('/^##\s+/', $lines[0])) {
                $html .= '<h2>'.$fmt(preg_replace('/^##\s+/', '', array_shift($lines))).'</h2>';
                if ($lines) {
                    $html .= '<p>'.implode('<br>', array_map($fmt, $lines)).'</p>';
                }
            } else {
                $html .= '<p>'.implode('<br>', array_map($fmt, $lines)).'</p>';
            }
        }

        return $html;
    }
}
