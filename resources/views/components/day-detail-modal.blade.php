@props([
    'endpoint' => null,
])

@php
    $endpoint ??= url('/reports/days');
@endphp

<div id="day-detail-modal" class="fixed inset-0 z-50 hidden" data-endpoint="{{ $endpoint }}" role="dialog" aria-modal="true">
    <div data-day-backdrop class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div class="relative flex h-full items-center justify-center p-4">
        <div class="max-h-[90vh] w-full max-w-2xl overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">جزئیات روز کاری</h2>
                    <p data-day-date class="mt-1 text-sm text-slate-500"></p>
                </div>
                <button type="button" data-day-close class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800" aria-label="بستن">
                    <x-ui.icon name="x-mark" class="h-5 w-5" />
                </button>
            </div>

            <div class="overflow-y-auto px-6 py-5">
                <p data-day-loading class="hidden text-sm text-slate-500">در حال بارگذاری...</p>
                <p data-day-error class="hidden rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700"></p>

                <div data-day-body class="hidden space-y-6">
                    <dl class="grid gap-4 rounded-xl bg-slate-50 p-4 text-sm sm:grid-cols-3">
                        <div>
                            <dt class="text-xs font-medium text-slate-500">شروع</dt>
                            <dd class="mt-1 font-semibold text-slate-900" data-day-started>—</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">پایان</dt>
                            <dd class="mt-1 font-semibold text-slate-900" data-day-closed>—</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">ساعات کار</dt>
                            <dd class="mt-1 font-semibold text-slate-900" data-day-hours>—</dd>
                        </div>
                    </dl>

                    <div>
                        <h3 class="mb-3 text-sm font-semibold text-slate-900">چک‌لیست وظایف</h3>
                        <div class="space-y-2" data-day-tasks></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    <script>
        (function () {
            const modal = document.getElementById('day-detail-modal');

            if (! modal || modal.dataset.bound === '1') {
                return;
            }

            modal.dataset.bound = '1';

            const endpoint = modal.dataset.endpoint;
            const loading = modal.querySelector('[data-day-loading]');
            const error = modal.querySelector('[data-day-error]');
            const body = modal.querySelector('[data-day-body]');
            const dateEl = modal.querySelector('[data-day-date]');
            const startedEl = modal.querySelector('[data-day-started]');
            const closedEl = modal.querySelector('[data-day-closed]');
            const hoursEl = modal.querySelector('[data-day-hours]');
            const tasksEl = modal.querySelector('[data-day-tasks]');

            const escapeHtml = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;');

            const taskMark = (task) => {
                if (task.status === 'done') {
                    return { mark: '✓', classes: 'bg-emerald-50 text-emerald-600 ring-emerald-600/20' };
                }

                if (task.status === 'not_done') {
                    return { mark: '✕', classes: 'bg-rose-50 text-rose-600 ring-rose-600/20' };
                }

                if (task.status === 'extra' || task.is_extra) {
                    return { mark: '+', classes: 'bg-slate-100 text-slate-600 ring-slate-500/15' };
                }

                return { mark: '○', classes: 'bg-slate-50 text-slate-400 ring-slate-300/50' };
            };

            const badgeClass = (task) => {
                if (task.status === 'done') return 'kb-badge kb-badge-success';
                if (task.status === 'not_done') return 'kb-badge kb-badge-danger';
                if (task.status === 'extra' || task.is_extra) return 'kb-badge kb-badge-info';
                return 'kb-badge kb-badge-muted';
            };

            const openModal = () => modal.classList.remove('hidden');
            const closeModal = () => modal.classList.add('hidden');

            const renderTasks = (tasks) => {
                if (! Array.isArray(tasks) || tasks.length === 0) {
                    tasksEl.innerHTML = '<p class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">وظیفه‌ای ثبت نشده است.</p>';
                    return;
                }

                tasksEl.innerHTML = tasks.map((task) => {
                    const { mark, classes } = taskMark(task);
                    const reason = task.not_done_reason_label
                        ? `<p class="mt-1.5 text-xs text-slate-500">دلیل: ${escapeHtml(task.not_done_reason_label)}${task.not_done_note ? ' — ' + escapeHtml(task.not_done_note) : ''}</p>`
                        : '';

                    return `
                        <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/40 px-4 py-3">
                            <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg ring-1 ${classes}">${mark}</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-medium text-slate-900">${escapeHtml(task.title)}</p>
                                    <span class="${badgeClass(task)}">${escapeHtml(task.status_label)}</span>
                                    ${task.is_extra ? '<span class="text-xs font-medium text-slate-500">اضافه</span>' : ''}
                                </div>
                                ${reason}
                            </div>
                        </div>
                    `;
                }).join('');
            };

            const loadDay = async (id) => {
                openModal();
                loading.classList.remove('hidden');
                error.classList.add('hidden');
                body.classList.add('hidden');
                error.textContent = '';

                try {
                    const response = await fetch(`${endpoint}/${id}`, {
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    const payload = await response.json();

                    if (! response.ok) {
                        throw new Error(payload.message || 'دسترسی به این روز مجاز نیست.');
                    }

                    const detail = payload.data;
                    dateEl.textContent = detail.plan_date_full || '';
                    startedEl.textContent = detail.started_at || '—';
                    closedEl.textContent = detail.closed_at || '—';
                    hoursEl.textContent = detail.hours_worked ?? '—';
                    renderTasks(detail.tasks);
                    body.classList.remove('hidden');
                } catch (err) {
                    error.textContent = err instanceof Error ? err.message : 'بارگذاری جزئیات روز ناموفق بود.';
                    error.classList.remove('hidden');
                } finally {
                    loading.classList.add('hidden');
                }
            };

            document.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-open-day]');

                if (trigger) {
                    event.preventDefault();
                    loadDay(trigger.getAttribute('data-open-day'));
                    return;
                }

                if (event.target.closest('[data-day-close], [data-day-backdrop]')) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && ! modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
@endonce
