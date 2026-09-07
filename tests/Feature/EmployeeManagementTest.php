<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_an_employee(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('employees.store'), [
                'name' => 'نیما حسینی',
                'email' => 'nima@karbaan.test',
                'password' => 'password1',
                'role' => UserRole::Employee->value,
                'job_title' => 'تحلیل‌گر',
                'is_active' => true,
            ])
            ->assertRedirect(route('employees.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'nima@karbaan.test',
            'role' => UserRole::Employee->value,
            'job_title' => 'تحلیل‌گر',
        ]);
    }

    public function test_manager_cannot_create_an_employee(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('employees.create'))
            ->assertForbidden();
    }

    public function test_employee_cannot_view_the_employee_list(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get(route('employees.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('employees.destroy', $admin))
            ->assertForbidden();
    }

    public function test_admin_create_page_redirects_to_the_employee_list_modal(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('employees.create'))
            ->assertRedirect(route('employees.index'));

        $this->actingAs($admin)
            ->followingRedirects()
            ->get(route('employees.create'))
            ->assertSee('ثبت کارمند')
            ->assertSee('id="employee-create-name"', false)
            ->assertDontSee(route('employees.create'), false);
    }

    public function test_admin_edit_page_redirects_to_the_employee_list_modal(): void
    {
        $admin = User::factory()->admin()->create();
        $employee = User::factory()->employee()->create([
            'name' => 'سارا احمدی',
        ]);

        $this->actingAs($admin)
            ->get(route('employees.edit', $employee))
            ->assertRedirect(route('employees.index'));

        $this->actingAs($admin)
            ->followingRedirects()
            ->get(route('employees.edit', $employee))
            ->assertSee('id="employee-edit-name"', false)
            ->assertDontSee(route('employees.edit', $employee), false);
    }

    public function test_employee_list_renders_create_and_edit_modals_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $employee = User::factory()->employee()->create();

        $this->actingAs($admin)
            ->get(route('employees.index'))
            ->assertOk()
            ->assertSee('کارمند جدید')
            ->assertSee('ثبت کارمند')
            ->assertSee('id="employee-create-name"', false)
            ->assertSee('id="employee-edit-name"', false)
            ->assertSee('x-ref="createName"', false)
            ->assertSee('x-ref="editForm"', false)
            ->assertSee('ویرایش')
            ->assertDontSee(route('employees.create'), false)
            ->assertDontSee(route('employees.edit', $employee), false);
    }

    public function test_invalid_employee_store_returns_to_the_list_with_errors(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('employees.index'))
            ->post(route('employees.store'), [
                'employee_form' => 'create',
            ])
            ->assertRedirect(route('employees.index'))
            ->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    public function test_admin_can_update_an_employee(): void
    {
        $admin = User::factory()->admin()->create();
        $employee = User::factory()->employee()->create([
            'name' => 'علی رضایی',
            'email' => 'ali@karbaan.test',
            'job_title' => 'کارشناس',
        ]);

        $this->actingAs($admin)
            ->put(route('employees.update', $employee), [
                'name' => 'علی محمدی',
                'email' => 'ali.mohammadi@karbaan.test',
                'role' => UserRole::Employee->value,
                'job_title' => 'تحلیل‌گر',
                'is_active' => true,
                'employee_form' => 'update',
                'employee_id' => $employee->id,
            ])
            ->assertRedirect(route('employees.index'));

        $this->assertDatabaseHas('users', [
            'id' => $employee->id,
            'name' => 'علی محمدی',
            'email' => 'ali.mohammadi@karbaan.test',
            'job_title' => 'تحلیل‌گر',
        ]);
    }

    public function test_manager_cannot_update_an_employee(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();

        $this->actingAs($manager)
            ->put(route('employees.update', $employee), [
                'name' => 'نام جدید',
                'email' => $employee->email,
                'role' => UserRole::Employee->value,
            ])
            ->assertForbidden();
    }

    public function test_manager_does_not_see_employee_management_modals(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('employees.index'))
            ->assertOk()
            ->assertDontSee('کارمند جدید')
            ->assertDontSee('id="employee-create-name"', false);
    }
}
