<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    /**
     * Record a payment. If a bill is specified, it's applied against that bill's
     * outstanding balance and the bill's status/totals are recalculated. If no
     * bill is specified (or is_advance is set), the amount is credited to the
     * customer's advance balance instead, to be drawn down on the next bill.
     */
    public function record(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::findOrFail($data['customer_id']);
            $bill = ! empty($data['bill_id']) ? Bill::findOrFail($data['bill_id']) : null;

            $data['is_advance'] = $bill === null;
            $data['status'] = $data['status'] ?? 'cleared';
            $data['received_by'] = auth()->id();

            $payment = Payment::create($data);

            if ($bill) {
                $this->applyToBill($bill, $payment);
            } else {
                $customer->increment('advance_balance', $payment->netAmount());
            }

            $this->refreshCustomerOutstanding($customer);

            ActivityLog::record('created', $payment, new: $payment->toArray(),
                description: "Payment {$payment->receipt_number} recorded for {$customer->consumer_id}");

            return $payment->fresh();
        });
    }

    protected function applyToBill(Bill $bill, Payment $payment): void
    {
        $bill->increment('paid_amount', $payment->netAmount());
        $bill->refresh();
        $bill->recalculateTotals();
        $bill->save();
    }

    /**
     * Recompute the customer's headline outstanding_balance as the sum of
     * outstanding amounts across all their unpaid/partially-paid bills.
     */
    protected function refreshCustomerOutstanding(Customer $customer): void
    {
        $outstanding = Bill::where('customer_id', $customer->id)
            ->whereIn('status', ['generated', 'partially_paid'])
            ->sum('outstanding_amount');

        $customer->update(['outstanding_balance' => $outstanding]);
    }

    /**
     * Reverse a payment: subtract it back out of the bill (or advance balance)
     * and delete it. Used when a payment was recorded in error or a cheque bounced.
     */
    public function reverse(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $customer = $payment->customer;

            if ($payment->bill_id) {
                $bill = $payment->bill;
                $bill->decrement('paid_amount', $payment->netAmount());
                $bill->refresh();
                $bill->recalculateTotals();
                if ($bill->paid_amount <= 0) {
                    $bill->status = 'generated';
                }
                $bill->save();
            } else {
                $customer->decrement('advance_balance', $payment->netAmount());
            }

            $this->refreshCustomerOutstanding($customer);

            ActivityLog::record('deleted', $payment, description: "Payment {$payment->receipt_number} reversed/deleted");

            $payment->delete();
        });
    }

    public function generateReceiptPdf(Payment $payment): string
    {
        $payment->load(['customer', 'bill', 'paymentMethod']);

        $pdf = Pdf::loadView('admin.payments.pdf', ['payment' => $payment]);

        $path = "receipts/{$payment->receipt_number}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $payment->update(['receipt_pdf_path' => $path]);

        return $path;
    }
}
