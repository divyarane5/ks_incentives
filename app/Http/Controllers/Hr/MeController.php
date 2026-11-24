<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\IssuedLetter;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\UserSalary;
use App\Services\CelebrationService;
use App\Services\LeaveService as L;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/** KeyMe home: every signed-in person's own leave, payslips, letters and team news on one page. */
class MeController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user()->loadMissing(['designation', 'department', 'location']);
        $today = now()->startOfDay();
        $has = fn ($t) => Schema::hasTable($t);

        $leave = null;
        if ($has('leave_types') && $has('leave_requests')) {
            $fy = L::fy();
            $approverId = L::approverFor($me);
            $leave = [
                'fy' => $fy,
                'balances' => L::balances($me, $fy),
                'upcoming' => LeaveRequest::with('type')->where('user_id', $me->id)
                    ->whereIn('status', ['pending', 'approved'])->where('to_date', '>=', $today->toDateString())
                    ->orderBy('from_date')->limit(5)->get(),
                'approver' => $approverId ? User::find($approverId) : null,
                'teamPending' => LeaveRequest::with(['user', 'type'])->where('approver_id', $me->id)->where('status', 'pending')
                    ->orderBy('from_date')->limit(5)->get(),
                'teamPendingCount' => LeaveRequest::where('approver_id', $me->id)->where('status', 'pending')->count(),
                'teamOff' => LeaveRequest::with(['user', 'type'])->where('status', 'approved')
                    ->whereHas('user', fn ($q) => $q->where('reporting_manager_id', $me->id))
                    ->where('from_date', '<=', $today->toDateString())->where('to_date', '>=', $today->toDateString())->get(),
                'holidays' => $has('holidays') ? Holiday::where('date', '>=', $today->toDateString())
                    ->where(fn ($q) => $q->whereNull('location_id')->orWhere('location_id', $me->work_location_id))
                    ->orderBy('date')->limit(4)->get() : collect(),
            ];
        }

        $payslips = UserSalary::where('user_id', $me->id)->where('salary_credited', '>', 0)->get()
            ->map(function ($r) {
                $start = (int) substr($r->financial_year, 0, 4);
                $r->date = Carbon::create((int) $r->month >= 4 ? $start : $start + 1, (int) $r->month, 1);

                return $r;
            })->sortByDesc('date')->take(3)->values();

        $letters = $has('issued_letters')
            ? IssuedLetter::where('user_id', $me->id)->latest('letter_date')->limit(3)->get()
            : collect();

        // Celebrations among colleagues in the next 7 days (own one shown as a greeting).
        $celebrations = CelebrationService::upcoming(7);
        $mine = $celebrations->first(fn ($c) => (int) $c['user']->id === (int) $me->id && $c['days'] === 0);
        $celebrations = $celebrations->reject(fn ($c) => (int) $c['user']->id === (int) $me->id)->take(6)->values();

        $gaps = collect([
            'Date of birth' => ! $me->dob,
            'Photo' => ! $me->photo,
            'PAN' => ! $me->pan_no,
            'Bank account' => ! $me->bank_account_number || ! $me->ifsc_code,
            'Emergency contact' => ! $me->emergency_contact_number,
        ])->filter()->keys();

        $manager = $me->reporting_manager_id ? User::find($me->reporting_manager_id) : null;

        return view('hr.me', compact('me', 'today', 'leave', 'payslips', 'letters', 'celebrations', 'mine', 'gaps', 'manager'));
    }
}
