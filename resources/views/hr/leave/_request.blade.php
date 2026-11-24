{{-- One leave request as a list row. $r = LeaveRequest, $decide = show approve/reject, $showPerson --}}
@php $dd = fn ($n) => rtrim(rtrim(number_format((float) $n, 1), '0'), '.'); @endphp
<li style="align-items:flex-start">
    <div class="ic" style="color:{{ $r->type?->color }}"><i class="bx bx-calendar"></i></div>
    <div class="bd">
        @if($showPerson ?? true)
            <div class="t"><a href="{{ route('users.show', $r->user_id) }}">{{ $r->user?->name }}</a></div>
            <div class="m">{{ $r->user?->designation?->name }}</div>
        @endif
        <div class="{{ ($showPerson ?? true) ? 'm fw-semibold' : 't' }}" style="color:var(--kp-text)">{{ $r->dates_label }}</div>
        <div class="m"><span class="hr-dot" style="background:{{ $r->type?->color ?? '#999' }}"></span>{{ $r->type?->name }} · {{ $dd($r->days) }} {{ $r->days == 1 ? 'day' : 'days' }} · asked {{ $r->created_at?->format('d M') }}</div>
        @if($r->reason)<div class="m">“{{ $r->reason }}”</div>@endif
        @if($r->status !== 'pending' && ($r->decider || $r->decision_note))
            <div class="m">{{ $r->decider ? ucfirst($r->status).' by '.$r->decider->name : '' }}{{ $r->decision_note ? ': '.$r->decision_note : '' }}</div>
        @elseif($r->status === 'pending' && ($showApprover ?? false))
            <div class="m">Waiting for {{ $r->approver?->name ?? 'HR' }}</div>
        @endif
        @if(($decide ?? false) && $r->status === 'pending')
            <form method="POST" action="{{ route('leave.decide', $r) }}" class="hr-actions">
                @csrf
                <input type="text" name="note" class="form-control form-control-sm" placeholder="Note (optional)" maxlength="255" style="flex:1 1 160px;min-height:38px;border-radius:9px">
                <button name="action" value="approve" class="btn btn-sm btn-success"><i class="bx bx-check"></i> Approve</button>
                <button name="action" value="reject" class="btn btn-sm btn-outline-danger"><i class="bx bx-x"></i> Reject</button>
            </form>
        @endif
    </div>
    <div class="r">
        <span class="hr-status {{ $r->status }}">{{ \App\Models\LeaveRequest::STATUSES[$r->status] ?? $r->status }}</span>
        @if(($cancel ?? false) && in_array($r->status, ['pending', 'approved'], true))
            <form method="POST" action="{{ route('leave.cancel', $r) }}" class="mt-2" onsubmit="return confirm('Cancel this leave?')">@csrf<button class="btn btn-sm btn-outline-secondary">Cancel</button></form>
        @endif
    </div>
</li>
