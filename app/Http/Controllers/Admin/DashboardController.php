<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\DailyEntry;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $stats = [
            'today_morning_qty' => DailyEntry::forDate($today)->forShift('morning')->sum('quantity'),
            'today_evening_qty' => DailyEntry::forDate($today)->forShift('evening')->sum('quantity'),
            'today_sales_amount' => DailyEntry::forDate($today)->sum('amount'),
            'total_customers' => Customer::active()->count(),
            'pending_payments' => Bill::whereIn('status', ['generated', 'partially_paid'])->sum('outstanding_amount'),
            'month_revenue' => Payment::whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount'),
            'month_expenses' => Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount'),
            'month_outstanding' => Bill::whereBetween('period_start', [$monthStart, $monthEnd])->sum('outstanding_amount'),
        ];

        $latestEntries = DailyEntry::with('customer')->latest()->limit(8)->get();
        $latestPayments = Payment::with('customer')->latest()->limit(8)->get();

        $monthlySales = DailyEntry::selectRaw('DATE(entry_date) as d, SUM(amount) as total')
            ->whereBetween('entry_date', [now()->subDays(29)->toDateString(), $today])
            ->groupBy('d')->orderBy('d')->pluck('total', 'd');

        $topCustomers = Bill::select('customer_id', DB::raw('SUM(total_amount) as total'))
            ->whereBetween('period_start', [$monthStart, $monthEnd])
            ->groupBy('customer_id')->orderByDesc('total')->limit(5)
            ->with('customer')->get();

        return view('admin.dashboard.index', compact('stats', 'latestEntries', 'latestPayments', 'monthlySales', 'topCustomers'));
    }
}
