{{-- RealtySuite menu, same modules and permissions as the back-office sidebar. --}}
@php
    $u = auth()->user();
    $isSAorAdmin = isSuperAdmin() || isAdmin();
    $bu = $u->businessUnit?->code;
    $krea = $isSAorAdmin || $bu === 'KREA';
    $ai = $isSAorAdmin || $bu === 'AI';
    $r = request()->route()?->getName() ?? '';
    $groups = [];
    $groups['KeyLeads'] = [
        ['Today', route('crm.dashboard'), $r === 'crm.dashboard'],
        ['Leads', route('crm.leads.index'), str_starts_with($r, 'crm.leads.')],
        ['Projects', route('crm.projects.index'), str_starts_with($r, 'crm.projects.')],
        ['Properties', route('crm.properties.index'), str_starts_with($r, 'crm.properties.')],
        ['Reports', route('crm.reports.index'), str_starts_with($r, 'crm.reports.')],
    ];
    if ($u->isAdmin()) {
        $groups['KeyLeads'][] = ['Team & settings', route('crm.team.index'), str_starts_with($r, 'crm.team.')];
    }
    if ($u->can('module-keybookings')) {
        $groups['KeyBookings'] = array_values(array_filter([
            $krea && $u->can('booking-view') ? ['Bookings', route('booking.index'), false] : null,
            $krea && $u->can('project-view') ? ['Projects & brokerage', route('project.index'), false] : null,
            $krea && $u->can('developer-view') ? ['Developers', route('developer.index'), false] : null,
            $ai && ($isSAorAdmin || canAccessModule('mandate_project-view', ['AI'])) ? ['Mandate', route('mandate_projects.index'), false] : null,
        ]));
    }
    if ($u->can('module-keyaccounts')) {
        $groups['KeyAccounts'] = array_values(array_filter([
            $krea && $u->can('payment-view') ? ['Brokerage payments', route('brokerage-payments.index'), false] : null,
            $krea && $u->can('company-bank-view') ? ['Company bank accounts', route('company-bank.index'), false] : null,
            $u->can('mandate-booking-view') ? ['Brokerage ledgers', url('/brokerage-ledgers'), false] : null,
        ]));
    }
    if ($u->can('module-keypeople')) {
        $groups['KeyPeople'] = array_values(array_filter([
            $u->can('user-view') ? ['Users', route('users.index'), false] : null,
            $isSAorAdmin ? ['Module access', route('module-access.index'), false] : null,
        ]));
    }
    if ($u->can('module-keyearn')) {
        $groups['KeyEarn'] = array_values(array_filter([
            ['My incentive', route('my-incentive'), false],
            $u->can('incentive-ledger-view') ? ['Ledger & freeze', route('incentive-ledger.index'), false] : null,
        ]));
    }
    $logo = asset('assets/img/logo/realtysuite-white.svg');
@endphp
<div class="flex h-full flex-col overflow-y-auto bg-[var(--rs-theme)] text-white">
    <a href="{{ route('dashboard') }}" class="block px-5 py-5">
        <img src="{{ $logo }}" alt="RealtySuite" style="height:44px;width:auto;max-width:100%">
    </a>
    <nav class="flex-1 px-3 pb-6 text-sm">
        <a href="{{ route('dashboard') }}" class="mb-2 flex items-center rounded-lg px-3 py-2.5 font-medium hover:bg-[var(--rs-accent)]">Dashboard</a>
        @foreach ($groups as $title => $links)
            @continue(empty($links))
            <div class="mt-4 mb-1 flex items-center gap-2 px-3 text-[11px] font-semibold tracking-wider text-white/60 uppercase">
                <span class="h-px w-3 bg-white/40"></span>{{ $title }}
            </div>
            @foreach ($links as [$label, $href, $active])
                <a href="{{ $href }}" class="flex items-center rounded-lg px-3 py-2 {{ $active ? 'bg-[var(--rs-accent)] font-semibold' : 'text-white/90 hover:bg-white/10' }}">{{ $label }}</a>
            @endforeach
        @endforeach
    </nav>
</div>
