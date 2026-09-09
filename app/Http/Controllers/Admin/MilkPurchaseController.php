<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MilkPurchase\StoreMilkPurchaseRequest;
use App\Http\Requests\MilkPurchase\UpdateMilkPurchaseRequest;
use App\Models\MilkPurchase;
use App\Models\PaymentMethod;
use App\Models\Supplier;
use App\Services\MilkPurchaseService;
use App\Services\MilkStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class MilkPurchaseController extends Controller
{
    public function __construct(
        protected MilkPurchaseService $service,
        protected MilkStockService $stock,
    ) {
        $this->authorizeResource(MilkPurchase::class, 'milk_purchase');
    }

    public function index(Request $request): View
    {
        if ($request->ajax()) {
            abort(400, 'Use /admin/milk-purchases/data for AJAX.');
        }

        return view('admin.milk-purchases.index', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'currentStock' => $this->stock->currentStock(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Incoming Milk' => null],
        ]);
    }

    public function data(Request $request)
    {
        $query = MilkPurchase::with('supplier');

        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }
        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }
        if ($from = $request->get('from')) {
            $query->whereDate('purchase_date', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('purchase_date', '<=', $to);
        }

        return DataTables::eloquent($query->latest('purchase_date')->latest('id'))
            ->addColumn('supplier_name', fn (MilkPurchase $p) => $p->supplier->name ?? '—')
            ->addColumn('status_badge', function (MilkPurchase $p) {
                $map = ['paid' => 'success', 'partial' => 'warning', 'pending' => 'danger'];
                return '<span class="badge text-bg-' . $map[$p->payment_status] . '">' . ucfirst($p->payment_status) . '</span>';
            })
            ->addColumn('actions', fn (MilkPurchase $p) => view('admin.milk-purchases._actions', ['purchase' => $p])->render())
            ->rawColumns(['status_badge', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.milk-purchases.create', $this->formData());
    }

    public function store(StoreMilkPurchaseRequest $request): RedirectResponse
    {
        try {
            $purchase = $this->service->create($request->validated());
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.milk-purchases.index')
            ->with('success', "Incoming milk {$purchase->incoming_no} recorded: +{$purchase->quantity} L.");
    }

    public function edit(MilkPurchase $milkPurchase): View
    {
        return view('admin.milk-purchases.edit', $this->formData() + ['purchase' => $milkPurchase]);
    }

    public function update(UpdateMilkPurchaseRequest $request, MilkPurchase $milkPurchase): RedirectResponse
    {
        $milkPurchase->update($request->validated());

        return redirect()->route('admin.milk-purchases.index')
            ->with('success', "Incoming milk {$milkPurchase->incoming_no} updated.");
    }

    public function destroy(MilkPurchase $milkPurchase): RedirectResponse
    {
        if ($milkPurchase->ledgerEntry()->exists()) {
            return back()->with('error', "Can't delete {$milkPurchase->incoming_no} — it has already posted to the stock ledger. Use a wastage/adjustment entry to correct stock instead.");
        }

        $incomingNo = $milkPurchase->incoming_no;
        $milkPurchase->delete();

        return redirect()->route('admin.milk-purchases.index')->with('success', "{$incomingNo} deleted.");
    }

    /**
     * Record an additional payment against a purchase that was left partial/pending.
     */
    public function recordPayment(Request $request, MilkPurchase $milkPurchase): RedirectResponse
    {
        $this->authorize('update', $milkPurchase);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $milkPurchase->outstandingAmount()],
        ]);

        $this->service->recordPayment($milkPurchase, (float) $data['amount']);

        return back()->with('success', 'Payment recorded.');
    }

    protected function formData(): array
    {
        return [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'methods' => PaymentMethod::where('is_active', true)->get(),
        ];
    }
}
