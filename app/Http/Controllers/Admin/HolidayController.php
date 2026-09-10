<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Holiday\StoreHolidayRequest;
use App\Http\Requests\Holiday\UpdateHolidayRequest;
use App\Models\ActivityLog;
use App\Models\Holiday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Holiday::class, 'holiday');
    }

    public function index(Request $request): View
    {
        if ($request->ajax()) {
            abort(400, 'Use /admin/holidays/data for AJAX.');
        }

        return view('admin.holidays.index', [
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Calendar' => route('admin.calendar.index'), 'Holidays' => null],
        ]);
    }

    public function data()
    {
        $query = Holiday::query()->orderBy('date');

        return DataTables::eloquent($query)
            ->addColumn('type_label', fn (Holiday $h) => ucfirst($h->type))
            ->addColumn('recurring_label', fn (Holiday $h) => $h->is_recurring_yearly ? 'Every year' : 'One-time')
            ->addColumn('actions', fn (Holiday $h) => view('admin.holidays._actions', ['holiday' => $h])->render())
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.holidays.create');
    }

    public function store(StoreHolidayRequest $request): RedirectResponse
    {
        $data = $request->safe()->all();
        $data['is_recurring_yearly'] = $request->boolean('is_recurring_yearly');

        $holiday = Holiday::create($data);

        ActivityLog::record('created', $holiday, new: $holiday->toArray(),
            description: "Holiday \"{$holiday->name}\" added");

        return redirect()->route('admin.holidays.index')->with('success', "Holiday \"{$holiday->name}\" added.");
    }

    public function edit(Holiday $holiday): View
    {
        return view('admin.holidays.edit', ['holiday' => $holiday]);
    }

    public function update(UpdateHolidayRequest $request, Holiday $holiday): RedirectResponse
    {
        $old = $holiday->toArray();
        $data = $request->safe()->all();
        $data['is_recurring_yearly'] = $request->boolean('is_recurring_yearly');

        $holiday->update($data);

        ActivityLog::record('updated', $holiday, old: $old, new: $holiday->toArray(),
            description: "Holiday \"{$holiday->name}\" updated");

        return redirect()->route('admin.holidays.index')->with('success', "Holiday \"{$holiday->name}\" updated.");
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $name = $holiday->name;
        $holiday->delete();

        ActivityLog::record('deleted', description: "Holiday \"{$name}\" deleted");

        return redirect()->route('admin.holidays.index')->with('success', "Holiday \"{$name}\" deleted.");
    }
}
