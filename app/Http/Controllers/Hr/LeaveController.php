<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\LeaveAdjustment;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Location;
use App\Models\User;
use App\Services\LeaveService as L;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** Leave: apply and see balance (everyone), approve (managers and HR), setup and balances (HR). */
class LeaveController extends Controller
{
    /* ------------------------------------------------------------------ everyone */

    public function my(Request $request)
    {
        $me = $request->user();
        $fy = $this->pickFy($request);
        $balances = L::balances($me, $fy);
        $types = LeaveType::active()->get();
        [$start, $end] = L::fyRange($fy);
        $requests = LeaveRequest::with(['type', 'approver', 'decider'])->where('user_id', $me->id)
            ->where('to_date', '>=', $start->toDateString())->where('from_date', '<=', $end->toDateString())
            ->orderByDesc('from_date')->get();
        $holidays = Holiday::where('date', '>=', now()->toDateString())
            ->where(fn ($q) => $q->whereNull('location_id')->orWhere('location_id', $me->work_location_id))
            ->orderBy('date')->limit(6)->get();
        $approver = ($id = L::approverFor($me)) ? User::find($id) : null;
        $teamPending = LeaveRequest::where('approver_id', $me->id)->where('status', 'pending')->count();
        $offs = L::weeklyOffs($me);
        $holidayDates = Holiday::whereBetween('date', [now()->subMonth()->toDateString(), now()->addYear()->toDateString()])
            ->where(fn ($q) => $q->whereNull('location_id')->orWhere('location_id', $me->work_location_id))
            ->pluck('date')->map(fn ($d) => Carbon::parse($d)->toDateString())->all();

        return view('hr.leave.my', compact('me', 'fy', 'balances', 'types', 'requests', 'holidays', 'approver', 'teamPending', 'offs', 'holidayDates'));
    }

    public function store(Request $request)
    {
        $me = $request->user();
        $data = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'half_day' => 'nullable|in:first,second',
            'reason' => 'nullable|string|max:1000',
            'user_id' => 'nullable|exists:users,id', // HR applying on someone's behalf
        ], ['to_date.after_or_equal' => 'The "to" date can\'t be before the "from" date.']);

        $forUser = $me;
        if (! empty($data['user_id']) && (int) $data['user_id'] !== (int) $me->id) {
            abort_unless(L::isHr($me), 403);
            $forUser = User::findOrFail($data['user_id']);
        }

        $type = LeaveType::findOrFail($data['leave_type_id']);
        $from = Carbon::parse($data['from_date']);
        $to = Carbon::parse($data['to_date']);
        $half = $from->equalTo($to) ? ($data['half_day'] ?? null) : null;

        if (L::fy($from) !== L::fy($to)) {
            return back()->withInput()->withErrors(['to_date' => 'Please apply separately for days before and after 1 April.']);
        }

        $days = L::countDays($forUser, $from, $to, $half);
        if ($days <= 0) {
            return back()->withInput()->withErrors(['from_date' => 'Those dates are all weekly offs or holidays, so no leave is needed.']);
        }

        $overlap = LeaveRequest::where('user_id', $forUser->id)->whereIn('status', ['pending', 'approved'])
            ->where('from_date', '<=', $to->toDateString())->where('to_date', '>=', $from->toDateString())->exists();
        if ($overlap) {
            return back()->withInput()->withErrors(['from_date' => 'There is already a leave on some of these dates.']);
        }

        if ($type->is_paid) {
            $bal = collect(L::balances($forUser, L::fy($from)))->firstWhere('type.id', $type->id);
            $available = $bal['available'] ?? 0;
            if ($days > $available) {
                return back()->withInput()->withErrors(['leave_type_id' => "Only {$this->d($available)} {$type->name} left. Choose fewer days or Unpaid leave for the rest."]);
            }
        }

        $byHr = $forUser->id !== $me->id;
        LeaveRequest::create([
            'user_id' => $forUser->id,
            'leave_type_id' => $type->id,
            'from_date' => $from->toDateString(),
            'to_date' => $to->toDateString(),
            'half_day' => $half,
            'days' => $days,
            'reason' => $data['reason'] ?? null,
            'status' => $byHr ? 'approved' : 'pending',
            'approver_id' => L::approverFor($forUser),
            'decided_by' => $byHr ? $me->id : null,
            'decided_at' => $byHr ? now() : null,
            'decision_note' => $byHr ? 'Added by HR' : null,
            'created_by' => $me->id,
        ]);

        if ($byHr) {
            return back()->with('success', "Leave added for {$forUser->name} ({$this->d($days)} ".($days == 1 ? 'day' : 'days').').');
        }

        return redirect()->route('leave.my')->with('success', "Leave request sent ({$this->d($days)} ".($days == 1 ? 'day' : 'days').'). You\'ll see it here once it\'s approved.');
    }

    public function cancel(Request $request, LeaveRequest $leave)
    {
        $me = $request->user();
        $own = (int) $leave->user_id === (int) $me->id;
        abort_unless($own || L::isHr($me), 403);
        if (! in_array($leave->status, ['pending', 'approved'], true)) {
            return back();
        }
        // People can cancel their own leave until it starts; HR can cancel any time.
        if ($own && ! L::isHr($me) && $leave->status === 'approved' && $leave->from_date->lte(today())) {
            return back()->with('error', 'This leave has already started. Ask HR to change it.');
        }
        $leave->update(['status' => 'cancelled', 'decided_by' => $me->id, 'decided_at' => now(), 'decision_note' => $own ? 'Cancelled by employee' : 'Cancelled by HR']);

        return back()->with('success', 'Leave cancelled.');
    }

    /* ------------------------------------------------------------------ managers */

    public function team(Request $request)
    {
        $me = $request->user();
        $pending = LeaveRequest::with(['user.designation', 'type'])
            ->where('status', 'pending')
            ->where('approver_id', $me->id)
            ->orderBy('from_date')->get();
        $teamIds = User::where('reporting_manager_id', $me->id)->where('status', 'Active')->pluck('id');
        $away = LeaveRequest::with(['user', 'type'])->whereIn('user_id', $teamIds)->where('status', 'approved')
            ->where('to_date', '>=', today()->toDateString())->where('from_date', '<=', today()->addDays(14)->toDateString())
            ->orderBy('from_date')->get();
        $recent = LeaveRequest::with(['user', 'type'])->where('approver_id', $me->id)->whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('decided_at')->limit(15)->get();

        return view('hr.leave.team', compact('pending', 'away', 'recent'));
    }

    public function decide(Request $request, LeaveRequest $leave)
    {
        $me = $request->user();
        abort_unless(L::canDecide($me, $leave), 403);
        $data = $request->validate(['action' => 'required|in:approve,reject', 'note' => 'nullable|string|max:255']);
        if ($leave->status !== 'pending') {
            return back()->with('error', 'This request was already '.strtolower(LeaveRequest::STATUSES[$leave->status] ?? $leave->status).'.');
        }
        if ($data['action'] === 'approve' && $leave->type?->is_paid) {
            // Re-check the balance at approval time (other leave may have been approved since).
            $bal = collect(L::balances($leave->user, L::fy($leave->from_date)))->firstWhere('type.id', $leave->leave_type_id);
            // "available" already counts this request as pending, so below zero means not enough.
            if ($bal && $bal['available'] < 0) {
                return back()->with('error', "{$leave->user->name} doesn't have enough {$leave->type->name} left. Reject it, or ask HR to add days.");
            }
        }
        $leave->update([
            'status' => $data['action'] === 'approve' ? 'approved' : 'rejected',
            'decided_by' => $me->id,
            'decided_at' => now(),
            'decision_note' => $data['note'] ?? null,
        ]);

        return back()->with('success', ($data['action'] === 'approve' ? 'Approved' : 'Rejected').": {$leave->user->name}, {$leave->dates_label}.");
    }

    /* ------------------------------------------------------------------ HR */

    public function hr(Request $request)
    {
        abort_unless(L::isHr($request->user()), 403);
        $fy = $this->pickFy($request);
        [$start, $end] = L::fyRange($fy);
        $status = $request->get('status', 'pending');
        $q = LeaveRequest::with(['user.designation', 'type', 'approver', 'decider'])
            ->where('to_date', '>=', $start->toDateString())->where('from_date', '<=', $end->toDateString());
        if ($status && isset(LeaveRequest::STATUSES[$status])) {
            $q->where('status', $status);
        }
        if ($request->filled('user_id')) {
            $q->where('user_id', $request->user_id);
        }
        $leaves = $q->orderBy($status === 'pending' ? 'from_date' : 'updated_at', $status === 'pending' ? 'asc' : 'desc')->paginate(30)->withQueryString();
        $counts = LeaveRequest::where('to_date', '>=', $start->toDateString())->where('from_date', '<=', $end->toDateString())
            ->selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status');
        $people = User::where('status', 'Active')->orderBy('name')->get(['id', 'name', 'employee_code']);
        $person = $request->filled('user_id') ? User::find($request->user_id) : null;
        $balances = $person ? L::balances($person, $fy) : null;
        $adjustments = $person ? LeaveAdjustment::with(['type', 'creator'])->where('user_id', $person->id)->where('financial_year', $fy)->latest()->get() : collect();
        $todayAway = LeaveRequest::with(['user', 'type'])->where('status', 'approved')
            ->where('from_date', '<=', today()->toDateString())->where('to_date', '>=', today()->toDateString())->get();
        $types = LeaveType::active()->get();

        return view('hr.leave.hr', compact('fy', 'status', 'leaves', 'counts', 'people', 'person', 'balances', 'adjustments', 'todayAway', 'types'));
    }

    public function adjust(Request $request)
    {
        $me = $request->user();
        abort_unless(L::isHr($me), 403);
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'financial_year' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'days' => 'required|numeric|between:-60,60|not_in:0',
            'note' => 'required|string|max:255',
        ], ['note.required' => 'Please write why (e.g. "Carried forward from 2025-26").']);
        LeaveAdjustment::create($data + ['created_by' => $me->id]);

        return back()->with('success', 'Balance updated.');
    }

    public function setup(Request $request)
    {
        abort_unless(L::isHr($request->user()), 403);
        $types = LeaveType::orderBy('sort')->orderBy('name')->get();
        $year = (int) $request->get('year', now()->year);
        $holidays = Holiday::with('location')->whereYear('date', $year)->orderBy('date')->get();
        $locations = Location::orderBy('name')->get(['id', 'name']);

        return view('hr.leave.setup', compact('types', 'holidays', 'locations', 'year'));
    }

    public function saveType(Request $request, ?LeaveType $type = null)
    {
        abort_unless(L::isHr($request->user()), 403);
        $data = $request->validate([
            'name' => 'required|string|max:60',
            'code' => 'required|string|max:10',
            'yearly_quota' => 'required|numeric|between:0,365',
            'carry_forward_max' => 'nullable|numeric|between:0,365',
            'is_paid' => 'nullable|boolean',
            'active' => 'nullable|boolean',
            'color' => 'nullable|string|max:20',
            'sort' => 'nullable|integer|between:0,999',
        ]);
        $data['is_paid'] = $request->boolean('is_paid');
        $data['active'] = $request->boolean('active');
        $data['carry_forward_max'] = $data['carry_forward_max'] ?? 0;
        $data['sort'] = $data['sort'] ?? 0;
        $data['code'] = strtoupper($data['code']);
        if (! $data['is_paid']) {
            $data['yearly_quota'] = 0;
        }
        $type && $type->exists ? $type->update($data) : LeaveType::create($data);

        return back()->with('success', 'Leave type saved.');
    }

    public function saveHoliday(Request $request)
    {
        abort_unless(L::isHr($request->user()), 403);
        $data = $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:100',
            'location_id' => 'nullable|exists:locations,id',
        ]);
        Holiday::create($data);

        return redirect()->route('leave.setup', ['year' => Carbon::parse($data['date'])->year])->with('success', 'Holiday added.');
    }

    public function deleteHoliday(Request $request, Holiday $holiday)
    {
        abort_unless(L::isHr($request->user()), 403);
        $holiday->delete();

        return back()->with('success', 'Holiday removed.');
    }

    /** Small JSON helper for the apply form: working days between two dates. */
    public function count(Request $request)
    {
        $me = $request->user();
        $user = $request->filled('user_id') && L::isHr($me) ? User::findOrFail($request->user_id) : $me;
        $from = $request->get('from');
        $to = $request->get('to') ?: $from;
        if (! $from) {
            return response()->json(['days' => 0]);
        }

        return response()->json(['days' => L::countDays($user, $from, $to, $request->get('half') ?: null)]);
    }

    private function pickFy(Request $request): string
    {
        $fy = (string) $request->get('fy', L::fy());

        return preg_match('/^\d{4}-\d{2}$/', $fy) ? $fy : L::fy();
    }

    private function d($n): string
    {
        return rtrim(rtrim(number_format((float) $n, 1), '0'), '.');
    }
}
