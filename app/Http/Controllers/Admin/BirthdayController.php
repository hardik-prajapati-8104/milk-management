<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Birthday\StoreBirthdayRequest;
use App\Http\Requests\Birthday\UpdateBirthdayRequest;
use App\Models\ActivityLog;
use App\Models\Birthday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BirthdayController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Birthday::class, 'birthday');
    }

    public function index(Request $request): View
    {
        if ($request->ajax()) {
            abort(400, 'Use /admin/birthdays/data for AJAX.');
        }

        return view('admin.birthdays.index', [
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Calendar' => route('admin.calendar.index'), 'Birthdays' => null],
        ]);
    }

    public function data()
    {
        $query = Birthday::query()->orderBy('name');

        return DataTables::eloquent($query)
            ->addColumn('category_label', fn (Birthday $b) => ucfirst($b->category))
            ->addColumn('next_occurrence', fn (Birthday $b) => $b->nextOccurrence()->format('d M Y'))
            ->addColumn('days_until', fn (Birthday $b) => $b->daysUntil())
            ->addColumn('turning', fn (Birthday $b) => $b->turningAge())
            ->addColumn('actions', fn (Birthday $b) => view('admin.birthdays._actions', ['birthday' => $b])->render())
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.birthdays.create');
    }

    public function store(StoreBirthdayRequest $request): RedirectResponse
    {
        $data = $request->safe()->all();
        $data['is_active'] = $request->boolean('is_active', true);

        $birthday = Birthday::create($data);

        ActivityLog::record('created', $birthday, new: $birthday->toArray(),
            description: "Birthday for \"{$birthday->name}\" added");

        return redirect()->route('admin.birthdays.index')->with('success', "Birthday for \"{$birthday->name}\" added.");
    }

    public function edit(Birthday $birthday): View
    {
        return view('admin.birthdays.edit', ['birthday' => $birthday]);
    }

    public function update(UpdateBirthdayRequest $request, Birthday $birthday): RedirectResponse
    {
        $old = $birthday->toArray();
        $data = $request->safe()->all();
        $data['is_active'] = $request->boolean('is_active', true);

        $birthday->update($data);

        ActivityLog::record('updated', $birthday, old: $old, new: $birthday->toArray(),
            description: "Birthday for \"{$birthday->name}\" updated");

        return redirect()->route('admin.birthdays.index')->with('success', "Birthday for \"{$birthday->name}\" updated.");
    }

    public function destroy(Birthday $birthday): RedirectResponse
    {
        $name = $birthday->name;
        $birthday->delete();

        ActivityLog::record('deleted', description: "Birthday for \"{$name}\" deleted");

        return redirect()->route('admin.birthdays.index')->with('success', "Birthday for \"{$name}\" deleted.");
    }
}
