<x-layouts.app title="کارکنان | کاربان">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-stone-900">کارکنان</h1>
            <p class="text-sm text-stone-500">فهرست کاربران سیستم</p>
        </div>
        @can('create', App\Models\User::class)
            <x-button href="{{ route('employees.create') }}">کارمند جدید</x-button>
        @endcan
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-right text-sm">
                <thead class="border-b border-stone-200 text-stone-500">
                    <tr>
                        <th class="py-2 font-medium">نام</th>
                        <th class="py-2 font-medium">ایمیل</th>
                        <th class="py-2 font-medium">نقش</th>
                        <th class="py-2 font-medium">سمت</th>
                        <th class="py-2 font-medium">وضعیت</th>
                        <th class="py-2 font-medium"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr class="border-b border-stone-100">
                            <td class="py-3">{{ $employee->name }}</td>
                            <td class="py-3">{{ $employee->email }}</td>
                            <td class="py-3">{{ $employee->role->label() }}</td>
                            <td class="py-3">{{ $employee->job_title ?? '—' }}</td>
                            <td class="py-3">{{ $employee->is_active ? 'فعال' : 'غیرفعال' }}</td>
                            <td class="py-3">
                                @can('update', $employee)
                                    <a href="{{ route('employees.edit', $employee) }}" class="text-stone-600 hover:text-stone-900">ویرایش</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $employees->links() }}</div>
    </x-card>
</x-layouts.app>
