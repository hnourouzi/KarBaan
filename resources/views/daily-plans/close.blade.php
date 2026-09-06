<x-layouts.app title="بستن روز | کاربان">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">بستن روز کاری</h1>
        <p class="text-sm text-stone-500">وضعیت هر وظیفه برنامه‌ریزی‌شده را مشخص کنید. برای موارد انجام‌نشده دلیل الزامی است.</p>
    </div>

    <x-card class="max-w-3xl">
        <form method="POST" action="{{ route('daily-plans.close.store', $plan) }}" class="space-y-6">
            @csrf

            @foreach ($plan->tasks->where('is_extra', false) as $task)
                <div class="rounded-md border border-stone-200 p-4">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded border border-stone-400 text-xs">☐</span>
                        <p class="text-sm font-medium text-stone-900">{{ $task->title }}</p>
                    </div>

                    <div class="grid gap-3 md:grid-cols-3">
                        <x-select :name="'tasks['.$task->id.'][status]'" label="وضعیت">
                            @foreach (\App\Enums\TaskStatus::closeoutStatuses() as $status)
                                <option value="{{ $status->value }}" @selected(old("tasks.{$task->id}.status", $task->status->value) === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select :name="'tasks['.$task->id.'][not_done_reason]'" label="دلیل (در صورت انجام‌نشدن)">
                            <option value="">—</option>
                            @foreach (\App\Enums\NotDoneReason::cases() as $reason)
                                <option value="{{ $reason->value }}" @selected(old("tasks.{$task->id}.not_done_reason", $task->not_done_reason?->value) === $reason->value)>
                                    {{ $reason->label() }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-input :name="'tasks['.$task->id.'][not_done_note]'" label="توضیح" :value="old('tasks.'.$task->id.'.not_done_note', $task->not_done_note)" />
                    </div>
                </div>
            @endforeach

            <div class="space-y-2">
                <p class="text-sm font-medium text-stone-800">وظایف اضافه انجام‌شده</p>
                <input type="text" name="extras[0][title]" value="{{ old('extras.0.title') }}" placeholder="عنوان وظیفه اضافه (اختیاری)" class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-stone-500">
                <input type="text" name="extras[1][title]" value="{{ old('extras.1.title') }}" placeholder="عنوان وظیفه اضافه (اختیاری)" class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-stone-500">
            </div>

            <div class="space-y-1.5">
                <label for="notes" class="block text-sm text-stone-700">یادداشت پایان روز</label>
                <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-stone-500">{{ old('notes', $plan->notes) }}</textarea>
            </div>

            <div class="flex gap-2">
                <x-button>ثبت و بستن روز</x-button>
                <x-button href="{{ route('dashboard') }}" variant="secondary">بازگشت</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
