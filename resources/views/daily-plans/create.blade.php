<x-layouts.app title="ثبت برنامه امروز | کاربان">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">ثبت برنامه روزانه</h1>
        <p class="text-sm text-stone-500">وظایفی را که امروز قصد انجام آن‌ها را دارید وارد کنید.</p>
    </div>

    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('daily-plans.store') }}" class="space-y-4">
            @csrf

            <div class="space-y-2" data-task-list>
                <label class="block text-sm text-stone-700">وظایف برنامه‌ریزی‌شده</label>
                @foreach (old('titles', ['']) as $title)
                    <input
                        type="text"
                        name="titles[]"
                        value="{{ $title }}"
                        required
                        maxlength="255"
                        placeholder="عنوان وظیفه"
                        class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm text-stone-800 outline-none focus:border-stone-500"
                    >
                @endforeach
            </div>
            @error('titles')
                <p class="text-xs text-red-700">{{ $message }}</p>
            @enderror
            @error('titles.*')
                <p class="text-xs text-red-700">{{ $message }}</p>
            @enderror

            <button type="button" data-add-task class="text-sm text-stone-600 hover:text-stone-900">+ افزودن ردیف</button>

            <div class="space-y-1.5">
                <label for="notes" class="block text-sm text-stone-700">یادداشت (اختیاری)</label>
                <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-stone-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-2">
                <x-button>شروع روز</x-button>
                <x-button href="{{ route('dashboard') }}" variant="secondary">انصراف</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
