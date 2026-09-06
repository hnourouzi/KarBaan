<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=vazirmatn:400,500,600,700">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@0.9.12/dist/jalalidatepicker.min.css">
    <style>[x-cloak]{display:none!important}</style>
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
    <div class="min-h-screen">
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-4">
                <div>
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-stone-900">کاربان</a>
                    <p class="text-xs text-stone-500">ثبت برنامه روزانه و ساعات کار</p>
                </div>
                <nav class="flex flex-wrap items-center gap-4 text-sm">
                    <a href="{{ route('dashboard') }}" class="text-stone-600 hover:text-stone-900">داشبورد</a>
                    <a href="{{ route('history.index') }}" class="text-stone-600 hover:text-stone-900">سوابق من</a>
                    @if (auth()->user()->canManageTeam())
                        <a href="{{ route('reports.employee') }}" class="text-stone-600 hover:text-stone-900">گزارش کارمند</a>
                        <a href="{{ route('reports.index') }}" class="text-stone-600 hover:text-stone-900">گزارش تیم</a>
                        <a href="{{ route('employees.index') }}" class="text-stone-600 hover:text-stone-900">کارکنان</a>
                    @endif
                    <span class="text-stone-400">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-stone-600 hover:text-stone-900">خروج</button>
                    </form>
                </nav>
            </div>
        </header>

        <x-end-of-day-reminder />

        <main class="mx-auto max-w-6xl px-4 py-8">
            <x-flash />
            {{ $slot }}
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@0.9.12/dist/jalalidatepicker.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dayDetailModal', (endpoint) => ({
                open: false,
                loading: false,
                error: null,
                detail: null,
                async show(id) {
                    if (! id) {
                        return;
                    }

                    this.open = true;
                    this.loading = true;
                    this.error = null;
                    this.detail = null;

                    try {
                        const response = await fetch(`${endpoint}/${id}`, {
                            headers: { Accept: 'application/json' },
                            credentials: 'same-origin',
                        });
                        const payload = await response.json();

                        if (! response.ok) {
                            this.error = payload.message || 'دسترسی به این روز مجاز نیست.';
                            return;
                        }

                        this.detail = payload.data;
                    } catch (error) {
                        this.error = 'بارگذاری جزئیات روز ناموفق بود.';
                    } finally {
                        this.loading = false;
                    }
                },
                close() {
                    this.open = false;
                },
                taskMark(task) {
                    if (task.status === 'done') {
                        return '✓';
                    }

                    if (task.status === 'not_done') {
                        return '✕';
                    }

                    if (task.status === 'extra' || task.is_extra) {
                        return '+';
                    }

                    return '○';
                },
                taskMarkClass(task) {
                    if (task.status === 'done') {
                        return 'border-emerald-700 text-emerald-700';
                    }

                    if (task.status === 'not_done') {
                        return 'border-red-700 text-red-700';
                    }

                    if (task.status === 'extra' || task.is_extra) {
                        return 'border-slate-600 text-slate-600';
                    }

                    return 'border-stone-400 text-stone-400';
                },
            }));
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (window.jalaliDatepicker) {
                jalaliDatepicker.startWatch({
                    time: false,
                    hideAfterChange: true,
                    autoHide: true,
                    persianDigits: false,
                });
            }
        });

        const addTaskButton = document.querySelector('[data-add-task]');
        if (addTaskButton) {
            addTaskButton.addEventListener('click', () => {
                const list = document.querySelector('[data-task-list]');
                if (!list) return;
                const row = document.createElement('div');
                row.className = 'flex gap-2';
                row.innerHTML = `
                    <input type="text" name="titles[]" required maxlength="255"
                        class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm text-stone-800 outline-none focus:border-stone-500"
                        placeholder="عنوان وظیفه">
                    <button type="button" data-remove-task
                        class="rounded-md border border-stone-300 px-3 text-sm text-stone-600 hover:bg-stone-100">حذف</button>
                `;
                list.appendChild(row);
            });
        }
        document.addEventListener('click', (event) => {
            const target = event.target;
            if (target instanceof HTMLElement && target.matches('[data-remove-task]')) {
                target.closest('div')?.remove();
            }
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</body>
</html>
