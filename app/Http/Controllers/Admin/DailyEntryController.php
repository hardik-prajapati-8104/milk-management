<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyEntry\BulkSaveDailyEntryRequest;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\DailyEntry;
use App\Models\Route as DeliveryRoute;
use App\Services\DailyEntryService;
use App\Services\MilkStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyEntryController extends Controller
{
    public function __construct(
        protected DailyEntryService $service,
        protected MilkStockService $stock,
    ) {
    }

    /**
     * The grid entry screen for a given date + shift.
     * Defaults to today and the shift matching the current time of day.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', DailyEntry::class);

        $date = $request->get('date', now()->toDateString());
        $shift = $request->get('shift', current_shift());
        $routeId = $request->get('route_id');

        $customerQuery = Customer::active()->with(['route'])->orderBy('name');
        if ($routeId) {
            $customerQuery->where('route_id', $routeId);
        }
        $customers = $customerQuery->get();

        $existingEntries = DailyEntry::where('entry_date', $date)
            ->where('shift', $shift)
            ->whereIn('customer_id', $customers->pluck('id'))
            ->get()
            ->keyBy('customer_id');

        return view('admin.daily-entries.index', [
            'date' => $date,
            'shift' => $shift,
            'routeId' => $routeId,
            'routes' => DeliveryRoute::active()->orderBy('name')->get(),
            'customers' => $customers,
            'existingEntries' => $existingEntries,
            'currentStock' => $this->stock->currentStock(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Daily Entries' => null],
        ]);
    }

    /**
     * Live customer search used by the grid's search box (ID / mobile / name / barcode).
     */
    public function search(Request $request): JsonResponse
    {
        $this->authorize('viewAny', DailyEntry::class);

        $term = (string) $request->get('q', '');

        $customers = Customer::active()->search($term)->limit(15)->get(
            ['id', 'consumer_id', 'name', 'mobile', 'milk_type', 'morning_rate', 'evening_rate']
        );

        return response()->json($customers);
    }

    /**
     * Bulk save the whole grid in one request. Rejects locked rows unless admin.
     */
    public function bulkSave(BulkSaveDailyEntryRequest $request): JsonResponse
    {
        $result = $this->service->bulkSave(
            $request->validated('entry_date'),
            $request->validated('shift'),
            $request->validated('entries')
        );

        return response()->json([
            'message' => "{$result['created']} entries created, {$result['updated']} updated.",
            'blocked' => $result['blocked'],
        ]);
    }

    /**
     * Copy yesterday's quantities (same shift) into today's grid for customers not yet entered.
     */
    public function copyPreviousDay(Request $request): JsonResponse
    {
        $this->authorize('create', DailyEntry::class);

        $request->validate(['entry_date' => 'required|date', 'shift' => 'required|in:morning,evening']);

        $count = $this->service->copyPreviousDay($request->entry_date, $request->shift);

        return response()->json(['message' => "{$count} entries copied from the previous day."]);
    }

    /**
     * Unlock a single entry so a non-admin's edit can go through once (admin-only action).
     */
    public function toggleLock(DailyEntry $dailyEntry): RedirectResponse
    {
        $this->authorize('update', $dailyEntry);

        $dailyEntry->update(['is_locked' => ! $dailyEntry->is_locked]);

        ActivityLog::record($dailyEntry->is_locked ? 'locked' : 'unlocked', $dailyEntry,
            description: 'Daily entry lock toggled for ' . $dailyEntry->entry_date->format('d M Y'));

        return back()->with('success', 'Entry ' . ($dailyEntry->is_locked ? 'locked' : 'unlocked') . '.');
    }

    /**
     * Calendar view showing green/yellow/red completion status per day for a given month.
     */
    public function calendar(Request $request): View
    {
        $this->authorize('viewAny', DailyEntry::class);

        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $status = $this->service->monthCompletionStatus($year, $month);

        return view('admin.daily-entries.calendar', [
            'year' => $year,
            'month' => $month,
            'status' => $status,
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Daily Entries' => route('admin.daily-entries.index'),
                'Calendar' => null,
            ],
        ]);
    }
}
