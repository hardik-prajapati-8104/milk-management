<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        if ($request->ajax()) {
            abort(400, 'Use /admin/payments/data for AJAX.');
        }

        return view('admin.payments.index', [
            'methods' => PaymentMethod::where('is_active', true)->get(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Payments' => null],
        ]);
    }

    public function data(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['customer', 'paymentMethod']);

        if ($methodId = $request->get('payment_method_id')) {
            $query->where('payment_method_id', $methodId);
        }
        if ($from = $request->get('from')) {
            $query->whereDate('payment_date', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('payment_date', '<=', $to);
        }

        return DataTables::eloquent($query->latest('id'))
            ->addColumn('customer_name', fn (Payment $p) => $p->customer->name ?? '—')
            ->addColumn('method_name', fn (Payment $p) => $p->paymentMethod->name ?? '—')
            ->addColumn('type_badge', fn (Payment $p) => $p->is_advance
                ? '<span class="badge text-bg-info">Advance</span>'
                : '<span class="badge text-bg-primary">Bill Payment</span>')
            ->addColumn('actions', fn (Payment $p) => view('admin.payments._actions', ['payment' => $p])->render())
            ->rawColumns(['type_badge', 'actions'])
            ->toJson();
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Payment::class);

        $bill = $request->filled('bill_id') ? Bill::with('customer')->find($request->bill_id) : null;

        return view('admin.payments.create', [
            'bill' => $bill,
            'customers' => Customer::active()->orderBy('name')->get(),
            'methods' => PaymentMethod::where('is_active', true)->get(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Payments' => route('admin.payments.index'), 'Record Payment' => null],
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $payment = $this->service->record($request->validated());

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', "Payment {$payment->receipt_number} recorded successfully.");
    }

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load(['customer', 'bill', 'paymentMethod', 'receivedBy']);

        return view('admin.payments.show', [
            'payment' => $payment,
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Payments' => route('admin.payments.index'),
                $payment->receipt_number => null,
            ],
        ]);
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $this->authorize('delete', $payment);

        $receiptNumber = $payment->receipt_number;
        $this->service->reverse($payment);

        return redirect()->route('admin.payments.index')->with('success', "Payment {$receiptNumber} reversed.");
    }

    public function downloadPdf(Payment $payment): Response
    {
        $this->authorize('print', $payment);

        if (! $payment->receipt_pdf_path || ! Storage::disk('public')->exists($payment->receipt_pdf_path)) {
            $this->service->generateReceiptPdf($payment);
            $payment->refresh();
        }

        return response(Storage::disk('public')->get($payment->receipt_pdf_path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $payment->receipt_number . '.pdf"',
        ]);
    }

    /**
     * Returns a customer's outstanding balance, advance balance, and open bills —
     * used by the create form's JS to auto-fill fields when a customer is picked.
     */
    public function customerOutstanding(Customer $customer)
    {
        $this->authorize('create', Payment::class);

        return response()->json([
            'outstanding_balance' => $customer->outstanding_balance,
            'advance_balance' => $customer->advance_balance,
            'bills' => $customer->bills()->whereIn('status', ['generated', 'partially_paid'])
                ->get(['id', 'bill_number', 'invoice_number', 'outstanding_amount']),
        ]);
    }
}
