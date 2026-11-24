<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\CelebrationPost;
use App\Models\Holiday;
use App\Models\IssuedLetter;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\UserSalary;
use App\Services\CelebrationService;
use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/** KeyPeople home: the HR numbers and lists HR looks at every day, on one page. */
class HrDashboardController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();
        abort_unless($me->can('module-keypeople') && ($me->can('user-view') || $me->can('user-edit')), 403);
        $today = now()->startOfDay();
        $mStart = $today->copy()->startOfMonth()->toDateString();
        $mEnd = $today->copy()->endOfMonth()->toDateString();
        $has = fn ($t) => Schema::hasTable($t);

        $active = User::with(['department', 'designation', 'location', 'businessUnit'])->where('status', 'Active')->get();
        $isProbation = fn ($u) => strtolower((string) $u->employment_status) === 'probation' || (! $u->confirm_date && $u->joining_date && Carbon::parse($u->joining_date)->gte($today->copy()->subMonths(6)));

        // Headcount
        $head = [
            'active' => $active->count(),
            'probation' => $active->filter($isProbation)->count(),
            'joined' => User::whereBetween('joining_date', [$mStart, $mEnd])->count(),
            'left' => User::whereBetween('leaving_date', [$mStart, $mEnd])->count(),
            'inactive' => User::where('status', '!=', 'Active')->whereNotIn('status', ['Exited'])->count(),
            'exited' => User::where('status', 'Exited')->count(),
        ];
        $group = fn ($key) => $active->groupBy($key)->map->count()->sortDesc();
        $byDept = $group(fn ($u) => $u->department?->name ?: 'No department');
        $byCompany = $group(fn ($u) => $u->entity ?: ($u->businessUnit?->name ?: 'Not set'));
        $byLocation = $group(fn ($u) => $u->location?->name ?: 'Not set');
        $gender = $group(fn ($u) => ucfirst(strtolower((string) $u->gender)) ?: 'Not set');

        // Joiners and leavers
        $joiners = User::with('designation')->where('joining_date', '>=', $today->copy()->subDays(30)->toDateString())
            ->orderByDesc('joining_date')->limit(8)->get();
        $noticePeriod = User::with('designation')->whereNotNull('leaving_date')->where('leaving_date', '>=', $today->toDateString())
            ->orderBy('leaving_date')->limit(8)->get();

        // Probation ending in the next 30 days (joining + probation days), not confirmed yet
        $probationDue = $active->filter(fn ($u) => ! $u->confirm_date && $u->joining_date && strtolower((string) $u->employment_status) !== 'confirmed')
            ->map(function ($u) {
                $u->probation_end = Carbon::parse($u->joining_date)->addDays((int) ($u->probation_period_days ?: 180));

                return $u;
            })
            ->filter(fn ($u) => $u->probation_end->lte($today->copy()->addDays(30)))
            ->sortBy('probation_end')->values();

        // Leave
        $leave = null;
        if ($has('leave_requests')) {
            $leave = [
                'today' => LeaveRequest::with(['user', 'type'])->where('status', 'approved')
                    ->whereDate('from_date', '<=', $today)->whereDate('to_date', '>=', $today)->get(),
                'pending' => LeaveRequest::where('status', 'pending')->count(),
                'week' => LeaveRequest::with(['user', 'type'])->where('status', 'approved')
                    ->whereDate('from_date', '>', $today)->whereDate('from_date', '<=', $today->copy()->addDays(7))->orderBy('from_date')->get(),
                'holidays' => $has('holidays') ? Holiday::whereDate('date', '>=', $today)->orderBy('date')->limit(4)->get() : collect(),
            ];
        }

        // Birthdays and anniversaries
        $celebrations = CelebrationService::upcoming(14);
        $posted = $has('celebration_posts')
            ? CelebrationPost::where('post_date', '>=', $today->toDateString())->get()->map(fn ($p) => $p->user_id.'|'.$p->type.'|'.$p->post_date->toDateString())->all()
            : [];

        // Salary: last month's register
        $salary = null;
        if ($me->can('user-salary-view')) {
            $last = $today->copy()->subMonthNoOverflow()->startOfMonth();
            $rows = UserSalary::where('financial_year', LeaveService::fy($last))->where('month', $last->month)->get();
            $paid = $rows->filter(fn ($r) => $r->salary_credited > 0);
            $salary = [
                'month' => $last, 'fy' => LeaveService::fy($last),
                'paid' => $paid->count(), 'net' => (float) $paid->sum('salary_credited'), 'gross' => (float) $rows->sum('gross_salary'),
                'cost' => (float) $rows->sum('total_employee_cost'),
                'missing' => max(0, $active->filter(fn ($u) => ! $u->joining_date || Carbon::parse($u->joining_date)->lte($last->copy()->endOfMonth()))->count() - $paid->count()),
                'no_bank' => $active->filter(fn ($u) => ! $u->bank_account_number || ! $u->ifsc_code)->count(),
            ];
        }

        // Letters
        $letters = null;
        if ($has('issued_letters')) {
            $letters = [
                'month' => IssuedLetter::whereBetween('letter_date', [$mStart, $mEnd])->count(),
                'recent' => IssuedLetter::with('user')->latest('letter_date')->latest('id')->limit(5)->get(),
                'signature' => $has('letter_signatories') && \App\Models\LetterSignatory::where('active', true)->exists(),
            ];
        }

        // Profiles missing key details
        $gaps = collect([
            ['Date of birth', fn ($u) => ! $u->dob],
            ['Photo', fn ($u) => ! $u->photo],
            ['PAN', fn ($u) => ! $u->pan_no],
            ['Bank account', fn ($u) => ! $u->bank_account_number || ! $u->ifsc_code],
            ['Reporting manager', fn ($u) => ! $u->reporting_manager_id],
            ['Emergency contact', fn ($u) => ! $u->emergency_contact_number],
            ['Mobile', fn ($u) => ! $u->official_contact && ! $u->personal_contact],
        ])->map(fn ($g) => ['label' => $g[0], 'people' => $active->filter($g[1])->sortBy('name')->values()])
            ->filter(fn ($g) => $g['people']->count())->values();

        return view('hr.dashboard', compact('head', 'byDept', 'byCompany', 'byLocation', 'gender', 'joiners', 'noticePeriod',
            'probationDue', 'leave', 'celebrations', 'posted', 'salary', 'letters', 'gaps', 'today'));
    }
}
