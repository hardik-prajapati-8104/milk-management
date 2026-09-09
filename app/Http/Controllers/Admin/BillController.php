<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\GenerateBillsRequest;
use App\Http\Requests\Bill\UpdateBillRequest;
use App\Models\ActivityLog;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Route as DeliveryRoute;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BillController extends Controller
{
    public function __construct(protected BillingService $billing)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Bill::class);

        if ($request->ajax()) {
            abort(400, 'Use /admin/bills/data for AJAX.');
        }

        return view('admin.bills.index', [
            'routes' => DeliveryRoute::active()->orderBy('name')->get(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Bills' => null],
        ]);
    }

    public function data(Request $request)
    {
        $this->authorize('viewAny', Bill::class);

        $query = Bill::with('customer')->latest('id');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($month = $request->get('month')) {
            $query->where('bill_month', $month);
        }
        if ($year = $request->get('year')) {
            $query->where('bill_year', $year);
        }

        return DataTables::eloquent($query)
            ->addColumn('customer_name', fn (Bill $b) => $b->customer->name ?? '—')
            ->addColumn('consumer_id', fn (Bill $b) => $b->customer->consumer_id ?? '—')
            ->addColumn('period', fn (Bill $b) => date('M Y', mktime(0, 0, 0, $b->bill_month, 1, $b->bill_year)))
            ->addColumn('status_badge', function (Bill $b) {
                $map = ['draft' => 'secondary', 'generated' => 'primary', 'partially_paid' => 'warning', 'paid' => 'success', 'cancelled' => 'danger'];
                return '<span class="badge text-bg-' . $map[$b->status] . '">' . ucwords(str_replace('_', ' ', $b->status)) . '</span>';
            })
            ->addColumn('actions', fn (Bill $b) => view('admin.bills._actions', ['bill' => $b])->render())
            ->rawColumns(['status_badge', 'actions'])
            ->toJson();
    }

    public function generateForm(): View
    {
        $this->authorize('generate', Bill::class);

        return view('admin.bills.generate', [
            'customers' => Customer::active()->orderBy('name')->get(),
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Bills' => route('admin.bills.index'),
                'Generate' => null,
            ],
        ]);
    }

    public function generate(GenerateBillsRequest $request): RedirectResponse
    {
        $result = $this->billing->generateForMonth(
            (int) $request->validated('month'),
            (int) $request->validated('year'),
            $request->validated('customer_ids') ?: null,
            $request->only(['gst_percent', 'delivery_charges', 'due_date'])
        );

        $message = "{$result['generated']} bills generated.";
        if ($result['skippedExisting']) {
            $message .= " {$result['skippedExisting']} already had a bill for this period.";
        }
        if ($result['skippedEmpty']) {
            $message .= " {$result['skippedEmpty']} had no unbilled entries.";
        }

        return redirect()->route('admin.bills.index')->with('success', $message);
    }

    public function show(Bill $bill): View
    {
        $this->authorize('view', $bill);

        $bill->load(['customer', 'items', 'payments']);

        return view('admin.bills.show', [
            'bill' => $bill,
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Bills' => route('admin.bills.index'),
                $bill->bill_number => null,
            ],
        ]);
    }

    public function edit(Bill $bill): View
    {
        $this->authorize('update', $bill);

        return view('admin.bills.edit', ['bill' => $bill]);
    }

    public function update(UpdateBillRequest $request, Bill $bill): RedirectResponse
    {
        $this->authorize('update', $bill);

        $old = $bill->toArray();
        $bill->fill($request->validated());
        $this->billing->recalculate($bill);

        ActivityLog::record('updated', $bill, old: $old, new: $bill->toArray(),
            description: "Bill {$bill->bill_number} charges adjusted");

        return redirect()->route('admin.bills.show', $bill)->with('success', 'Bill updated and totals recalculated.');
    }

    public function destroy(Bill $bill): RedirectResponse
    {
        $this->authorize('delete', $bill);

        // Un-billing: release the linked daily entries back to unbilled/unlocked before removing the bill.
        $bill->dailyEntries()->update(['bill_id' => null, 'is_locked' => false]);

        $billNumber = $bill->bill_number;
        $bill->delete();

        ActivityLog::record('deleted', description: "Draft bill {$billNumber} deleted");

        return redirect()->route('admin.bills.index')->with('success', "Bill {$billNumber} deleted.");
    }

    public function downloadPdf(Bill $bill): Response
    {
        $this->authorize('print', $bill);

        if (! $bill->pdf_path || ! Storage::disk('public')->exists($bill->pdf_path)) {
            $this->billing->generatePdf($bill);
            $bill->refresh();
        }

        return response(Storage::disk('public')->get($bill->pdf_path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $bill->bill_number . '.pdf"',
        ]);
    }

    /**
     * Build a WhatsApp "click to chat" link with a pre-filled invoice summary message.
     */
    public function whatsappLink(Bill $bill): RedirectResponse
    {
        $this->authorize('view', $bill);

        $bill->load('customer');
        $mobile = preg_replace('/\D/', '', $bill->customer->mobile);

        $text = rawurlencode(
            "Hi {$bill->customer->name}, your invoice {$bill->invoice_number} for " .
            date('M Y', mktime(0, 0, 0, $bill->bill_month, 1, $bill->bill_year)) .
            " is " . money($bill->total_amount) . ". Outstanding: " . money($bill->outstanding_amount) . ". Thank you!"
        );

        return redirect()->away("https://wa.me/{$mobile}?text={$text}");
    }
}
