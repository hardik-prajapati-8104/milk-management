<?php

namespace App\Policies;

use App\Models\CalendarEvent;
use App\Models\User;

class CalendarEventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('calendar.view');
    }

    public function view(User $user, CalendarEvent $calendarEvent): bool
    {
        return $user->can('calendar.view');
    }

    public function create(User $user): bool
    {
        return $user->can('calendar.create');
    }

    public function update(User $user, CalendarEvent $calendarEvent): bool
    {
        if ($user->can('calendar.edit')) {
            return true;
        }

        // Owners/assignees may always update their own item (e.g. mark a task done).
        return $calendarEvent->created_by === $user->id || $calendarEvent->assigned_to === $user->id;
    }

    public function delete(User $user, CalendarEvent $calendarEvent): bool
    {
        return $user->can('calendar.delete') || $calendarEvent->created_by === $user->id;
    }
}
