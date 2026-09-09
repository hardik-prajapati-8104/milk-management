<?php

namespace App\Policies;

use App\Models\CashSale;
use App\Models\User;

class CashSalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('cash-sales.view');
    }

    public function view(User $user, CashSale $cashSale): bool
    {
        return $user->can('cash-sales.view');
    }

    public function create(User $user): bool
    {
        return $user->can('cash-sales.create');
    }

    public function update(User $user, CashSale $cashSale): bool
    {
        return $user->can('cash-sales.edit');
    }

    public function delete(User $user, CashSale $cashSale): bool
    {
        return $user->can('cash-sales.delete');
    }
}
