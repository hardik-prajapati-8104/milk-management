<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashSale\StoreCashSaleRequest;
use App\Http\Requests\CashSale\UpdateCashSaleRequest;
use App\Models\CashSale;
use App\Models\PaymentMethod;
use App\Services\CashSaleService;
use App\Services\MilkStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CashSaleController extends Controller
{
    public function __construct(
        protected CashSaleService $service,
        protected MilkStockService $stock,
    ) {
        $this->authorizeResource(CashSale::class, 'cash_sale');
    }

    public function index(Request $request): View
    {
        if ($request->ajax()) {
            abort(400, 'Use /admin/cash-sales/data for AJAX.');
        }

        return view('admin.cash-sales.index', [
            'currentStock' => $this->stock->currentStock(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Cash Sales' => null],
        ]);
    }

    public function data(Request $request)
    {
        $query = CashSale::query();

        if ($from = $request->get('from')) {
            $query->whereDate('sale_date', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('sale_date', '<=', $to);
        }
        if ($shift = $request->get('shift')) {
            $query->where('shift', $shift);
        }

        return DataTables::eloquent($query->latest('sale_date')->latest('id'))
            ->addColumn('actions', fn (CashSale $s) => view('admin.cash-sales._actions', ['sale' => $s])->render())
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.cash-sales.create', ['methods' => PaymentMethod::where('is_active', true)->get()]);
    }

    public function store(StoreCashSaleRequest $request): RedirectResponse
    {
        try {
            $sale = $this->service->create($request->validated());
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.cash-sales.index')
            ->with('success', "Cash sale {$sale->cash_sale_no} recorded: {$sale->quantity} L for " . money($sale->total_amount) . '.');
    }

    public function edit(CashSale $cashSale): View
    {
        return view('admin.cash-sales.edit', ['sale' => $cashSale, 'methods' => PaymentMethod::where('is_active', true)->get()]);
    }

    public function update(UpdateCashSaleRequest $request, CashSale $cashSale): RedirectResponse
    {
        $cashSale->update($request->validated());

        return redirect()->route('admin.cash-sales.index')->with('success', "Cash sale {$cashSale->cash_sale_no} updated.");
    }

    public function destroy(CashSale $cashSale): RedirectResponse
    {
        if ($cashSale->ledgerEntry()->exists()) {
            return back()->with('error', "Can't delete {$cashSale->cash_sale_no} — it has already posted to the stock ledger.");
        }

        $no = $cashSale->cash_sale_no;
        $cashSale->delete();

        return redirect()->route('admin.cash-sales.index')->with('success', "{$no} deleted.");
    }
}
