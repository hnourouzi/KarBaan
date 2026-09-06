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

        return view('employees.index', [
            'employees' => $this->employees->paginate(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('employees.create');
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $this->employees->create($request->toDto());

        return redirect()
            ->route('employees.index')
            ->with('success', 'کارمند جدید ثبت شد.');
    }

    public function edit(User $employee): View
    {
        $this->authorize('update', $employee);

        return view('employees.edit', [
            'employee' => $employee,
        ]);
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
}
