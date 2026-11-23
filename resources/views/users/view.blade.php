@use('App\Support\PeopleFormat', 'F')
@use('Illuminate\Support\Str')
@extends('layouts.app')

@section('content')
@php
    $me = auth()->user();
    $isSelf = $me && $me->id === $user->id;
    $canEdit = $me && $me->can('user-edit');
    $canSalary = $me && ($me->can('user-salary-view') || $isSelf);

    $designation = $user->designation?->name;
    $manager = $user->currentReportingManagerHistory?->manager ?? $user->reportingManager;
    $role = $user->getRoleNames()->first();
    $exited = strcasecmp((string) $user->status, 'Exited') === 0;
    $probation = strtolower((string) $user->employment_status) === 'probation';
    $phone = $user->official_contact ?: $user->personal_contact;
    $intl = F::intlPhone($phone);
    $email = $user->official_email ?: $user->email;
    $cardSlug = Str::slug(trim($user->first_name.' '.$user->last_name));
    $team = $user->teamMembers->filter(fn ($m) => strcasecmp((string) $m->status, 'Active') === 0)->sortBy('name');

    // Profile completeness: the fields HR needs for payroll and compliance.
    $needed = [
        'Photo' => $user->photo, 'Date of birth' => $user->dob, 'Mobile' => $phone,
        'Official email' => $user->official_email, 'Department' => $user->department_id,
        'Designation' => $user->designation_id, 'Reporting manager' => $manager?->id,
        'Joining date' => $user->joining_date, 'Annual CTC' => (float) $user->annual_ctc > 0 ? 1 : null,
        'PAN' => $user->pan_no, 'Aadhaar' => $user->aadhar_no, 'Bank account' => $user->bank_account_number,
        'IFSC' => $user->ifsc_code, 'Current address' => $user->communication_address,
        'Emergency contact' => $user->emergency_contact_number, 'Blood group' => $user->blood_group,
    ];
    $missing = array_keys(array_filter($needed, fn ($v) => $v === null || $v === ''));
    $pct = (int) round(100 * (count($needed) - count($missing)) / count($needed));

    $masked = function ($value) {
        if (! $value) return null;
        return '<span data-full="'.e($value).'">'.e(F::mask($value)).'</span><button type="button" class="kp-mask-btn" onclick="kpReveal(this)">Show</button>';
    };
    $yesNo = fn ($v) => '<span class="kp-chip '.($v ? 'ok' : 'warn').'">'.($v ? 'Yes' : 'No').'</span>';
@endphp

@include('users.partials.people-styles')

<div class="container-xxl flex-grow-1 container-p-y kp">
    <div class="kp-crumb">
        <a href="{{ route('users.index') }}">KeyPeople</a> <i class="bx bx-chevron-right"></i>
        <a href="{{ route('users.index') }}">Employees</a> <i class="bx bx-chevron-right"></i>
        <span>{{ $user->first_name ?: $user->name }}</span>
    </div>

    {{-- HERO --}}
    <section class="kp-hero">
        <div class="kp-hero-top">
            @if($user->photo)
                <img class="kp-avatar" src="{{ asset('storage/'.$user->photo) }}" alt="{{ $user->name }}">
            @else
                <div class="kp-avatar-initials">{{ F::initials($user->name) }}</div>
            @endif
            <div style="min-width:0">
                <h1>{{ trim($user->first_name.' '.$user->last_name) ?: $user->name }}</h1>
                <div class="kp-sub">
                    {{ $designation ?? 'No designation' }}@if($user->department?->name) · {{ $user->department->name }}@endif
                    <br>{{ $user->employee_code ?? 'No code' }}@if($user->businessUnit?->name) · {{ $user->businessUnit->name }}@endif
                </div>
                <div class="kp-chips">
                    <span class="kp-chip {{ $exited ? 'bad' : 'ok' }}">{{ $user->status ?? 'Unknown' }}</span>
                    @if($user->employment_status)
                        <span class="kp-chip {{ $probation ? 'warn' : 'ok' }}">{{ ucfirst($user->employment_status) }}</span>
                    @endif
                    @if($role)<span class="kp-chip gold">{{ $role }}</span>@endif
                </div>
            </div>
        </div>

        <div class="kp-actions">
            <a class="kp-act {{ $intl ? '' : 'disabled' }}" href="{{ $intl ? 'tel:+'.$intl : '#' }}"><i class="bx bx-phone"></i>Call</a>
            <a class="kp-act {{ $intl ? '' : 'disabled' }}" href="{{ $intl ? 'https://wa.me/'.$intl : '#' }}" target="_blank" rel="noopener"><i class="bx bxl-whatsapp"></i>WhatsApp</a>
            <a class="kp-act {{ $email ? '' : 'disabled' }}" href="{{ $email ? 'mailto:'.$email : '#' }}"><i class="bx bx-envelope"></i>Email</a>
            <a class="kp-act {{ $cardSlug && ! $exited ? '' : 'disabled' }}" href="{{ $cardSlug ? route('users.card', $cardSlug) : '#' }}" target="_blank"><i class="bx bx-id-card"></i>Card</a>
        </div>

        <div class="kp-hero-links">
            @if($canEdit)
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-kp btn-sm"><i class="bx bx-edit-alt"></i> Edit profile</a>
            @endif
            @if($me && $me->can('user-salary-view'))
                <a href="{{ route('users.salary.index', $user->id) }}" class="btn btn-kp-ghost btn-sm"><i class="bx bx-rupee"></i> Monthly salary</a>
            @endif
        </div>
    </section>

    {{-- KEY FACTS --}}
    <div class="kp-facts">
        <div class="kp-fact">
            <div class="l">Joined</div>
            <div class="v">{{ F::date($user->joining_date) ?? '—' }}</div>
            <div class="s">{{ F::tenure($user->joining_date, $exited ? $user->leaving_date : null) ?? '' }}{{ $exited ? ' (left)' : '' }}</div>
        </div>
        <div class="kp-fact">
            <div class="l">Reports to</div>
            <div class="v">
                @if($manager)
                    <a href="{{ route('users.show', $manager->id) }}">{{ $manager->name }}</a>
                @else — @endif
            </div>
            <div class="s">{{ $manager?->designation?->name ?? '' }}</div>
        </div>
        <div class="kp-fact">
            <div class="l">Team</div>
            <div class="v">{{ $team->count() }} {{ Str::plural('person', $team->count()) }}</div>
            <div class="s">report directly</div>
        </div>
        <div class="kp-fact">
            <div class="l">Location</div>
            <div class="v">{{ $user->location?->name ?? '—' }}</div>
            <div class="s">{{ $user->location_handled ? 'Handles '.$user->location_handled : '' }}</div>
        </div>
    </div>

    {{-- SECTION PILLS --}}
    <ul class="nav kp-tabs" id="userTabs" role="tablist">
        <li><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview" type="button"><i class="bx bx-user"></i>Overview</button></li>
        <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#job" type="button"><i class="bx bx-briefcase"></i>Job</button></li>
        @if($canSalary)
            <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#pay" type="button"><i class="bx bx-wallet"></i>Pay</button></li>
        @endif
        <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bank" type="button"><i class="bx bx-credit-card"></i>Bank &amp; IDs</button></li>
        <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents" type="button"><i class="bx bx-file"></i>Documents</button></li>
        <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#history" type="button"><i class="bx bx-history"></i>History</button></li>
        <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#personal" type="button"><i class="bx bx-home-heart"></i>Personal</button></li>
    </ul>

    <div class="tab-content p-0" style="background:transparent;box-shadow:none">

        {{-- OVERVIEW --}}
        <div class="tab-pane fade show active" id="overview">
            @if($canEdit && $missing)
                <div class="kp-panel">
                    <div class="kp-panel-b" style="padding:12px 14px">
                        <div class="kp-complete">
                            <div class="kp-ring" style="--p: {{ $pct }}"><span>{{ $pct }}%</span></div>
                            <div style="min-width:0">
                                <div class="fw-bold" style="color:var(--kp-navy)">Profile {{ $pct }}% complete</div>
                                <div class="kp-missing">
                                    @foreach($missing as $m)<span>{{ $m }}</span>@endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-id-card"></i>Basic details</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Full name', $user->name],
                        ['Employee code', $user->employee_code],
                        ['Gender', $user->gender],
                        ['Date of birth', $user->dob ? F::date($user->dob).' ('.\Carbon\Carbon::parse($user->dob)->age.' yrs)' : null],
                        ['Company', $user->entity],
                        ['Business unit', $user->businessUnit?->name],
                    ]])
                </div>
            </div>

            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-phone-call"></i>Contact</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Official mobile', $user->official_contact ? '<a href="tel:+'.e(F::intlPhone($user->official_contact)).'">'.e($user->official_contact).'</a>' : null, 'html'],
                        ['Personal mobile', $user->personal_contact ? '<a href="tel:+'.e(F::intlPhone($user->personal_contact)).'">'.e($user->personal_contact).'</a>' : null, 'html'],
                        ['Official email', $user->official_email ? '<a href="mailto:'.e($user->official_email).'">'.e($user->official_email).'</a>' : null, 'html'],
                        ['Personal email', $user->personal_email ? '<a href="mailto:'.e($user->personal_email).'">'.e($user->personal_email).'</a>' : null, 'html'],
                        ['Login email', $user->email],
                        ['Last login', $user->last_login ? F::date($user->last_login, 'd M Y, h:i A') : null],
                    ]])
                </div>
            </div>

            <div class="kp-panel">
                <div class="kp-panel-h">
                    <h2><i class="bx bx-group"></i>Team</h2>
                    <span class="kp-note">{{ $team->count() }} direct</span>
                </div>
                <div class="kp-panel-b">
                    @if($team->count())
                        <ul class="kp-list">
                            @foreach($team as $m)
                                <li>
                                    <div class="ic"><i class="bx bx-user"></i></div>
                                    <div class="bd">
                                        <div class="t"><a href="{{ route('users.show', $m->id) }}">{{ $m->name }}</a></div>
                                        <div class="m">{{ $m->designation?->name ?? 'No designation' }} · {{ $m->employee_code }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="kp-empty">Nobody reports to {{ $user->first_name ?: 'this person' }} right now.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- JOB --}}
        <div class="tab-pane fade" id="job">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-briefcase"></i>Role and team</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Department', $user->department?->name],
                        ['Designation', $designation],
                        ['System role', $role],
                        ['Reporting manager', $manager ? '<a href="'.route('users.show', $manager->id).'">'.e($manager->name).'</a>' : null, 'html'],
                        ['Work location', $user->location?->name],
                        ['Location handled', $user->location_handled],
                        ['Weekly off', $user->work_off],
                    ]])
                </div>
            </div>

            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-calendar-check"></i>Employment dates</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Joining date', F::date($user->joining_date)],
                        ['Probation', $user->probation_period_days ? $user->probation_period_days.' days' : null],
                        ['Confirmation date', F::date($user->confirm_date)],
                        ['Employment status', $user->employment_status ? ucfirst($user->employment_status) : null],
                        ['Notice period', $user->notice_period_days ? $user->notice_period_days.' days' : null],
                        ['Time with us', F::tenure($user->joining_date, $exited ? $user->leaving_date : null)],
                    ]])
                </div>
            </div>

            @if(\Illuminate\Support\Facades\Schema::hasTable('leave_types') && ($isSelf || \App\Services\LeaveService::isHr($me)))
                @php $lfy = \App\Services\LeaveService::fy(); $lbal = \App\Services\LeaveService::balances($user, $lfy); $dd = fn ($n) => rtrim(rtrim(number_format((float) $n, 1), '0'), '.'); @endphp
                <div class="kp-panel">
                    <div class="kp-panel-h">
                        <h2><i class="bx bx-calendar"></i>Leave FY {{ $lfy }}</h2>
                        <a href="{{ $isSelf ? route('leave.my') : route('leave.hr', ['user_id' => $user->id, 'status' => '']) }}" class="small">All leave</a>
                    </div>
                    <div class="kp-panel-b">
                        @include('users.partials.fields', ['items' => collect($lbal)->map(fn ($b) => [
                            $b['type']->name,
                            $b['type']->is_paid ? $dd($b['available']).' left of '.$dd($b['quota'] + $b['adjusted']).' (used '.$dd($b['used']).')' : $dd($b['used']).' days taken',
                        ])->all()])
                    </div>
                </div>
            @endif

            @if($exited || $user->leaving_date || $user->latestExitHistory)
                <div class="kp-panel">
                    <div class="kp-panel-h"><h2><i class="bx bx-log-out-circle"></i>Exit</h2></div>
                    <div class="kp-panel-b">
                        @include('users.partials.fields', ['items' => [
                            ['Leaving date', F::date($user->leaving_date ?? $user->latestExitHistory?->exit_date)],
                            ['Exit type', $user->exit_status ?? $user->latestExitHistory?->exit_type],
                            ['Reason', $user->reason_for_leaving ?? $user->latestExitHistory?->exit_reason],
                            ['Full & final', $user->fnf_status],
                            ['Rehire?', $user->latestExitHistory ? ($user->latestExitHistory->is_rehirable ? 'Yes' : 'No') : null],
                        ]])
                    </div>
                </div>
            @endif

            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-laptop"></i>Company assets</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Laptop / desktop', $user->laptop_desktop],
                        ['Company phone', $user->company_phone],
                        ['Company SIM', $user->company_sim],
                        ['Notes', $user->additional_comments],
                    ]])
                </div>
            </div>
        </div>

        {{-- PAY --}}
        @if($canSalary)
        <div class="tab-pane fade" id="pay">
            @php
                $monthly = (float) $user->current_ctc;
                $parts = [
                    ['Basic', (float) $user->monthly_basic, 'var(--kp-navy)'],
                    ['HRA', (float) $user->monthly_hra, '#3b6fb6'],
                    ['Special', (float) $user->special_allowance, '#7aa0d4'],
                    ['Conveyance', (float) $user->conveyance_allowance, 'var(--kp-gold)'],
                    ['Medical', (float) $user->medical_reimbursement, '#e0d48c'],
                ];
                $sumParts = array_sum(array_column($parts, 1)) ?: 1;
            @endphp
            <div class="kp-panel">
                <div class="kp-panel-h">
                    <h2><i class="bx bx-wallet"></i>Current pay</h2>
                    @if($me->can('user-salary-view'))
                        <a href="{{ route('users.salary.index', $user->id) }}" class="btn btn-sm btn-outline-primary">Monthly salary</a>
                    @endif
                </div>
                <div class="kp-panel-b">
                    <div class="kp-pay-hero">
                        <div class="kp-fact"><div class="l">Annual CTC</div><div class="v kp-money">{{ F::inr($user->annual_ctc) ?? '—' }}</div></div>
                        <div class="kp-fact"><div class="l">Monthly CTC</div><div class="v kp-money">{{ F::inr($user->current_ctc) ?? '—' }}</div></div>
                        <div class="kp-fact"><div class="l">Deductions</div><div class="v kp-money">{{ F::inr($user->net_deductions) ?? '—' }}</div></div>
                        <div class="kp-fact"><div class="l">Take-home</div><div class="v kp-money">{{ F::inr($user->net_salary) ?? '—' }}</div></div>
                    </div>
                    @if($monthly > 0)
                        <div class="kp-bar">
                            @foreach($parts as [$l, $v, $c])
                                @if($v > 0)<span style="width: {{ round(100 * $v / $sumParts, 2) }}%; background: {{ $c }}"></span>@endif
                            @endforeach
                        </div>
                        <div class="kp-legend">
                            @foreach($parts as [$l, $v, $c])
                                <span><i style="background: {{ $c }}"></i>{{ $l }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-list-ul"></i>Monthly breakup</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Basic', F::inr($user->monthly_basic)],
                        ['HRA', F::inr($user->monthly_hra)],
                        ['Special allowance', F::inr($user->special_allowance)],
                        ['Conveyance', F::inr($user->conveyance_allowance)],
                        ['Medical', F::inr($user->medical_reimbursement)],
                        ['Professional tax', F::inr($user->professional_tax)],
                        ['PF (employee)', F::inr($user->pf_employee)],
                        ['PF (employer)', F::inr($user->pf_employer)],
                    ]])
                </div>
            </div>

            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-trending-up"></i>Salary revisions</h2></div>
                <div class="kp-panel-b">
                    @php $revs = $user->salaryHistories->sortByDesc('effective_from')->values(); @endphp
                    @if($revs->count())
                        <ul class="kp-list">
                            @foreach($revs as $i => $s)
                                @php $prev = $revs[$i + 1] ?? null; $chg = $prev && $prev->annual_ctc > 0 ? round(100 * ($s->annual_ctc - $prev->annual_ctc) / $prev->annual_ctc, 1) : null; @endphp
                                <li>
                                    <div class="ic"><i class="bx {{ $chg !== null && $chg < 0 ? 'bx-trending-down' : 'bx-trending-up' }}"></i></div>
                                    <div class="bd">
                                        <div class="t kp-money">{{ F::inr($s->annual_ctc) }} / yr</div>
                                        <div class="m">From {{ F::date($s->effective_from) }}{{ $s->change_reason ? ' · '.$s->change_reason : '' }}</div>
                                    </div>
                                    <div class="r">
                                        @if($chg !== null)
                                            <span class="kp-chip {{ $chg >= 0 ? 'ok' : 'bad' }}">{{ $chg >= 0 ? '+' : '' }}{{ $chg }}%</span>
                                        @elseif($i === $revs->count() - 1)
                                            <span class="kp-chip warn">First</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="kp-empty">No salary changes recorded yet.</div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- BANK & IDS --}}
        <div class="tab-pane fade" id="bank">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-building-house"></i>Bank account</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Name as per bank', $user->bank_account_name],
                        ['Bank', $user->bank_name],
                        ['Branch', $user->bank_branch_name],
                        ['Account type', $user->bank_account_type ? ucfirst($user->bank_account_type) : null],
                        ['Account number', $masked($user->bank_account_number), 'html'],
                        ['IFSC', $user->ifsc_code],
                    ]])
                </div>
            </div>
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-shield-quarter"></i>Statutory</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['PF', '<span class="kp-chip '.($user->pf_status ? 'ok' : 'warn').'">'.($user->pf_status ? 'Active' : 'Not active').'</span>', 'html'],
                        ['PF joining date', F::date($user->pf_joining_date)],
                        ['UAN', $user->uan_number],
                        ['PAN', $user->pan_no ? strtoupper($user->pan_no) : null],
                        ['Aadhaar', $masked($user->aadhar_no), 'html'],
                    ]])
                </div>
            </div>
        </div>

        {{-- DOCUMENTS --}}
        <div class="tab-pane fade" id="documents">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-envelope-open"></i>Offer and joining letters</h2></div>
                <div class="kp-panel-b">
                    <ul class="kp-list">
                        @foreach([
                            ['Offer letter', $user->offer_letter_sent, $user->offer_letter_acknowledged, $user->offer_letter_file],
                            ['Joining letter', $user->joining_letter_sent, $user->joining_letter_acknowledged, $user->joining_letter_file],
                        ] as [$title, $sent, $ack, $file])
                            <li>
                                <div class="ic"><i class="bx bx-file"></i></div>
                                <div class="bd">
                                    <div class="t">{{ $title }}</div>
                                    <div class="m">Sent {!! $yesNo($sent) !!} &nbsp; Signed {!! $yesNo($ack) !!}</div>
                                </div>
                                <div class="r">
                                    @if($file)
                                        <a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/'.$file) }}" target="_blank">Open</a>
                                    @else
                                        <span class="text-muted">No file</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @php $issued = \Illuminate\Support\Facades\Schema::hasTable('issued_letters') ? \App\Models\IssuedLetter::where('user_id', $user->id)->latest('letter_date')->get() : collect(); @endphp
            <div class="kp-panel">
                <div class="kp-panel-h">
                    <h2><i class="bx bx-envelope"></i>Letters issued</h2>
                    @if(\App\Services\LeaveService::isHr($me))
                        <a href="{{ route('letters.create', ['user_id' => $user->id]) }}" class="btn btn-sm btn-outline-primary">Issue letter</a>
                    @endif
                </div>
                <div class="kp-panel-b">
                    @if($issued->count())
                        <ul class="kp-list">
                            @foreach($issued as $l)
                                <li>
                                    <div class="ic"><i class="bx bx-file"></i></div>
                                    <div class="bd"><div class="t">{{ $l->subject ?: ucfirst($l->type).' letter' }}</div><div class="m">{{ $l->letter_date->format('d M Y') }} · {{ $l->reference }}</div></div>
                                    <div class="r"><a class="btn btn-sm btn-outline-primary" href="{{ route('letters.pdf', $l) }}" target="_blank">Open</a></div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="kp-empty">No letters issued from RealtySuite yet.</div>
                    @endif
                </div>
            </div>

            <div class="kp-panel">
                <div class="kp-panel-h">
                    <h2><i class="bx bx-folder"></i>Previous employment</h2>
                    <span class="kp-note">{{ $user->previousEmploymentDocuments->count() }} files</span>
                </div>
                <div class="kp-panel-b">
                    @if($user->previousEmploymentDocuments->count())
                        <ul class="kp-list">
                            @foreach($user->previousEmploymentDocuments as $doc)
                                <li>
                                    <div class="ic"><i class="bx bx-paperclip"></i></div>
                                    <div class="bd">
                                        <div class="t">{{ basename($doc->file_path) }}</div>
                                        <div class="m">Added {{ F::date($doc->created_at) }}</div>
                                    </div>
                                    <div class="r"><a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">Open</a></div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="kp-empty">No documents uploaded.@if($canEdit) <a href="{{ route('users.edit', $user->id) }}#sec-documents">Upload</a>@endif</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- HISTORY --}}
        <div class="tab-pane fade" id="history">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-git-branch"></i>Reporting managers</h2></div>
                <div class="kp-panel-b" style="padding-top:12px">
                    @if($user->reportingManagerHistories->count())
                        <ul class="kp-timeline">
                            @foreach($user->reportingManagerHistories as $h)
                                <li class="{{ $h->effective_to ? '' : 'now' }}">
                                    <div class="t">
                                        @if($h->manager)<a href="{{ route('users.show', $h->manager->id) }}">{{ $h->manager->name }}</a>@else No manager @endif
                                        @unless($h->effective_to)<span class="kp-chip ok ms-1">Current</span>@endunless
                                    </div>
                                    <div class="m">
                                        {{ F::date($h->effective_from) }} – {{ $h->effective_to ? F::date($h->effective_to) : 'today' }}
                                        @if($h->changedBy) · changed by {{ $h->changedBy->name }}@endif
                                    </div>
                                    @if($h->remarks)<div class="m">{{ $h->remarks }}</div>@endif
                                </li>
                            @endforeach
                        </ul>
                        @php($first = $user->reportingManagerHistories->last())
                        @if($first && $user->joining_date && $first->effective_from?->gt(\Carbon\Carbon::parse($user->joining_date)))
                            <div class="kp-empty pt-0">Before {{ $first->effective_from->format('d M Y') }}: not recorded.</div>
                        @endif
                    @else
                        <div class="kp-empty">No manager recorded yet.</div>
                    @endif
                </div>
            </div>

            @if($user->reporteeHistories->count())
                <div class="kp-panel">
                    <div class="kp-panel-h">
                        <h2><i class="bx bx-group"></i>People who reported to {{ $user->first_name ?: 'them' }}</h2>
                        <span class="kp-note">{{ $user->reporteeHistories->count() }}</span>
                    </div>
                    <div class="kp-panel-b">
                        <ul class="kp-list">
                            @foreach($user->reporteeHistories as $h)
                                <li>
                                    <div class="bd">
                                        <div class="t">@if($h->employee)<a href="{{ route('users.show', $h->employee->id) }}">{{ $h->employee->name }}</a>@else N/A @endif</div>
                                        <div class="m">{{ F::date($h->effective_from) }} – {{ $h->effective_to ? F::date($h->effective_to) : 'today' }}</div>
                                    </div>
                                    <div class="r">@unless($h->effective_to)<span class="kp-chip ok">Current</span>@endunless</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if($user->exitHistories->count())
                <div class="kp-panel">
                    <div class="kp-panel-h"><h2><i class="bx bx-log-out-circle"></i>Exit records</h2></div>
                    <div class="kp-panel-b">
                        <ul class="kp-list">
                            @foreach($user->exitHistories->sortByDesc('id') as $x)
                                <li>
                                    <div class="bd">
                                        <div class="t">{{ $x->exit_type ?: 'Exit' }} · {{ F::date($x->exit_date) ?? 'no date' }}</div>
                                        <div class="m">{{ $x->exit_reason }}{{ $x->remarks ? ' · '.$x->remarks : '' }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        {{-- PERSONAL --}}
        <div class="tab-pane fade" id="personal">
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-user-circle"></i>Personal</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Blood group', $user->blood_group],
                        ['Marital status', $user->marital_status ? ucfirst($user->marital_status) : null],
                        ['Marriage date', F::date($user->marriage_date)],
                        ['Spouse name', $user->spouse_name],
                        ['Education', $user->education_qualification],
                        ['Languages', $user->languages_known],
                    ]])
                </div>
            </div>
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-map"></i>Address</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Current address', $user->communication_address],
                        ['Permanent address', $user->permanent_address],
                    ]])
                </div>
            </div>
            <div class="kp-panel">
                <div class="kp-panel-h"><h2><i class="bx bx-first-aid"></i>Emergency</h2></div>
                <div class="kp-panel-b">
                    @include('users.partials.fields', ['items' => [
                        ['Contact person', $user->emergency_contact_name],
                        ['Relationship', $user->emergency_contact_relationship],
                        ['Number', $user->emergency_contact_number ? '<a href="tel:+'.e(F::intlPhone($user->emergency_contact_number)).'">'.e($user->emergency_contact_number).'</a>' : null, 'html'],
                        ['Parents\' contact', $user->parents_contact],
                    ]])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function kpReveal(btn) {
    var span = btn.previousElementSibling;
    if (!span) return;
    if (btn.textContent === 'Show') { span.dataset.masked = span.textContent; span.textContent = span.dataset.full; btn.textContent = 'Hide'; }
    else { span.textContent = span.dataset.masked; btn.textContent = 'Show'; }
}
document.addEventListener('DOMContentLoaded', function () {
    var key = 'kpProfileTab';
    document.querySelectorAll('#userTabs [data-bs-toggle="tab"]').forEach(function (el) {
        el.addEventListener('shown.bs.tab', function (e) {
            try { localStorage.setItem(key, e.target.getAttribute('data-bs-target')); } catch (err) {}
            e.target.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
        });
    });
    var saved = null;
    try { saved = location.hash ? '#' + location.hash.slice(1) : localStorage.getItem(key); } catch (err) {}
    var btn = saved && document.querySelector('#userTabs [data-bs-target="' + saved + '"]');
    if (btn && window.bootstrap) new bootstrap.Tab(btn).show();
});
</script>
@endsection
