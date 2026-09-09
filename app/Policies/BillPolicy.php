<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\User;

class BillPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('bills.view');
    }

    public function view(User $user, Bill $bill): bool
    {
        return $user->can('bills.view');
    }

    public function create(User $user): bool
    {
        return $user->can('bills.create');
    }

    public function generate(User $user): bool
    {
        return $user->can('bills.generate');
    }

    public function update(User $user, Bill $bill): bool
    {
        return $user->can('bills.edit') && $bill->status !== 'paid';
    }

    public function delete(User $user, Bill $bill): bool
    {
        return $user->can('bills.delete') && $bill->status === 'draft';
    }

    public function print(User $user, Bill $bill): bool
    {
        return $user->can('bills.print');
    }

    public function export(User $user): bool
    {
        return $user->can('bills.export');
    }
}
