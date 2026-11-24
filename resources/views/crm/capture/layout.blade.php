<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $form->heading ?: 'Enquire now' }}</title>
    @vite(['resources/css/crm.css'])
</head>
<body class="bg-white font-sans text-slate-900 antialiased">
<main class="mx-auto max-w-md px-4 py-6">
    @yield('content')
</main>
</body>
</html>
