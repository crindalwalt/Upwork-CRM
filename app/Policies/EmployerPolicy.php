<?php

namespace App\Policies;

use App\Models\Employer;
use App\Models\User;

class EmployerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Employer $employer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Employer $employer): bool
    {
        return true;
    }

    public function delete(User $user, Employer $employer): bool
    {
        return $user->isAdmin();
    }
}
