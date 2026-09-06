<x-layouts.app title="کارکنان | کاربان">
    <x-ui.page-header title="کارکنان" description="فهرست کاربران سیستم">
        <x-slot:actions>
            @can('create', App\Models\User::class)
                <x-button href="{{ route('employees.create') }}" size="sm">کارمند جدید</x-button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <x-card>
        <div class="kb-table-wrap">
            <table class="kb-table">
                <thead>
                    <tr>
                        <th>نام</th>
                        <th>ایمیل</th>
                        <th>نقش</th>
                        <th>سمت</th>
                        <th>وضعیت</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->role->label() }}</td>
                            <td>{{ $employee->job_title ?? '—' }}</td>
                            <td>
                                @if ($employee->is_active)
                                    <span class="kb-badge kb-badge-success">فعال</span>
                                @else
                                    <span class="kb-badge kb-badge-muted">غیرفعال</span>
                                @endif
                            </td>
                            <td>
                                @can('update', $employee)
                                    <x-button href="{{ route('employees.edit', $employee) }}" variant="ghost" size="sm">ویرایش</x-button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 border-t border-slate-100 pt-4">{{ $employees->links() }}</div>
    </x-card>
</x-layouts.app>
