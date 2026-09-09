<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MilkRate\StoreMilkRateRequest;
use App\Http\Requests\MilkRate\UpdateMilkRateRequest;
use App\Models\ActivityLog;
use App\Models\MilkRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MilkRateController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(MilkRate::class, 'milk_rate');
    }

    public function index(): View
    {
        $rates = MilkRate::with('createdBy')->orderByDesc('effective_date')->paginate(20);

        return view('admin.milk-rates.index', [
            'rates' => $rates,
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Milk Rates' => null],
        ]);
    }

    public function create(): View
    {
        return view('admin.milk-rates.create', [
            'defaultDate' => now()->addDay()->toDateString(),
        ]);
    }

    public function store(StoreMilkRateRequest $request): RedirectResponse
    {
        $rate = MilkRate::create($request->validated() + [
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        ActivityLog::record('created', $rate, new: $rate->toArray(),
            description: "Milk rate card created for {$rate->effective_date->format('d M Y')}");

        return redirect()->route('admin.milk-rates.index')
            ->with('success', 'Rate card created. It will apply automatically to entries from ' . $rate->effective_date->format('d M Y') . ' onward.');
    }

    public function edit(MilkRate $milkRate): View
    {
        return view('admin.milk-rates.edit', ['rate' => $milkRate]);
    }

    public function update(UpdateMilkRateRequest $request, MilkRate $milkRate): RedirectResponse
    {
        $old = $milkRate->toArray();

        $milkRate->update($request->validated() + [
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record('updated', $milkRate, old: $old, new: $milkRate->toArray(),
            description: "Milk rate card updated for {$milkRate->effective_date->format('d M Y')}");

        return redirect()->route('admin.milk-rates.index')->with('success', 'Rate card updated successfully.');
    }

    public function destroy(MilkRate $milkRate): RedirectResponse
    {
        // Historical rate cards should generally be deactivated rather than deleted,
        // since past daily entries/bills may reference the rate that was applied.
        $milkRate->delete();

        ActivityLog::record('deleted', $milkRate,
            description: "Milk rate card for {$milkRate->effective_date->format('d M Y')} deleted");

        return redirect()->route('admin.milk-rates.index')->with('success', 'Rate card deleted.');
    }
}
