<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('roles.view');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.view');
    }

    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('roles.edit');
    }

    public function delete(User $user, Role $role): bool
    {
        // The 5 seeded roles are structural to the app's permission matrix and
        // are never deletable from the UI, only custom roles added later are.
        $protected = ['Admin', 'Manager', 'Accountant', 'Operator', 'Delivery Boy'];

        return $user->can('roles.delete') && ! in_array($role->name, $protected, true);
    }
}
