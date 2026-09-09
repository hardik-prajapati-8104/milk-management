<?php

namespace App\Policies;

use App\Models\MilkPurchase;
use App\Models\User;

class MilkPurchasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('milk-purchases.view');
    }

    public function view(User $user, MilkPurchase $milkPurchase): bool
    {
        return $user->can('milk-purchases.view');
    }

    public function create(User $user): bool
    {
        return $user->can('milk-purchases.create');
    }

    public function update(User $user, MilkPurchase $milkPurchase): bool
    {
        return $user->can('milk-purchases.edit');
    }

    public function delete(User $user, MilkPurchase $milkPurchase): bool
    {
        return $user->can('milk-purchases.delete');
    }
}
