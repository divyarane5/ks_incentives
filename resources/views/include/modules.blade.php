{{-- RealtySuite: the modules this person can open --}}
@php
    $mods = \App\Support\Modules::for(auth()->user());
    $links = [
        'keyleads' => auth()->user()->usesCrm() ? route('crm.dashboard') : null,
        'keybookings' => auth()->user()->can('booking-view') ? route('booking.index') : (auth()->user()->can('mandate_project-view') ? route('mandate_projects.index') : null),
        'keyaccounts' => auth()->user()->can('payment-view') ? route('brokerage-payments.index') : null,
        'keypeople' => auth()->user()->can('user-view') ? route('users.index') : null,
        'keyearn' => route('my-incentive'),
    ];
@endphp
@if (count($mods))
<div class="mb-2 text-muted small text-uppercase fw-semibold">RealtySuite</div>
<div class="row g-3 mb-4">
    @foreach ($mods as $key)
        @php($m = \App\Support\Modules::ALL[$key])
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ $links[$key] ?? '#' }}" class="card h-100 text-body {{ ($links[$key] ?? null) ? '' : 'pe-none' }}">
                <div class="card-body py-3">
                    <i class="bx {{ $m['icon'] }} fs-3 text-primary"></i>
                    <div class="fw-semibold mt-1">{{ $m['name'] }}</div>
                    <div class="text-muted small">{{ $m['about'] }}</div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endif
