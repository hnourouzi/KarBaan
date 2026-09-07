<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\User;
use App\Services\Contracts\EmployeeServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeServiceInterface $employees) {}

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $employees = $this->employees->paginate();

        return view('employees.index', [
            'employees' => $employees,
            'employeeModalPayloads' => $employees->getCollection()->mapWithKeys(
                fn (User $employee): array => [$employee->id => $this->employeeModalPayload($employee)]
            ),
        ]);
    }

    public function create(): RedirectResponse
    {
        $this->authorize('create', User::class);

        return redirect()
            ->route('employees.index')
            ->with('employee_modal', 'create');
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $this->employees->create($request->toDto());

        return redirect()
            ->route('employees.index')
            ->with('success', 'کارمند جدید ثبت شد.');
    }

    public function edit(User $employee): RedirectResponse
    {
        $this->authorize('update', $employee);

        return redirect()
            ->route('employees.index')
            ->with('employee_modal_edit', $this->employeeModalPayload($employee));
    }

    public function update(UpdateEmployeeRequest $request, User $employee): RedirectResponse
    {
        $this->employees->update($employee, $request->toDto());

        return redirect()
            ->route('employees.index')
            ->with('success', 'اطلاعات کارمند به‌روز شد.');
    }

    public function destroy(User $employee): RedirectResponse
    {
        $this->authorize('delete', $employee);

        $this->employees->delete($employee);

        return redirect()
            ->route('employees.index')
            ->with('success', 'کارمند حذف شد.');
    }

    /**
     * @return array{id: int, name: string, email: string, jobTitle: string, role: string, isActive: bool, updateUrl: string, destroyUrl: string, canDelete: bool}
     */
    private function employeeModalPayload(User $employee): array
    {
        return [
            'id' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email,
            'jobTitle' => $employee->job_title ?? '',
            'role' => $employee->role->value,
            'isActive' => $employee->is_active,
            'updateUrl' => route('employees.update', $employee),
            'destroyUrl' => route('employees.destroy', $employee),
            'canDelete' => request()->user()?->can('delete', $employee) ?? false,
        ];
    }
}
