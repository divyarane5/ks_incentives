<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#072041">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.webmanifest">
    @include('include.favicon')
    <title>{{ $title ?? config('app.name') }} · {{ config('app.name') }}</title>
    @vite(['resources/css/crm.css', 'resources/js/crm.js'])
    @php($suiteUnit = auth()->user()?->businessUnit ?? \App\Models\BusinessUnit::first())
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Same colours as the back office (set per business unit) --}}
    <style>
        :root {
            --rs-theme: {{ $suiteUnit->theme_color ?? '#011e48' }};
            --rs-accent: {{ $suiteUnit->secondary_color ?? '#c3b246' }};
            --color-brand-50: color-mix(in srgb, var(--rs-theme) 7%, white);
            --color-brand-100: color-mix(in srgb, var(--rs-theme) 14%, white);
            --color-brand-200: color-mix(in srgb, var(--rs-theme) 28%, white);
            --color-brand-500: color-mix(in srgb, var(--rs-theme) 75%, white);
            --color-brand-600: color-mix(in srgb, var(--rs-theme) 88%, white);
            --color-brand-700: var(--rs-theme);
            --color-brand-800: color-mix(in srgb, var(--rs-theme) 85%, black);
            --color-gold-100: color-mix(in srgb, var(--rs-accent) 20%, white);
            --color-gold-400: color-mix(in srgb, var(--rs-accent) 85%, white);
            --color-gold-500: var(--rs-accent);
            --color-gold-600: color-mix(in srgb, var(--rs-accent) 85%, black);
            --font-sans: 'Public Sans', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-[#f5f5f9] font-sans text-slate-900 antialiased">
@auth
    {{-- RealtySuite menu: fixed on tablets and computers, a slide-in drawer on phones --}}
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 md:block">@include('crm.partials.suite-nav')</aside>
    <div data-drawer class="fixed inset-0 z-50 hidden md:hidden">
        <div data-drawer-close class="absolute inset-0 bg-black/40"></div>
        <aside class="absolute inset-y-0 left-0 w-72 max-w-[85%] shadow-xl">@include('crm.partials.suite-nav')</aside>
    </div>
@endauth
<div class="@auth md:pl-64 @endauth">
@auth
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-3">
            @unless(isset($back))
                <button type="button" data-drawer-open class="-ml-1 rounded-lg p-1 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            @endunless
            @isset($back)
                <a href="{{ $back }}" class="-ml-1 rounded-lg p-1 text-slate-500 hover:bg-slate-100" aria-label="Back">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endisset
            <h1 class="flex-1 truncate text-lg font-semibold">{{ $title ?? config('app.name') }}</h1>
            @php($unread = auth()->user()->unreadNotifications()->count())
            <a href="{{ route('crm.alerts.index') }}" class="relative rounded-lg p-1.5 text-slate-600 hover:bg-slate-100" aria-label="Alerts{{ $unread ? ' ('.$unread.' new)' : '' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4-5.7V5a2 2 0 10-4 0v.3A6 6 0 006 11v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                @if($unread)<span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
            </a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="rounded-lg px-2 py-1 text-sm text-slate-500 hover:bg-slate-100" title="Sign out">Sign out</button>
            </form>
        </div>
    </header>
@endauth

<main class="mx-auto max-w-6xl px-4 pt-4 pb-28 md:pb-10">
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if (session('warning'))
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ session('warning') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    {{ $slot ?? '' }}
    @yield('content')
</main>

@auth
    {{-- Bottom tab bar on phones --}}
    <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white pb-[env(safe-area-inset-bottom)] md:hidden">
        <div class="grid grid-cols-5 text-xs font-medium text-slate-600">
            @php($r = \Illuminate\Support\Str::after(request()->route()?->getName() ?? '', 'crm.'))
            <a href="{{ route('crm.dashboard') }}" class="flex flex-col items-center gap-0.5 py-2 {{ $r === 'dashboard' ? 'text-brand-700' : '' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"/></svg>Today</a>
            <a href="{{ route('crm.leads.index') }}" class="flex flex-col items-center gap-0.5 py-2 {{ str_starts_with($r, 'leads.') && $r !== 'leads.create' ? 'text-brand-700' : '' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 00-5-3.9M9 20H2v-2a4 4 0 015-3.9m10-6.1a3 3 0 11-6 0 3 3 0 016 0zM9 8a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Leads</a>
            <a href="{{ route('crm.leads.create') }}" class="flex flex-col items-center gap-0.5 py-2 {{ $r === 'leads.create' ? 'text-brand-700' : '' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg>Add lead</a>
            <a href="{{ route('crm.projects.index') }}" class="flex flex-col items-center gap-0.5 py-2 {{ str_starts_with($r, 'projects.') || str_starts_with($r, 'properties.') ? 'text-brand-700' : '' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>Projects</a>
            <a href="{{ route('crm.reports.index') }}" class="flex flex-col items-center gap-0.5 py-2 {{ str_starts_with($r, 'reports.') ? 'text-brand-700' : '' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 20V10m6 10V4m6 16v-7m4 7H2"/></svg>Reports</a>
        </div>
    </nav>
@endauth
</div>
</body>
</html>
