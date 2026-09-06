@if (session('success'))
    <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200/80 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
        <x-ui.icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
        <p>{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200/80 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm">
        <x-ui.icon name="x-circle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
        <p>{{ session('error') }}</p>
    </div>
@endif
