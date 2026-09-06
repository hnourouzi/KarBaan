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
}
