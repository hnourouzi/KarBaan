<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_employees_and_reports(): void
    {
        $admin = User::factory()->admin()->create();
        $employee = User::factory()->employee()->create();

        $this->assertTrue($admin->can('viewAny', User::class));
        $this->assertTrue($admin->can('create', User::class));
        $this->assertTrue($admin->can('update', $employee));
        $this->assertTrue($admin->can('delete', $employee));
        $this->assertTrue($admin->can('viewReports', User::class));
        $this->assertTrue($admin->can('viewOwnHistory', User::class));
        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_manager_can_view_employees_but_not_mutate_them(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();

        $this->assertTrue($manager->can('viewAny', User::class));
        $this->assertTrue($manager->can('viewReports', User::class));
        $this->assertFalse($manager->can('create', User::class));
        $this->assertFalse($manager->can('update', $employee));
        $this->assertFalse($manager->can('delete', $employee));
    }

    public function test_employee_cannot_view_team_reports_or_employee_directory(): void
    {
        $employee = User::factory()->employee()->create();

        $this->assertFalse($employee->can('viewAny', User::class));
        $this->assertFalse($employee->can('viewReports', User::class));
        $this->assertTrue($employee->can('viewOwnHistory', User::class));
        $this->assertFalse($employee->can('create', User::class));
    }
}
