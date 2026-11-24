@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
@php $d = fn ($n) => rtrim(rtrim(number_format((float) $n, 1), '0'), '.'); @endphp
<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1200px">
    <div class="kp-crumb"><a href="{{ route('users.index') }}">KeyPeople</a> <i class="bx bx-chevron-right"></i> <a href="{{ route('leave.hr') }}">Leave</a> <i class="bx bx-chevron-right"></i> <span>Setup</span></div>
    <div class="hr-head"><div><h4>Leave types &amp; holidays</h4><div class="sub">Days per type are for a full year (April to March); people who join mid-year get a share.</div></div></div>

    @if($errors->any())
        <div class="alert alert-danger py-2">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-category"></i>Leave types</h2></div>
                <div class="kp-panel-b" style="padding:12px 14px">
                    @foreach($types as $t)
                        <form method="POST" action="{{ route('leave.type.save', $t) }}" class="hr-form row g-2 align-items-end pb-3 mb-3" style="border-bottom:1px dashed var(--kp-line)">
                            @csrf
                            <div class="col-8 col-md-4"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ $t->name }}" required></div>
                            <div class="col-4 col-md-2"><label class="form-label">Code</label><input name="code" class="form-control" value="{{ $t->code }}" required></div>
                            <div class="col-4 col-md-2"><label class="form-label">Days / year</label><input name="yearly_quota" type="number" step="0.5" class="form-control" value="{{ $d($t->yearly_quota) }}"></div>
                            <div class="col-4 col-md-2"><label class="form-label">Carry fwd max</label><input name="carry_forward_max" type="number" step="0.5" class="form-control" value="{{ $d($t->carry_forward_max) }}"></div>
                            <div class="col-4 col-md-2"><label class="form-label">Colour</label><input name="color" type="color" class="form-control form-control-color w-100" value="{{ $t->color ?: '#3b6fb6' }}"></div>
                            <input type="hidden" name="sort" value="{{ $t->sort }}">
                            <div class="col-12 d-flex flex-wrap gap-3 align-items-center">
                                <label class="d-flex align-items-center gap-2 mb-0"><input type="hidden" name="is_paid" value="0"><input type="checkbox" class="form-check-input m-0" name="is_paid" value="1" {{ $t->is_paid ? 'checked' : '' }}> Paid</label>
                                <label class="d-flex align-items-center gap-2 mb-0"><input type="hidden" name="active" value="0"><input type="checkbox" class="form-check-input m-0" name="active" value="1" {{ $t->active ? 'checked' : '' }}> In use</label>
                                <button class="btn btn-sm btn-outline-primary ms-auto">Save</button>
                            </div>
                        </form>
                    @endforeach

                    <div class="fw-bold mb-2" style="color:var(--kp-navy)">Add a leave type</div>
                    <form method="POST" action="{{ route('leave.type.save') }}" class="hr-form row g-2 align-items-end">
                        @csrf
                        <div class="col-8 col-md-4"><label class="form-label">Name</label><input name="name" class="form-control" placeholder="e.g. Maternity leave" required></div>
                        <div class="col-4 col-md-2"><label class="form-label">Code</label><input name="code" class="form-control" placeholder="ML" required></div>
                        <div class="col-6 col-md-3"><label class="form-label">Days / year</label><input name="yearly_quota" type="number" step="0.5" class="form-control" value="0"></div>
                        <div class="col-6 col-md-3"><label class="form-label">Carry fwd max</label><input name="carry_forward_max" type="number" step="0.5" class="form-control" value="0"></div>
                        <input type="hidden" name="is_paid" value="1"><input type="hidden" name="active" value="1"><input type="hidden" name="sort" value="5">
                        <div class="col-12"><button class="btn btn-primary">Add type</button></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="kp-panel">
                <div class="kp-panel-h">
                    <h2><i class="bx bx-party"></i>Holidays {{ $year }}</h2>
                    <span>
                        <a href="?year={{ $year - 1 }}" class="small">‹ {{ $year - 1 }}</a> &nbsp;
                        <a href="?year={{ $year + 1 }}" class="small">{{ $year + 1 }} ›</a>
                    </span>
                </div>
                <div class="kp-panel-b" style="padding:12px 14px">
                    <form method="POST" action="{{ route('leave.holiday.save') }}" class="hr-form row g-2 mb-3">
                        @csrf
                        <div class="col-5"><input type="date" name="date" class="form-control" required></div>
                        <div class="col-7"><input type="text" name="name" class="form-control" placeholder="e.g. Diwali" required maxlength="100"></div>
                        <div class="col-8">
                            <select name="location_id" class="form-select raw-select">
                                <option value="">All offices</option>
                                @foreach($locations as $l)<option value="{{ $l->id }}">Only {{ $l->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-4"><button class="btn btn-primary w-100" style="min-height:44px">Add</button></div>
                    </form>
                    @forelse($holidays as $h)
                        <ul class="kp-list"><li>
                            <div class="ic" style="flex-direction:column;line-height:1;font-size:.7rem;font-weight:700"><span style="font-size:1rem">{{ $h->date->format('d') }}</span>{{ $h->date->format('M') }}</div>
                            <div class="bd"><div class="t">{{ $h->name }}</div><div class="m">{{ $h->date->format('l') }} · {{ $h->location?->name ?? 'All offices' }}</div></div>
                            <div class="r">
                                <form method="POST" action="{{ route('leave.holiday.delete', $h) }}" onsubmit="return confirm('Remove {{ addslashes($h->name) }}?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button></form>
                            </div>
                        </li></ul>
                    @empty
                        <div class="kp-empty">No holidays for {{ $year }} yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
