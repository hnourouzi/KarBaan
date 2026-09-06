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
        <link rel="stylesheet" href="{{ asset('css/fallback-theme.css') }}">
    @endif
</head>
<body class="min-h-screen font-sans">
    <div class="min-h-screen">
        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 shadow-sm backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between gap-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-sm font-bold text-white shadow-sm">ک</span>
                        <span class="hidden sm:block">
                            <span class="block text-sm font-bold text-slate-900">کاربان</span>
                            <span class="block text-xs text-slate-500">ثبت برنامه و ساعات کار</span>
                        </span>
                    </a>

                    <nav class="flex flex-1 items-center justify-center gap-1 overflow-x-auto px-2">
                        <x-ui.nav-link href="{{ route('dashboard') }}" icon="dashboard" :active="request()->routeIs('dashboard')">
                            داشبورد
                        </x-ui.nav-link>
                        <x-ui.nav-link href="{{ route('history.index') }}" icon="history" :active="request()->routeIs('history.*')">
                            سوابق من
                        </x-ui.nav-link>
                        @if (auth()->user()->canManageTeam())
                            <x-ui.nav-link href="{{ route('reports.employee') }}" icon="document" :active="request()->routeIs('reports.employee*')">
                                گزارش کارمند
                            </x-ui.nav-link>
                            <x-ui.nav-link href="{{ route('reports.index') }}" icon="chart" :active="request()->routeIs('reports.index')">
                                گزارش تیم
                            </x-ui.nav-link>
                            <x-ui.nav-link href="{{ route('employees.index') }}" icon="users" :active="request()->routeIs('employees.*')">
                                کارکنان
                            </x-ui.nav-link>
                        @endif
                    </nav>

                    <div class="flex items-center gap-2">
                        <div class="hidden items-center gap-2 rounded-lg bg-slate-100 px-3 py-1.5 sm:flex">
                            <x-ui.icon name="user" class="h-4 w-4 text-slate-500" />
                            <span class="max-w-[8rem] truncate text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900" title="خروج">
                                <x-ui.icon name="logout" class="h-4 w-4" />
                                <span class="hidden md:inline">خروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <x-end-of-day-reminder />

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
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
                        return 'bg-emerald-50 text-emerald-600 ring-emerald-600/20';
                    }

                    if (task.status === 'not_done') {
                        return 'bg-rose-50 text-rose-600 ring-rose-600/20';
                    }

                    if (task.status === 'extra' || task.is_extra) {
                        return 'bg-slate-100 text-slate-600 ring-slate-500/15';
                    }

                    return 'bg-slate-50 text-slate-400 ring-slate-300/50';
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
                        class="kb-input"
                        placeholder="عنوان وظیفه">
                    <button type="button" data-remove-task
                        class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 shadow-sm hover:bg-slate-50">حذف</button>
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
