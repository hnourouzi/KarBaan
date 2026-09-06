@if ($plan)
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-cloak
        class="border-b border-amber-200/80 bg-amber-50/90"
        role="status"
    >
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex items-start gap-3">
                <x-ui.icon name="exclamation" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
                <p class="text-sm font-medium text-amber-900">
                    یادت نره امروزت رو ببندی و وضعیت کارهات رو ثبت کنی.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-button href="{{ route('daily-plans.close', $plan) }}" variant="secondary" size="sm">
                    بستن روز کاری
                </x-button>
                <button
                    type="button"
                    @click="visible = false"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-amber-700 transition hover:bg-amber-100"
                    aria-label="بستن اعلان"
                >
                    <x-ui.icon name="x-mark" class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>
@endif
