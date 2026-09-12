@props(['plan'])

@php
    $sessions = $plan->workSessions;
    $openSession = $plan->openWorkSession();
    $confirmedHours = $sessions
        ->filter(fn ($session) => $session->isClosed())
        ->sum(fn ($session) => $session->durationHours());
@endphp

<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    <ul class="space-y-2">
        @forelse ($sessions as $session)
            <li class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2.5 text-sm">
                <span class="font-medium text-slate-700">
                    <x-jalali-date :date="$session->started_at" format="time" />
                    –
                    @if ($session->isClosed())
                        <x-jalali-date :date="$session->ended_at" format="time" />
                    @else
                        <span class="text-amber-700">در جریان</span>
                    @endif
                </span>
                <span class="font-semibold text-slate-900">
                    @if ($session->isClosed())
                        {{ number_format($session->durationHours(), 2) }}
                    @else
                        —
                    @endif
                </span>
            </li>
        @empty
            <li class="rounded-lg bg-slate-50 px-3 py-2.5 text-sm text-slate-500">جلسه‌ای ثبت نشده است.</li>
        @endforelse
    </ul>

    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5 text-sm">
        <span class="font-medium text-slate-500">جمع ساعات تأییدشده</span>
        <span class="font-semibold text-slate-900">{{ $sessions->whereNotNull('ended_at')->isEmpty() ? '—' : number_format($confirmedHours, 2) }}</span>
    </div>

    @if ($plan->isOpen() && auth()->id() === $plan->user_id)
        <div class="flex flex-wrap gap-2 pt-1">
            @if ($openSession)
                <form method="POST" action="{{ route('work-sessions.end', $openSession) }}">
                    @csrf
                    <x-button size="sm" variant="secondary">پایان جلسه کاری</x-button>
                </form>
            @else
                <form method="POST" action="{{ route('daily-plans.work-sessions.store', $plan) }}">
                    @csrf
                    <x-button size="sm">شروع مجدد کار</x-button>
                </form>
            @endif
        </div>
    @endif
</div>
