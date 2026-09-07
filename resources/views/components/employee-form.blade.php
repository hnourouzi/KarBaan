@props([
    'mode' => 'create',
    'action',
])

@php
    $isEdit = $mode === 'edit';
    $prefix = $isEdit ? 'employee-edit' : 'employee-create';
    $remember = old('employee_form') === ($isEdit ? 'update' : 'create');
@endphp

<form method="POST" action="{{ $action }}" class="space-y-4" {{ $attributes }}>
    @csrf
    @if ($isEdit)
        @method('PUT')
        <input type="hidden" name="employee_form" value="update">
        <input type="hidden" name="employee_id" value="{{ old('employee_id') }}">
    @else
        <input type="hidden" name="employee_form" value="create">
    @endif

    <x-input :id="$prefix.'-name'" name="name" label="نام" :remember="$remember" x-ref="{{ $isEdit ? 'editName' : 'createName' }}" required />
    <x-input :id="$prefix.'-email'" name="email" type="email" label="ایمیل" :remember="$remember" required />
    <x-input
        :id="$prefix.'-password'"
        name="password"
        type="password"
        :label="$isEdit ? 'رمز عبور جدید (اختیاری)' : 'رمز عبور'"
        autocomplete="new-password"
        :remember="$remember"
        @required(! $isEdit)
    />
    <x-input :id="$prefix.'-job-title'" name="job_title" label="سمت" :remember="$remember" />
    <x-select :id="$prefix.'-role'" name="role" label="نقش" :remember="$remember" required>
        @foreach (\App\Enums\UserRole::cases() as $role)
            <option value="{{ $role->value }}" @selected($remember && old('role') === $role->value)>{{ $role->label() }}</option>
        @endforeach
    </x-select>
    <label for="{{ $prefix }}-is-active" class="flex items-center gap-2 text-sm text-slate-600">
        <input type="hidden" name="is_active" value="0">
        <input
            id="{{ $prefix }}-is-active"
            type="checkbox"
            name="is_active"
            value="1"
            @checked($remember ? old('is_active', ! $isEdit) : ! $isEdit)
            class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/20"
        >
        حساب فعال باشد
    </label>
    <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-5">
        <x-button>{{ $isEdit ? 'ذخیره' : 'ثبت' }}</x-button>
        <x-button type="button" variant="secondary" x-on:click="{{ $isEdit ? 'closeEdit()' : 'closeCreate()' }}">انصراف</x-button>
    </div>
</form>
