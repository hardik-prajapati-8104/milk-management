<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MilkStockLedger;
use App\Services\MilkStockService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class MilkStockController extends Controller
{
    public function __construct(protected MilkStockService $stock)
    {
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('milk-stock.view'), 403);

        $date = $request->get('date', now()->toDateString());
        $shift = $request->get('shift');

        return view('admin.milk-stock.index', [
            'currentStock' => $this->stock->currentStock(),
            'date' => $date,
            'shift' => $shift,
            'breakdown' => $this->stock->dayBreakdown($date, $shift ?: null),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Milk Stock' => null],
        ]);
    }

    public function data(Request $request)
    {
        abort_unless($request->user()->can('milk-stock.view'), 403);

        $query = MilkStockLedger::query();

        if ($date = $request->get('date')) {
            $query->where('movement_date', $date);
        }
        if ($shift = $request->get('shift')) {
            $query->where('shift', $shift);
        }
        if ($type = $request->get('movement_type')) {
            $query->where('movement_type', $type);
        }

        return DataTables::eloquent($query->latest('id'))
            ->addColumn('direction_badge', fn (MilkStockLedger $l) => $l->direction === 'in'
                ? '<span class="badge text-bg-success">IN</span>'
                : '<span class="badge text-bg-danger">OUT</span>')
            ->addColumn('type_label', fn (MilkStockLedger $l) => ucwords(str_replace('_', ' ', $l->movement_type)))
            ->toJson();
    }
}
