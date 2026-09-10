<?php

namespace App\Policies;

use App\Models\Birthday;
use App\Models\User;

class BirthdayPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('birthdays.view');
    }

    public function view(User $user, Birthday $birthday): bool
    {
        return $user->can('birthdays.view');
    }

    public function create(User $user): bool
    {
        return $user->can('birthdays.create');
    }

    public function update(User $user, Birthday $birthday): bool
    {
        return $user->can('birthdays.edit');
    }

    public function delete(User $user, Birthday $birthday): bool
    {
        return $user->can('birthdays.delete');
    }
}
