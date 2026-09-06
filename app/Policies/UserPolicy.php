<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageTeam();
    }

    public function view(User $user, User $employee): bool
    {
        return $user->canManageTeam() || $user->is($employee);
    }

    public function create(User $user): bool
    {
        return $user->canManageEmployees();
    }

    public function update(User $user, User $employee): bool
    {
        return $user->canManageEmployees();
    }

    public function delete(User $user, User $employee): bool
    {
        return $user->canManageEmployees() && $user->isNot($employee);
    }

    public function viewReports(User $user): bool
    {
        return $user->canManageTeam();
    }

    public function viewOwnHistory(User $user): bool
    {
        return true;
    }
}
