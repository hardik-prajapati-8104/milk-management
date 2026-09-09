<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\DailyEntry;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Daily / Morning / Evening report: one row per customer for a given date,
     * optionally filtered to a single shift.
     */
    public function dailyReport(string $date, ?string $shift = null): Collection
    {
        $query = DailyEntry::with('customer')->where('entry_date', $date);
        if ($shift) {
            $query->where('shift', $shift);
        }

        return $query->orderBy('customer_id')->get();
    }

    /**
     * Milk Summary: total quantity & amount grouped by day across a date range.
     */
    public function milkSummary(string $from, string $to): Collection
    {
        return DailyEntry::selectRaw('entry_date, shift, SUM(quantity) as total_qty, SUM(amount) as total_amount, COUNT(DISTINCT customer_id) as customers')
            ->whereBetween('entry_date', [$from, $to])
            ->groupBy('entry_date', 'shift')
            ->orderBy('entry_date')
            ->get();
    }

    /**
     * Collection Report: payments received in a date range, grouped by payment method.
     */
    public function collectionReport(string $from, string $to): Collection
    {
        return Payment::with('paymentMethod')
            ->whereBetween('payment_date', [$from, $to])
            ->get();
    }

    /**
     * Outstanding Report: every customer with a non-zero outstanding balance.
     */
    public function outstandingReport(): Collection
    {
        return Customer::where('outstanding_balance', '>', 0)
            ->orderByDesc('outstanding_balance')
            ->get();
    }

    /**
     * Customer Ledger: a running-balance statement of bills and payments for one customer.
     */
    public function customerLedger(Customer $customer, string $from, string $to): Collection
    {
        $bills = $customer->bills()
            ->whereBetween('period_start', [$from, $to])
            ->get()
            ->map(fn (Bill $b) => [
                'date' => $b->created_at,
                'type' => 'Bill',
                'reference' => $b->invoice_number,
                'debit' => $b->total_amount,
                'credit' => 0,
            ]);

        $payments = $customer->payments()
            ->whereBetween('payment_date', [$from, $to])
            ->get()
            ->map(fn (Payment $p) => [
                'date' => $p->payment_date,
                'type' => 'Payment',
                'reference' => $p->receipt_number,
                'debit' => 0,
                'credit' => $p->netAmount(),
            ]);

        $ledger = $bills->concat($payments)->sortBy('date')->values();

        $running = 0;
        return $ledger->map(function ($row) use (&$running) {
            $running += $row['debit'] - $row['credit'];
            $row['balance'] = $running;
            return $row;
        });
    }

    /**
     * Area / Village / Route reports: milk quantity + amount aggregated by location.
     */
    public function areaReport(string $from, string $to): Collection
    {
        return DailyEntry::join('customers', 'customers.id', '=', 'daily_entries.customer_id')
            ->join('areas', 'areas.id', '=', 'customers.area_id')
            ->whereBetween('entry_date', [$from, $to])
            ->groupBy('areas.id', 'areas.name')
            ->selectRaw('areas.name as area_name, SUM(daily_entries.quantity) as total_qty, SUM(daily_entries.amount) as total_amount, COUNT(DISTINCT customers.id) as customers')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function villageReport(string $from, string $to): Collection
    {
        return DailyEntry::join('customers', 'customers.id', '=', 'daily_entries.customer_id')
            ->join('villages', 'villages.id', '=', 'customers.village_id')
            ->whereBetween('entry_date', [$from, $to])
            ->groupBy('villages.id', 'villages.name')
            ->selectRaw('villages.name as village_name, SUM(daily_entries.quantity) as total_qty, SUM(daily_entries.amount) as total_amount, COUNT(DISTINCT customers.id) as customers')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function routeReport(string $from, string $to): Collection
    {
        return DailyEntry::join('customers', 'customers.id', '=', 'daily_entries.customer_id')
            ->join('routes', 'routes.id', '=', 'customers.route_id')
            ->whereBetween('entry_date', [$from, $to])
            ->groupBy('routes.id', 'routes.name')
            ->selectRaw('routes.name as route_name, SUM(daily_entries.quantity) as total_qty, SUM(daily_entries.amount) as total_amount, COUNT(DISTINCT customers.id) as customers')
            ->orderByDesc('total_amount')
            ->get();
    }

    /**
     * Expense Report: expenses grouped by category for a date range.
     */
    public function expenseReport(string $from, string $to): Collection
    {
        return Expense::with('category')
            ->whereBetween('expense_date', [$from, $to])
            ->orderByDesc('expense_date')
            ->get();
    }

    /**
     * Profit & Loss: revenue (milk sales + other payments received) vs expenses,
     * for a date range.
     */
    public function profitLoss(string $from, string $to): array
    {
        $milkRevenue = (float) DailyEntry::whereBetween('entry_date', [$from, $to])->sum('amount');
        $paymentsCollected = (float) Payment::whereBetween('payment_date', [$from, $to])->sum('amount');
        $totalExpenses = (float) Expense::whereBetween('expense_date', [$from, $to])->sum('amount');

        $expenseByCategory = Expense::whereBetween('expense_date', [$from, $to])
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->selectRaw('expense_categories.name as category, SUM(expenses.amount) as total')
            ->orderByDesc('total')
            ->get();

        return [
            'milk_revenue' => $milkRevenue,
            'payments_collected' => $paymentsCollected,
            'total_expenses' => $totalExpenses,
            'net_profit' => $milkRevenue - $totalExpenses,
            'expense_by_category' => $expenseByCategory,
        ];
    }

    /**
     * Monthly / Yearly Summary: milk sold, revenue, collections, expenses, and
     * profit rolled up per month within the given year.
     */
    public function monthlySummary(int $year): Collection
    {
        $months = collect(range(1, 12));

        return $months->map(function ($month) use ($year) {
            $start = sprintf('%04d-%02d-01', $year, $month);
            $end = date('Y-m-t', strtotime($start));

            $milkAmount = (float) DailyEntry::whereBetween('entry_date', [$start, $end])->sum('amount');
            $collected = (float) Payment::whereBetween('payment_date', [$start, $end])->sum('amount');
            $expenses = (float) Expense::whereBetween('expense_date', [$start, $end])->sum('amount');

            return [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'milk_amount' => $milkAmount,
                'collected' => $collected,
                'expenses' => $expenses,
                'profit' => $milkAmount - $expenses,
            ];
        });
    }
}
