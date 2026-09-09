<?php

namespace App\Policies;

use App\Models\MilkRate;
use App\Models\User;

class MilkRatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('milk-rates.view');
    }

    public function view(User $user, MilkRate $milkRate): bool
    {
        return $user->can('milk-rates.view');
    }

    public function create(User $user): bool
    {
        return $user->can('milk-rates.create');
    }

    public function update(User $user, MilkRate $milkRate): bool
    {
        return $user->can('milk-rates.edit');
    }

    public function delete(User $user, MilkRate $milkRate): bool
    {
        return $user->can('milk-rates.delete');
    }
}
