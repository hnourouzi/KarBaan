<x-layouts.app title="کارکنان | کاربان">
    @php
        $openCreate = old('employee_form') === 'create' || session('employee_modal') === 'create';
        $openEdit = old('employee_form') === 'update';
        $editPayload = $openEdit ? null : session('employee_modal_edit');
        $initialCanDelete = filled(old('employee_id')) && (int) old('employee_id') !== auth()->id();
        $canManageEmployees = auth()->user()->can('create', App\Models\User::class);
    @endphp

    <div
        @if ($canManageEmployees)
            x-data="employeeModals({{ \Illuminate\Support\Js::from([
                'createOpen' => $openCreate,
                'editOpen' => $openEdit,
                'canDelete' => $initialCanDelete,
                'editPayload' => $editPayload,
                'editTitle' => old('name', 'ویرایش کارمند'),
            ]) }})"
        @endif
    >
        <x-ui.page-header title="کارکنان" description="فهرست کاربران سیستم">
            <x-slot:actions>
                @can('create', App\Models\User::class)
                    <x-button type="button" size="sm" x-on:click="openCreate()">کارمند جدید</x-button>
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
                                        <x-button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            x-on:click="openEdit({{ \Illuminate\Support\Js::from($employeeModalPayloads[$employee->id]) }})"
                                        >
                                            ویرایش
                                        </x-button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6 border-t border-slate-100 pt-4">{{ $employees->links() }}</div>
        </x-card>

        @can('create', App\Models\User::class)
            <x-ui.modal
                title="ثبت کارمند"
                description="ایجاد حساب کاربری جدید برای عضو تیم"
                close="closeCreate()"
                x-show="createOpen"
                x-on:keydown.escape.window="if (createOpen) closeCreate()"
            >
                <x-employee-form mode="create" :action="route('employees.store')" x-ref="createForm" />
            </x-ui.modal>

            <x-ui.modal
                description="به‌روزرسانی اطلاعات حساب کاربری"
                close="closeEdit()"
                x-show="editOpen"
                x-on:keydown.escape.window="if (editOpen) closeEdit()"
            >
                <x-slot:heading>
                    <span x-text="editTitle">ویرایش کارمند</span>
                </x-slot:heading>

                <x-employee-form
                    mode="edit"
                    :action="old('employee_id') ? route('employees.update', (int) old('employee_id')) : url('/employees/0')"
                    x-ref="editForm"
                />

                <form
                    method="POST"
                    action="{{ old('employee_id') ? route('employees.destroy', (int) old('employee_id')) : url('/employees/0') }}"
                    class="mt-4 border-t border-slate-100 pt-4"
                    x-ref="destroyForm"
                    x-show="canDelete"
                    x-cloak
                    onsubmit="return confirm('حذف این کاربر قطعی است؟')"
                >
                    @csrf
                    @method('DELETE')
                    <x-button variant="danger">حذف کارمند</x-button>
                </form>
            </x-ui.modal>
        @endcan
    </div>

    @can('create', App\Models\User::class)
        @once
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('employeeModals', (initial = {}) => ({
                        createOpen: Boolean(initial.createOpen),
                        editOpen: Boolean(initial.editOpen),
                        canDelete: Boolean(initial.canDelete),
                        editTitle: initial.editTitle || 'ویرایش کارمند',
                        editPayload: initial.editPayload ?? null,
                        init() {
                            if (! this.editOpen && this.editPayload) {
                                this.openEdit(this.editPayload);
                            }
                        },
                        openCreate() {
                            this.editOpen = false;
                            this.createOpen = true;
                            this.$nextTick(() => this.$refs.createName?.focus());
                        },
                        closeCreate() {
                            this.createOpen = false;
                        },
                        openEdit(employee) {
                            const form = this.$refs.editForm;

                            if (! form) {
                                return;
                            }

                            this.createOpen = false;
                            form.action = employee.updateUrl;
                            form.querySelector('[name="employee_id"]').value = employee.id;
                            form.querySelector('[name="name"]').value = employee.name;
                            form.querySelector('[name="email"]').value = employee.email;
                            form.querySelector('[name="password"]').value = '';
                            form.querySelector('[name="job_title"]').value = employee.jobTitle ?? '';
                            form.querySelector('[name="role"]').value = employee.role;

                            const active = form.querySelector('[name="is_active"][type="checkbox"]');

                            if (active) {
                                active.checked = Boolean(employee.isActive);
                            }

                            if (this.$refs.destroyForm) {
                                this.$refs.destroyForm.action = employee.destroyUrl;
                            }

                            this.canDelete = Boolean(employee.canDelete);
                            this.editTitle = 'ویرایش ' + employee.name;
                            this.editOpen = true;
                            this.$nextTick(() => this.$refs.editName?.focus());
                        },
                        closeEdit() {
                            this.editOpen = false;
                        },
                    }));
                });
        </script>
        @endonce
    @endcan
</x-layouts.app>
