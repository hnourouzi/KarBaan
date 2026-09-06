<x-layouts.app title="ثبت برنامه امروز | کاربان">
    <x-ui.page-header
        title="ثبت برنامه روزانه"
        description="وظایفی را که امروز قصد انجام آن‌ها را دارید وارد کنید."
    />

    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('daily-plans.store') }}" class="space-y-5">
            @csrf

            <div class="space-y-3" data-task-list>
                <label class="block text-sm font-medium text-slate-700">وظایف برنامه‌ریزی‌شده</label>
                @foreach (old('titles', ['']) as $title)
                    <input
                        type="text"
                        name="titles[]"
                        value="{{ $title }}"
                        required
                        maxlength="255"
                        placeholder="عنوان وظیفه"
                        class="kb-input"
                    >
                @endforeach
            </div>
            @error('titles')
                <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
            @error('titles.*')
                <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror

            <button type="button" data-add-task class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:text-brand-700">
                <x-ui.icon name="plus-circle" class="h-4 w-4" />
                افزودن ردیف
            </button>

            <div class="space-y-1.5">
                <label for="notes" class="block text-sm font-medium text-slate-700">یادداشت (اختیاری)</label>
                <textarea name="notes" id="notes" rows="3" class="kb-input">{{ old('notes') }}</textarea>
            </div>

            <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-5">
                <x-button>شروع روز</x-button>
                <x-button href="{{ route('dashboard') }}" variant="secondary">انصراف</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
