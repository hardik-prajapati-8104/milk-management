<?php

namespace App\Policies;

use App\Models\DailyEntry;
use App\Models\User;

class DailyEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('daily-entries.view');
    }

    public function view(User $user, DailyEntry $dailyEntry): bool
    {
        return $user->can('daily-entries.view');
    }

    public function create(User $user): bool
    {
        return $user->can('daily-entries.create');
    }

    public function update(User $user, DailyEntry $dailyEntry): bool
    {
        if ($dailyEntry->is_locked) {
            return $user->can('daily-entries.lock-override') || $user->hasRole('Admin');
        }

        return $user->can('daily-entries.edit');
    }

    public function delete(User $user, DailyEntry $dailyEntry): bool
    {
        return $user->can('daily-entries.delete') && (! $dailyEntry->is_locked || $user->hasRole('Admin'));
    }
}
