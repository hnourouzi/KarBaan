<x-layouts.app title="کارمند جدید | کاربان">
    <x-ui.page-header title="ثبت کارمند" description="ایجاد حساب کاربری جدید برای عضو تیم" />

    <x-card class="max-w-xl">
        <form method="POST" action="{{ route('employees.store') }}" class="space-y-4">
            @csrf
            <x-input name="name" label="نام" required />
            <x-input name="email" type="email" label="ایمیل" required />
            <x-input name="password" type="password" label="رمز عبور" required />
            <x-input name="job_title" label="سمت" />
            <x-select name="role" label="نقش">
                @foreach (\App\Enums\UserRole::cases() as $role)
                    <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </x-select>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/20">
                حساب فعال باشد
            </label>
            <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-5">
                <x-button>ثبت</x-button>
                <x-button href="{{ route('employees.index') }}" variant="secondary">انصراف</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>
