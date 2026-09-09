<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('users.edit');
    }

    public function delete(User $user, User $model): bool
    {
        // Never allow deleting your own account, or the last remaining Admin.
        if ($user->id === $model->id) {
            return false;
        }

        if ($model->hasRole('Admin') && User::role('Admin')->count() <= 1) {
            return false;
        }

        return $user->can('users.delete');
    }
}
