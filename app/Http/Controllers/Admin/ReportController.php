<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GenericTableExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Excel as ExcelFacade;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reports)
    {
    }

    public function index(): View
    {
        $this->ensureCanView();

        return view('admin.reports.index', [
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Reports' => null],
        ]);
    }

    public function daily(Request $request): View
    {
        $this->ensureCanView();

        $date = $request->get('date', now()->toDateString());
        $shift = $request->get('shift');

        $rows = $this->reports->dailyReport($date, $shift);

        return view('admin.reports.table', [
            'title' => 'Daily Report',
            'subtitle' => \Carbon\Carbon::parse($date)->format('d M Y') . ($shift ? ' — ' . ucfirst($shift) : ''),
            'headings' => ['Consumer ID', 'Customer', 'Shift', 'Qty (L)', 'Rate', 'Amount', 'Status'],
            'rows' => $rows->map(fn ($e) => [
                $e->customer->consumer_id ?? '—', $e->customer->name ?? '—', ucfirst($e->shift),
                number_format($e->quantity, 2), money($e->rate), money($e->amount),
                $e->is_absent ? 'Absent' : ($e->is_holiday ? 'Holiday' : 'Delivered'),
            ]),
            'reportType' => 'daily',
            'filters' => $request->only('date', 'shift'),
            'breadcrumbs' => $this->crumbs('Daily Report'),
        ]);
    }

    public function milkSummary(Request $request): View
    {
        $this->ensureCanView();

        [$from, $to] = $this->range($request);
        $rows = $this->reports->milkSummary($from, $to);

        return view('admin.reports.table', [
            'title' => 'Milk Summary',
            'subtitle' => "{$from} to {$to}",
            'headings' => ['Date', 'Shift', 'Total Qty (L)', 'Total Amount', 'Customers'],
            'rows' => $rows->map(fn ($r) => [
                $r->entry_date->format('d M Y'), ucfirst($r->shift), number_format($r->total_qty, 2), money($r->total_amount), $r->customers,
            ]),
            'reportType' => 'milk-summary',
            'filters' => $request->only('from', 'to'),
            'breadcrumbs' => $this->crumbs('Milk Summary'),
        ]);
    }

    public function collection(Request $request): View
    {
        $this->ensureCanView();

        [$from, $to] = $this->range($request);
        $rows = $this->reports->collectionReport($from, $to);

        return view('admin.reports.table', [
            'title' => 'Collection Report',
            'subtitle' => "{$from} to {$to}",
            'headings' => ['Receipt #', 'Date', 'Customer', 'Method', 'Amount'],
            'rows' => $rows->map(fn ($p) => [
                $p->receipt_number, $p->payment_date->format('d M Y'), $p->customer->name ?? '—',
                $p->paymentMethod->name ?? '—', money($p->netAmount()),
            ]),
            'reportType' => 'collection',
            'filters' => $request->only('from', 'to'),
            'breadcrumbs' => $this->crumbs('Collection Report'),
        ]);
    }

    public function outstanding(): View
    {
        $this->ensureCanView();

        $rows = $this->reports->outstandingReport();

        return view('admin.reports.table', [
            'title' => 'Outstanding Report',
            'subtitle' => 'All customers with a pending balance',
            'headings' => ['Consumer ID', 'Customer', 'Mobile', 'Outstanding Balance'],
            'rows' => $rows->map(fn ($c) => [$c->consumer_id, $c->name, $c->mobile, money($c->outstanding_balance)]),
            'reportType' => 'outstanding',
            'filters' => [],
            'breadcrumbs' => $this->crumbs('Outstanding Report'),
        ]);
    }

    public function customerLedger(Request $request): View
    {
        $this->ensureCanView();

        $customerId = $request->get('customer_id');

        if (! $customerId) {
            // No customer selected yet: show the picker with an empty statement.
            $placeholder = new Customer(['name' => 'Select a customer', 'consumer_id' => '—']);
            $placeholder->outstanding_balance = 0;
            $placeholder->advance_balance = 0;

            return view('admin.reports.ledger', [
                'customer' => $placeholder,
                'rows' => collect(),
                'from' => $request->get('from', now()->startOfMonth()->toDateString()),
                'to' => $request->get('to', now()->toDateString()),
                'customers' => Customer::orderBy('name')->get(['id', 'name', 'consumer_id']),
                'breadcrumbs' => $this->crumbs('Customer Ledger'),
            ]);
        }

        $customer = Customer::findOrFail($customerId);
        [$from, $to] = $this->range($request);
        $rows = $this->reports->customerLedger($customer, $from, $to);

        return view('admin.reports.ledger', [
            'customer' => $customer,
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
            'customers' => Customer::orderBy('name')->get(['id', 'name', 'consumer_id']),
            'breadcrumbs' => $this->crumbs('Customer Ledger'),
        ]);
    }

    public function area(Request $request): View
    {
        $this->ensureCanView();
        [$from, $to] = $this->range($request);
        $rows = $this->reports->areaReport($from, $to);

        return view('admin.reports.table', [
            'title' => 'Area Report', 'subtitle' => "{$from} to {$to}",
            'headings' => ['Area', 'Total Qty (L)', 'Total Amount', 'Customers'],
            'rows' => $rows->map(fn ($r) => [$r->area_name, number_format($r->total_qty, 2), money($r->total_amount), $r->customers]),
            'reportType' => 'area', 'filters' => $request->only('from', 'to'),
            'breadcrumbs' => $this->crumbs('Area Report'),
        ]);
    }

    public function village(Request $request): View
    {
        $this->ensureCanView();
        [$from, $to] = $this->range($request);
        $rows = $this->reports->villageReport($from, $to);

        return view('admin.reports.table', [
            'title' => 'Village Report', 'subtitle' => "{$from} to {$to}",
            'headings' => ['Village', 'Total Qty (L)', 'Total Amount', 'Customers'],
            'rows' => $rows->map(fn ($r) => [$r->village_name, number_format($r->total_qty, 2), money($r->total_amount), $r->customers]),
            'reportType' => 'village', 'filters' => $request->only('from', 'to'),
            'breadcrumbs' => $this->crumbs('Village Report'),
        ]);
    }

    public function route(Request $request): View
    {
        $this->ensureCanView();
        [$from, $to] = $this->range($request);
        $rows = $this->reports->routeReport($from, $to);

        return view('admin.reports.table', [
            'title' => 'Route Report', 'subtitle' => "{$from} to {$to}",
            'headings' => ['Route', 'Total Qty (L)', 'Total Amount', 'Customers'],
            'rows' => $rows->map(fn ($r) => [$r->route_name, number_format($r->total_qty, 2), money($r->total_amount), $r->customers]),
            'reportType' => 'route', 'filters' => $request->only('from', 'to'),
            'breadcrumbs' => $this->crumbs('Route Report'),
        ]);
    }

    public function expense(Request $request): View
    {
        $this->ensureCanView();
        [$from, $to] = $this->range($request);
        $rows = $this->reports->expenseReport($from, $to);

        return view('admin.reports.table', [
            'title' => 'Expense Report', 'subtitle' => "{$from} to {$to}",
            'headings' => ['Expense #', 'Date', 'Category', 'Paid To', 'Amount'],
            'rows' => $rows->map(fn ($e) => [$e->expense_number, $e->expense_date->format('d M Y'), $e->category->name ?? '—', $e->paid_to, money($e->amount)]),
            'reportType' => 'expense', 'filters' => $request->only('from', 'to'),
            'breadcrumbs' => $this->crumbs('Expense Report'),
        ]);
    }

    public function profitLoss(Request $request): View
    {
        $this->ensureCanView();
        [$from, $to] = $this->range($request);
        $data = $this->reports->profitLoss($from, $to);

        return view('admin.reports.profit-loss', $data + [
            'from' => $from, 'to' => $to,
            'breadcrumbs' => $this->crumbs('Profit & Loss'),
        ]);
    }

    public function monthlySummary(Request $request): View
    {
        $this->ensureCanView();
        $year = (int) $request->get('year', now()->year);
        $rows = $this->reports->monthlySummary($year);

        return view('admin.reports.table', [
            'title' => 'Monthly Summary', 'subtitle' => (string) $year,
            'headings' => ['Month', 'Milk Amount', 'Collected', 'Expenses', 'Profit'],
            'rows' => $rows->map(fn ($r) => [$r['month'], money($r['milk_amount']), money($r['collected']), money($r['expenses']), money($r['profit'])]),
            'reportType' => 'monthly-summary', 'filters' => ['year' => $year],
            'breadcrumbs' => $this->crumbs('Monthly Summary'),
        ]);
    }

    /**
     * Generic export endpoint: re-runs the same query as the on-screen report
     * and streams it as xlsx, csv, or pdf based on ?format=.
     */
    public function export(Request $request, string $type)
    {
        abort_unless(auth()->user()->can('reports.export'), 403);

        $format = $request->get('format', 'xlsx');
        [$headings, $rows, $title] = $this->resolveExportData($type, $request);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.pdf', compact('headings', 'rows', 'title'));
            return $pdf->download($title . '.pdf');
        }

        $export = new GenericTableExport($headings, $rows, $title);

        return $format === 'csv'
            ? ExcelFacade::download($export, $title . '.csv', \Maatwebsite\Excel\Excel::CSV)
            : ExcelFacade::download($export, $title . '.xlsx');
    }

    protected function resolveExportData(string $type, Request $request): array
    {
        [$from, $to] = $this->range($request);

        return match ($type) {
            'daily' => [
                ['Consumer ID', 'Customer', 'Shift', 'Qty', 'Rate', 'Amount', 'Status'],
                $this->reports->dailyReport($request->get('date', now()->toDateString()), $request->get('shift'))
                    ->map(fn ($e) => [$e->customer->consumer_id ?? '', $e->customer->name ?? '', $e->shift, $e->quantity, $e->rate, $e->amount, $e->is_absent ? 'Absent' : ($e->is_holiday ? 'Holiday' : 'Delivered')])->toArray(),
                'daily-report',
            ],
            'milk-summary' => [
                ['Date', 'Shift', 'Total Qty', 'Total Amount', 'Customers'],
                $this->reports->milkSummary($from, $to)->map(fn ($r) => [$r->entry_date->format('Y-m-d'), $r->shift, $r->total_qty, $r->total_amount, $r->customers])->toArray(),
                'milk-summary',
            ],
            'collection' => [
                ['Receipt #', 'Date', 'Customer', 'Method', 'Amount'],
                $this->reports->collectionReport($from, $to)->map(fn ($p) => [$p->receipt_number, $p->payment_date->format('Y-m-d'), $p->customer->name ?? '', $p->paymentMethod->name ?? '', $p->netAmount()])->toArray(),
                'collection-report',
            ],
            'outstanding' => [
                ['Consumer ID', 'Customer', 'Mobile', 'Outstanding'],
                $this->reports->outstandingReport()->map(fn ($c) => [$c->consumer_id, $c->name, $c->mobile, $c->outstanding_balance])->toArray(),
                'outstanding-report',
            ],
            'area' => [
                ['Area', 'Total Qty', 'Total Amount', 'Customers'],
                $this->reports->areaReport($from, $to)->map(fn ($r) => [$r->area_name, $r->total_qty, $r->total_amount, $r->customers])->toArray(),
                'area-report',
            ],
            'village' => [
                ['Village', 'Total Qty', 'Total Amount', 'Customers'],
                $this->reports->villageReport($from, $to)->map(fn ($r) => [$r->village_name, $r->total_qty, $r->total_amount, $r->customers])->toArray(),
                'village-report',
            ],
            'route' => [
                ['Route', 'Total Qty', 'Total Amount', 'Customers'],
                $this->reports->routeReport($from, $to)->map(fn ($r) => [$r->route_name, $r->total_qty, $r->total_amount, $r->customers])->toArray(),
                'route-report',
            ],
            'expense' => [
                ['Expense #', 'Date', 'Category', 'Paid To', 'Amount'],
                $this->reports->expenseReport($from, $to)->map(fn ($e) => [$e->expense_number, $e->expense_date->format('Y-m-d'), $e->category->name ?? '', $e->paid_to, $e->amount])->toArray(),
                'expense-report',
            ],
            'monthly-summary' => [
                ['Month', 'Milk Amount', 'Collected', 'Expenses', 'Profit'],
                $this->reports->monthlySummary((int) $request->get('year', now()->year))
                    ->map(fn ($r) => [$r['month'], $r['milk_amount'], $r['collected'], $r['expenses'], $r['profit']])->toArray(),
                'monthly-summary',
            ],
            default => abort(404),
        };
    }

    protected function ensureCanView(): void
    {
        abort_unless(auth()->user()->can('reports.view'), 403);
    }

    protected function range(Request $request): array
    {
        return [
            $request->get('from', now()->startOfMonth()->toDateString()),
            $request->get('to', now()->toDateString()),
        ];
    }

    protected function crumbs(string $label): array
    {
        return [
            'Dashboard' => route('admin.dashboard'),
            'Reports' => route('admin.reports.index'),
            $label => null,
        ];
    }
}
