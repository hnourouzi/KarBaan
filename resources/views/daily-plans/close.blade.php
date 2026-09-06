<x-layouts.app title="بستن روز | کاربان">
    <x-ui.page-header
        title="بستن روز کاری"
        description="وضعیت هر وظیفه برنامه‌ریزی‌شده را مشخص کنید. برای موارد انجام‌نشده دلیل الزامی است."
    />

    <x-card class="max-w-3xl">
        <form method="POST" action="{{ route('daily-plans.close.store', $plan) }}" class="space-y-6">
            @csrf

            @foreach ($plan->tasks->where('is_extra', false) as $task)
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-400 ring-1 ring-slate-200">
                            <x-ui.icon name="clipboard" class="h-4 w-4" />
                        </span>
                        <p class="text-sm font-semibold text-slate-900">{{ $task->title }}</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
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

            <div class="space-y-3 rounded-xl border border-dashed border-slate-200 bg-slate-50/40 p-5">
                <p class="text-sm font-semibold text-slate-800">وظایف اضافه انجام‌شده</p>
                <input type="text" name="extras[0][title]" value="{{ old('extras.0.title') }}" placeholder="عنوان وظیفه اضافه (اختیاری)" class="kb-input">
                <input type="text" name="extras[1][title]" value="{{ old('extras.1.title') }}" placeholder="عنوان وظیفه اضافه (اختیاری)" class="kb-input">
            </div>

            <div class="space-y-1.5">
                <label for="notes" class="block text-sm font-medium text-slate-700">یادداشت پایان روز</label>
                <textarea name="notes" id="notes" rows="3" class="kb-input">{{ old('notes', $plan->notes) }}</textarea>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-5">
                <x-button>ثبت و بستن روز</x-button>
                <x-button href="{{ route('dashboard') }}" variant="secondary">بازگشت</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
