<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=vazirmatn:400,500,600,700">
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link rel="stylesheet" href="{{ asset('css/fallback-theme.css') }}">
    @endif
</head>
<body class="min-h-screen bg-surface-muted font-sans antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <span class="mx-auto mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 text-lg font-bold text-white shadow-sm">ک</span>
                <p class="text-sm text-slate-500">سامانه ثبت برنامه روزانه کارکنان</p>
            </div>
            {{ $slot }}
        </div>
    </div>
</body>
</html>
