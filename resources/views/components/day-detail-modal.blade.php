@props([
    'endpoint' => null,
])

@php
    $endpoint ??= url('/reports/days');
@endphp

<div id="day-detail-modal" class="fixed inset-0 z-50 hidden" data-endpoint="{{ $endpoint }}" role="dialog" aria-modal="true">
    <div data-day-backdrop class="absolute inset-0 bg-stone-900/40"></div>
    <div class="relative flex h-full items-center justify-center p-4">
        <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg border border-stone-200 bg-white p-5 shadow-lg">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-stone-900">جزئیات روز کاری</h2>
                    <p data-day-date class="text-sm text-stone-500"></p>
                </div>
                <button type="button" data-day-close class="text-sm text-stone-500 hover:text-stone-800">بستن</button>
            </div>

            <p data-day-loading class="hidden text-sm text-stone-500">در حال بارگذاری...</p>
            <p data-day-error class="hidden text-sm text-red-700"></p>

            <div data-day-body class="hidden space-y-5">
                <dl class="grid gap-3 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-stone-500">شروع</dt>
                        <dd data-day-started>—</dd>
                    </div>
                    <div>
                        <dt class="text-stone-500">پایان</dt>
                        <dd data-day-closed>—</dd>
                    </div>
                    <div>
                        <dt class="text-stone-500">ساعات کار</dt>
                        <dd data-day-hours>—</dd>
                    </div>
                </dl>

                <div>
                    <h3 class="mb-2 text-sm font-medium text-stone-800">چک‌لیست وظایف</h3>
                    <div data-day-tasks></div>
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
                    return { mark: '✓', classes: 'border-emerald-700 text-emerald-700' };
                }

                if (task.status === 'not_done') {
                    return { mark: '✕', classes: 'border-red-700 text-red-700' };
                }

                if (task.status === 'extra' || task.is_extra) {
                    return { mark: '+', classes: 'border-slate-600 text-slate-600' };
                }

                return { mark: '○', classes: 'border-stone-400 text-stone-400' };
            };

            const openModal = () => modal.classList.remove('hidden');
            const closeModal = () => modal.classList.add('hidden');

            const renderTasks = (tasks) => {
                if (! Array.isArray(tasks) || tasks.length === 0) {
                    tasksEl.innerHTML = '<p class="text-sm text-stone-500">وظیفه‌ای ثبت نشده است.</p>';
                    return;
                }

                tasksEl.innerHTML = tasks.map((task) => {
                    const { mark, classes } = taskMark(task);
                    const reason = task.not_done_reason_label
                        ? `<p class="mt-1 text-xs text-stone-500">دلیل: ${escapeHtml(task.not_done_reason_label)}${task.not_done_note ? ' — ' + escapeHtml(task.not_done_note) : ''}</p>`
                        : '';

                    return `
                        <div class="flex items-start gap-3 border-b border-stone-100 py-3 last:border-b-0">
                            <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded border text-xs ${classes}">${mark}</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-sm text-stone-800">${escapeHtml(task.title)}</p>
                                    <span class="inline-flex items-center rounded-full border border-stone-200 bg-stone-50 px-2 py-0.5 text-xs text-stone-700">${escapeHtml(task.status_label)}</span>
                                    ${task.is_extra ? '<span class="text-xs text-slate-500">اضافه</span>' : ''}
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
