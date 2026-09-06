@if ($plan)
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-cloak
        class="border-b border-amber-200 bg-amber-50"
        role="status"
    >
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3 text-sm">
            <p class="text-amber-900">
                یادت نره امروزت رو ببندی و وضعیت کارهات رو ثبت کنی.
            </p>
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('daily-plans.close', $plan) }}"
                    class="inline-flex items-center rounded-md border border-amber-300 bg-white px-3 py-1.5 font-medium text-amber-900 hover:bg-amber-100"
                >
                    بستن روز کاری
                </a>
                <button
                    type="button"
                    @click="visible = false"
                    class="text-amber-700 hover:text-amber-900"
                    aria-label="بستن اعلان"
                >
                    ✕
                </button>
            </div>
        </div>
    </div>
@endif
