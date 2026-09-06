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
        <style type="text/tailwindcss">
            @theme {
                --font-sans: 'Vazirmatn', ui-sans-serif, system-ui, sans-serif;
            }
        </style>
    @endif
</head>
<body class="min-h-screen font-sans">
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
