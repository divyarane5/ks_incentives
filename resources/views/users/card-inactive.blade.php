@php
    $bu = $user->businessUnit;
    $brand = $bu?->theme_color ?: '#011e48';
    $accent = $bu?->secondary_color ?: '#c3b246';
    $website = $bu?->domain ?? null;
    if ($website && ! \Illuminate\Support\Str::startsWith($website, ['http://', 'https://'])) {
        $website = 'https://'.$website;
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Card not active{{ $bu?->name ? ' · '.$bu->name : '' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #eef1f6; font-family: "Public Sans", system-ui, sans-serif; color: #1f2a3d; padding: 16px; box-sizing: border-box; }
        .box { background: #fff; max-width: 380px; width: 100%; border-radius: 20px; padding: 28px 22px; text-align: center; box-shadow: 0 10px 30px rgba(16, 24, 40, .08); border-top: 6px solid {{ $accent }}; }
        h1 { color: {{ $brand }}; font-size: 1.2rem; margin: 0 0 8px; }
        p { color: #6b778c; line-height: 1.5; margin: 0 0 18px; }
        a { display: inline-block; padding: 12px 18px; border-radius: 12px; background: {{ $brand }}; color: #fff; font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>
    <div class="box">
        <h1>This card is no longer active</h1>
        <p>{{ trim($user->first_name.' '.$user->last_name) }} is no longer with {{ $bu?->name ?? 'us' }}. Please contact the company for help.</p>
        @if($website)<a href="{{ $website }}">Visit {{ $bu->name }}</a>@endif
    </div>
</body>
</html>
