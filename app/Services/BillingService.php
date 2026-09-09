<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Models\DailyEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BillingService
{
    /**
     * Generate bills for a given month/year across a set of customers (or all
     * active customers if none given). Skips customers who already have a bill
     * for that period, and skips customers with zero unbilled entries.
     *
     * @param  int[]|null  $customerIds
     * @return array{generated:int, skipped_existing:int, skipped_empty:int, bills: Collection<int, Bill>}
     */
    public function generateForMonth(int $month, int $year, ?array $customerIds, array $options = []): array
    {
        $periodStart = sprintf('%04d-%02d-01', $year, $month);
        $periodEnd = date('Y-m-t', strtotime($periodStart));

        $customers = $customerIds
            ? Customer::whereIn('id', $customerIds)->get()
            : Customer::active()->get();

        $generated = 0;
        $skippedExisting = 0;
        $skippedEmpty = 0;
        $bills = collect();

        foreach ($customers as $customer) {
            $alreadyBilled = Bill::where('customer_id', $customer->id)
                ->where('bill_month', $month)->where('bill_year', $year)
                ->exists();

            if ($alreadyBilled) {
                $skippedExisting++;
                continue;
            }

            $entries = DailyEntry::where('customer_id', $customer->id)
                ->whereBetween('entry_date', [$periodStart, $periodEnd])
                ->unbilled()
                ->get();

            if ($entries->isEmpty()) {
                $skippedEmpty++;
                continue;
            }

            $bill = $this->generateForCustomer($customer, $month, $year, $periodStart, $periodEnd, $entries, $options);
            $bills->push($bill);
            $generated++;
        }

        return compact('generated', 'skippedExisting', 'skippedEmpty', 'bills');
    }

    protected function generateForCustomer(
        Customer $customer,
        int $month,
        int $year,
        string $periodStart,
        string $periodEnd,
        Collection $entries,
        array $options
    ): Bill {
        return DB::transaction(function () use ($customer, $month, $year, $periodStart, $periodEnd, $entries, $options) {
            $morningQty = (float) $entries->where('shift', 'morning')->sum('quantity');
            $eveningQty = (float) $entries->where('shift', 'evening')->sum('quantity');
            $milkAmount = (float) $entries->sum('amount');

            $previousDue = (float) Bill::where('customer_id', $customer->id)
                ->whereIn('status', ['generated', 'partially_paid'])
                ->sum('outstanding_amount');

            $advanceAvailable = (float) $customer->advance_balance;
            $advanceToApply = min($advanceAvailable, $options['use_advance'] ?? $advanceAvailable);

            $bill = Bill::create([
                'bill_number' => Bill::generateBillNumber($month, $year),
                'invoice_number' => $this->generateInvoiceNumber($month, $year),
                'customer_id' => $customer->id,
                'bill_month' => $month,
                'bill_year' => $year,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'morning_qty' => $morningQty,
                'evening_qty' => $eveningQty,
                'milk_amount' => $milkAmount,
                'extra_charges' => $options['extra_charges'] ?? 0,
                'delivery_charges' => $options['delivery_charges'] ?? 0,
                'discount' => $options['discount'] ?? 0,
                'penalty' => $options['penalty'] ?? 0,
                'gst_percent' => $options['gst_percent'] ?? 0,
                'previous_due' => $previousDue,
                'advance_adjusted' => $advanceToApply,
                'status' => 'generated',
                'due_date' => $options['due_date'] ?? now()->addDays(7)->toDateString(),
                'generated_by' => auth()->id(),
            ]);

            $bill->recalculateTotals();
            $bill->save();

            // Line items: one per daily entry for full transparency on the invoice.
            foreach ($entries as $entry) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'daily_entry_id' => $entry->id,
                    'item_date' => $entry->entry_date,
                    'description' => ucfirst($entry->shift) . ' milk (' . ucfirst($entry->milk_type) . ')',
                    'quantity' => $entry->quantity,
                    'rate' => $entry->rate,
                    'amount' => $entry->amount,
                ]);
            }

            // Lock the billed entries and link them to this bill so they can't be re-billed or edited without an override.
            DailyEntry::whereIn('id', $entries->pluck('id'))->update([
                'bill_id' => $bill->id,
                'is_locked' => true,
            ]);

            if ($advanceToApply > 0) {
                $customer->decrement('advance_balance', $advanceToApply);
            }
            $customer->update(['outstanding_balance' => $bill->outstanding_amount]);

            ActivityLog::record('generated', $bill, description: "Bill {$bill->bill_number} generated for {$customer->consumer_id}");

            return $bill;
        });
    }

    protected function generateInvoiceNumber(int $month, int $year): string
    {
        $prefix = setting('invoice_prefix', 'INV');
        $sequence = Bill::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;

        return sprintf('%s/%04d/%02d/%05d', $prefix, $year, $month, $sequence);
    }

    /**
     * Render and persist the invoice PDF for a bill, returning the storage path.
     */
    public function generatePdf(Bill $bill): string
    {
        $bill->load(['customer', 'items']);

        $pdf = Pdf::loadView('admin.bills.pdf', ['bill' => $bill]);

        $path = "bills/{$bill->bill_number}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $bill->update(['pdf_path' => $path]);

        return $path;
    }

    /**
     * Recalculate and persist totals after a manual charge/discount/GST edit.
     */
    public function recalculate(Bill $bill): Bill
    {
        $bill->recalculateTotals();
        $bill->save();

        $bill->customer()->update(['outstanding_balance' => $bill->outstanding_amount]);

        return $bill;
    }
}
