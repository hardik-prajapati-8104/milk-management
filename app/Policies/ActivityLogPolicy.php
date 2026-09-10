<?php

namespace App\Policies;

use App\Models\User;

class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('activity-logs.view');
    }

    public function view(User $user): bool
    {
        return $user->can('activity-logs.view');
    }

    public function export(User $user): bool
    {
        return $user->can('activity-logs.export');
    }

    public function manage(User $user): bool
    {
        // Purging old logs / marking errors resolved is a more sensitive,
        // Admin-level action than just viewing the audit trail.
        return $user->can('activity-logs.manage');
    }
}
