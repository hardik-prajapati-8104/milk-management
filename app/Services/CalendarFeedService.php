<?php

namespace App\Services;

use App\Models\Birthday;
use App\Models\CalendarEvent;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarFeedService
{
    /** Colours for the non-CalendarEvent sources (those pick their own per-type colour). */
    protected const HOLIDAY_COLOR = '#dc3545';
    protected const BIRTHDAY_COLOR = '#d63384';

    /**
     * Build a FullCalendar-ready array of events for the given window.
     *
     * @param  array<int,string>  $types  Subset of: event, meeting, reminder, task, holiday, birthday
     */
    public function feed(Carbon $start, Carbon $end, array $types, ?int $assignedTo = null): array
    {
        $items = collect();

        $calendarTypes = array_values(array_intersect($types, ['event', 'meeting', 'reminder', 'task']));
        if (! empty($calendarTypes)) {
            $items = $items->merge($this->calendarEvents($start, $end, $calendarTypes, $assignedTo));
        }

        if (in_array('holiday', $types, true)) {
            $items = $items->merge($this->holidays($start, $end));
        }

        if (in_array('birthday', $types, true)) {
            $items = $items->merge($this->birthdays($start, $end));
        }

        return $items->values()->all();
    }

    protected function calendarEvents(Carbon $start, Carbon $end, array $types, ?int $assignedTo): Collection
    {
        $query = CalendarEvent::query()
            ->with(['assignee:id,name', 'creator:id,name'])
            ->whereIn('type', $types)
            ->between($start, $end);

        if ($assignedTo) {
            $query->where(function ($q) use ($assignedTo) {
                $q->where('assigned_to', $assignedTo)->orWhere('created_by', $assignedTo);
            });
        }

        return $query->get()->map(function (CalendarEvent $event) {
            return [
                'id' => 'ce-'.$event->id,
                'title' => $event->title,
                'start' => $event->start_datetime->toIso8601String(),
                'end' => $event->end_datetime?->toIso8601String(),
                'allDay' => (bool) $event->all_day,
                'color' => $event->color,
                'extendedProps' => [
                    'source' => 'calendar_event',
                    'recordId' => $event->id,
                    'type' => $event->type,
                    'status' => $event->status,
                    'priority' => $event->priority,
                    'location' => $event->location,
                    'description' => $event->description,
                    'assignee' => $event->assignee?->name,
                    'creator' => $event->creator?->name,
                    'editable' => true,
                ],
            ];
        });
    }

    protected function holidays(Carbon $start, Carbon $end): Collection
    {
        $result = collect();

        Holiday::query()->get()->each(function (Holiday $holiday) use ($start, $end, &$result) {
            foreach ([$start->year, $end->year] as $year) {
                $occurrence = $holiday->occurrenceInYear($year);
                if ($occurrence && $occurrence->between($start, $end)) {
                    $result->push([
                        'id' => 'hol-'.$holiday->id.'-'.$year,
                        'title' => '🎉 '.$holiday->name,
                        'start' => $occurrence->toDateString(),
                        'allDay' => true,
                        'color' => $holiday->color ?: self::HOLIDAY_COLOR,
                        'extendedProps' => [
                            'source' => 'holiday',
                            'recordId' => $holiday->id,
                            'type' => 'holiday',
                            'description' => $holiday->description,
                            'editable' => false,
                        ],
                    ]);
                }
            }
        });

        return $result->unique('id');
    }

    protected function birthdays(Carbon $start, Carbon $end): Collection
    {
        $result = collect();

        $entries = collect();

        Birthday::query()->where('is_active', true)->get()->each(function (Birthday $b) use (&$entries) {
            $entries->push(['name' => $b->name, 'dob' => $b->date_of_birth, 'id' => 'bd-'.$b->id]);
        });

        Employee::query()->whereNotNull('date_of_birth')->get(['id', 'name', 'date_of_birth'])
            ->each(function (Employee $e) use (&$entries) {
                $entries->push(['name' => $e->name.' (Staff)', 'dob' => $e->date_of_birth, 'id' => 'emp-'.$e->id]);
            });

        Customer::query()->whereNotNull('date_of_birth')->get(['id', 'name', 'date_of_birth'])
            ->each(function (Customer $c) use (&$entries) {
                $entries->push(['name' => $c->name.' (Customer)', 'dob' => $c->date_of_birth, 'id' => 'cus-'.$c->id]);
            });

        foreach ($entries as $entry) {
            foreach ([$start->year, $end->year] as $year) {
                try {
                    $occurrence = Carbon::create($year, $entry['dob']->month, $entry['dob']->day);
                } catch (\Throwable) {
                    continue;
                }

                if ($occurrence->between($start, $end)) {
                    $result->push([
                        'id' => 'birthday-'.$entry['id'].'-'.$year,
                        'title' => '🎂 '.$entry['name'],
                        'start' => $occurrence->toDateString(),
                        'allDay' => true,
                        'color' => self::BIRTHDAY_COLOR,
                        'extendedProps' => [
                            'source' => 'birthday',
                            'type' => 'birthday',
                            'description' => 'Turns '.($year - $entry['dob']->year).' years old.',
                            'editable' => false,
                        ],
                    ]);
                }
            }
        }

        return $result->unique('id');
    }

    /** Compact list used by dashboard / calendar sidebar widgets. */
    public function upcomingBirthdays(int $days = 30, int $limit = 8): Collection
    {
        $today = Carbon::today();
        $entries = collect();

        Birthday::query()->where('is_active', true)->get()->each(function (Birthday $b) use (&$entries) {
            $entries->push(['name' => $b->name, 'model' => $b]);
        });
        Employee::query()->whereNotNull('date_of_birth')->get(['id', 'name', 'date_of_birth'])
            ->each(function (Employee $e) use (&$entries) {
                $entries->push(['name' => $e->name.' (Staff)', 'dob' => $e->date_of_birth]);
            });
        Customer::query()->whereNotNull('date_of_birth')->get(['id', 'name', 'date_of_birth'])
            ->each(function (Customer $c) use (&$entries) {
                $entries->push(['name' => $c->name.' (Customer)', 'dob' => $c->date_of_birth]);
            });

        return $entries->map(function ($entry) {
            $dob = $entry['dob'] ?? $entry['model']->date_of_birth;
            $next = $entry['model'] ?? null;
            $nextDate = $next ? $next->nextOccurrence() : $this->nextOccurrenceOf($dob);

            return [
                'name' => $entry['name'],
                'date' => $nextDate,
                'days_until' => Carbon::today()->diffInDays($nextDate, false),
                'turning' => $nextDate->year - $dob->year,
            ];
        })
            ->filter(fn ($e) => $e['days_until'] >= 0 && $e['days_until'] <= $days)
            ->sortBy('days_until')
            ->take($limit)
            ->values();
    }

    public function upcomingHolidays(int $days = 60, int $limit = 5): Collection
    {
        $today = Carbon::today();

        return Holiday::query()->get()
            ->map(function (Holiday $holiday) use ($today) {
                $occurrence = $holiday->occurrenceInYear($today->year);
                if ($occurrence && $occurrence->isBefore($today)) {
                    $occurrence = $holiday->occurrenceInYear($today->year + 1);
                }

                return $occurrence ? [
                    'name' => $holiday->name,
                    'date' => $occurrence,
                    'days_until' => $today->diffInDays($occurrence, false),
                    'type' => $holiday->type,
                ] : null;
            })
            ->filter()
            ->filter(fn ($h) => $h['days_until'] >= 0 && $h['days_until'] <= $days)
            ->sortBy('days_until')
            ->take($limit)
            ->values();
    }

    protected function nextOccurrenceOf(Carbon $dob): Carbon
    {
        $today = Carbon::today();
        try {
            $thisYear = Carbon::create($today->year, $dob->month, $dob->day);
        } catch (\Throwable) {
            $thisYear = Carbon::create($today->year, 3, 1);
        }

        return $thisYear->isBefore($today) ? $thisYear->addYear() : $thisYear;
    }
}
