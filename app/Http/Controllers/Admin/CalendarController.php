<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\StoreCalendarEventRequest;
use App\Http\Requests\Calendar\UpdateCalendarEventRequest;
use App\Models\ActivityLog;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Services\CalendarFeedService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(protected CalendarFeedService $feedService)
    {
        $this->authorizeResource(CalendarEvent::class, 'calendar_event');
    }

    public function index(): View
    {
        return view('admin.calendar.index', [
            'users' => User::where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'upcomingBirthdays' => $this->feedService->upcomingBirthdays(),
            'upcomingHolidays' => $this->feedService->upcomingHolidays(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Calendar' => null],
        ]);
    }

    /**
     * JSON feed consumed by FullCalendar's `events` callback. Expects `start`
     * and `end` (ISO date strings FullCalendar sends automatically) and an
     * optional repeatable `types[]` filter.
     */
    public function feed(Request $request): JsonResponse
    {
        $start = Carbon::parse($request->get('start', now()->startOfMonth()));
        $end = Carbon::parse($request->get('end', now()->endOfMonth()));

        $types = $request->get('types', ['event', 'meeting', 'reminder', 'task', 'holiday', 'birthday']);
        $mineOnly = $request->boolean('mine_only');

        return response()->json(
            $this->feedService->feed($start, $end, $types, $mineOnly ? auth()->id() : null)
        );
    }

    public function show(CalendarEvent $calendar_event): JsonResponse
    {
        $calendar_event->load(['assignee:id,name', 'creator:id,name', 'attendees:id,name,email']);

        return response()->json($calendar_event);
    }

    public function store(StoreCalendarEventRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->safe()->except('attendees');
        $data['all_day'] = $request->boolean('all_day');
        $data['is_recurring'] = $request->boolean('is_recurring');

        $event = CalendarEvent::create($data);

        if ($request->filled('attendees')) {
            $event->attendees()->sync($request->input('attendees'));
        }

        $this->generateRecurrences($event);

        ActivityLog::record('created', $event, new: $event->toArray(),
            description: ucfirst($event->type)." \"{$event->title}\" created");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'event' => $event], 201);
        }

        return redirect()->route('admin.calendar.index')->with('success', ucfirst($event->type).' created.');
    }

    public function update(UpdateCalendarEventRequest $request, CalendarEvent $calendar_event): JsonResponse|RedirectResponse
    {
        $old = $calendar_event->toArray();
        $data = $request->safe()->except('attendees');
        $data['all_day'] = $request->boolean('all_day');
        $data['is_recurring'] = $request->boolean('is_recurring');

        $calendar_event->update($data);

        if ($request->has('attendees')) {
            $calendar_event->attendees()->sync($request->input('attendees', []));
        }

        ActivityLog::record('updated', $calendar_event, old: $old, new: $calendar_event->toArray(),
            description: ucfirst($calendar_event->type)." \"{$calendar_event->title}\" updated");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'event' => $calendar_event]);
        }

        return redirect()->route('admin.calendar.index')->with('success', ucfirst($calendar_event->type).' updated.');
    }

    /** Quick drag/drop reschedule from the calendar grid. */
    public function reschedule(Request $request, CalendarEvent $calendar_event): JsonResponse
    {
        $this->authorize('update', $calendar_event);

        $request->validate([
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['nullable', 'date', 'after_or_equal:start_datetime'],
        ]);

        $old = $calendar_event->toArray();
        $calendar_event->update($request->only('start_datetime', 'end_datetime'));

        ActivityLog::record('updated', $calendar_event, old: $old, new: $calendar_event->toArray(),
            description: "\"{$calendar_event->title}\" rescheduled");

        return response()->json(['success' => true]);
    }

    /** One-click status change (e.g. mark a task complete from the popover). */
    public function updateStatus(Request $request, CalendarEvent $calendar_event): JsonResponse
    {
        $this->authorize('update', $calendar_event);

        $request->validate(['status' => ['required', 'in:pending,in_progress,completed,cancelled']]);

        $old = $calendar_event->toArray();
        $calendar_event->update(['status' => $request->input('status')]);

        ActivityLog::record('updated', $calendar_event, old: $old, new: $calendar_event->toArray(),
            description: "\"{$calendar_event->title}\" marked {$calendar_event->status}");

        return response()->json(['success' => true, 'status' => $calendar_event->status]);
    }

    public function destroy(CalendarEvent $calendar_event): JsonResponse|RedirectResponse
    {
        $title = $calendar_event->title;
        $calendar_event->delete();

        ActivityLog::record('deleted', description: "Calendar item \"{$title}\" deleted");

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.calendar.index')->with('success', 'Deleted.');
    }

    /**
     * Materialise a handful of upcoming occurrences for a recurring item so
     * they show up on the feed without needing a cron-driven generator.
     * Capped at 52 occurrences (a year of weekly items) for safety.
     */
    protected function generateRecurrences(CalendarEvent $event): void
    {
        if (! $event->is_recurring || ! $event->recurrence_type) {
            return;
        }

        $interval = match ($event->recurrence_type) {
            'daily' => '1 day',
            'weekly' => '1 week',
            'monthly' => '1 month',
            'yearly' => '1 year',
        };

        $cursor = $event->start_datetime->copy();
        $endBound = $event->recurrence_end_date
            ? $event->recurrence_end_date->copy()->endOfDay()
            : $cursor->copy()->addYear();

        $duration = $event->end_datetime ? $event->start_datetime->diffInSeconds($event->end_datetime) : null;
        $count = 0;

        while ($count < 52) {
            $cursor = $cursor->copy()->modify('+'.$interval);
            if ($cursor->greaterThan($endBound)) {
                break;
            }

            CalendarEvent::create([
                'type' => $event->type,
                'title' => $event->title,
                'description' => $event->description,
                'start_datetime' => $cursor,
                'end_datetime' => $duration ? $cursor->copy()->addSeconds($duration) : null,
                'all_day' => $event->all_day,
                'location' => $event->location,
                'color' => $event->getRawOriginal('color'),
                'status' => 'pending',
                'priority' => $event->priority,
                'reminder_minutes_before' => $event->reminder_minutes_before,
                'is_recurring' => false,
                'assigned_to' => $event->assigned_to,
                'created_by' => $event->created_by,
                'recurrence_parent_id' => $event->id,
            ]);

            $count++;
        }
    }
}
