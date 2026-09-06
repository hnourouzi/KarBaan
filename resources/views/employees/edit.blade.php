<x-layouts.app title="ویرایش کارمند | کاربان">
    <x-ui.page-header :title="'ویرایش '.$employee->name" description="به‌روزرسانی اطلاعات حساب کاربری" />

    <x-card class="max-w-xl">
        <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <x-input name="name" label="نام" :value="$employee->name" required />
            <x-input name="email" type="email" label="ایمیل" :value="$employee->email" required />
            <x-input name="password" type="password" label="رمز عبور جدید (اختیاری)" />
            <x-input name="job_title" label="سمت" :value="$employee->job_title" />
            <x-select name="role" label="نقش">
                @foreach (\App\Enums\UserRole::cases() as $role)
                    <option value="{{ $role->value }}" @selected(old('role', $employee->role->value) === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </x-select>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $employee->is_active)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/20">
                حساب فعال باشد
            </label>
            <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-5">
                <x-button>ذخیره</x-button>
                <x-button href="{{ route('employees.index') }}" variant="secondary">انصراف</x-button>
            </div>
        </form>

        @can('delete', $employee)
            <form method="POST" action="{{ route('employees.destroy', $employee) }}" class="mt-6 border-t border-slate-100 pt-5" onsubmit="return confirm('حذف این کاربر قطعی است؟')">
                @csrf
                @method('DELETE')
                <x-button variant="danger">حذف کارمند</x-button>
            </form>
        @endcan
    </x-card>
</x-layouts.app>
