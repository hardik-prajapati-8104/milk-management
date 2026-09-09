@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Dashboard</h4>
    <span class="text-muted small">{{ now()->format('l, d M Y') }}</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Morning Qty (Today)</div>
                <div class="fs-4 fw-bold">{{ number_format($stats['today_morning_qty'], 2) }} L</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Evening Qty (Today)</div>
                <div class="fs-4 fw-bold">{{ number_format($stats['today_evening_qty'], 2) }} L</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Today's Sales</div>
                <div class="fs-4 fw-bold">{{ money($stats['today_sales_amount']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Active Customers</div>
                <div class="fs-4 fw-bold">{{ number_format($stats['total_customers']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Pending Payments</div>
                <div class="fs-4 fw-bold text-danger">{{ money($stats['pending_payments']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">This Month Revenue</div>
                <div class="fs-4 fw-bold text-success">{{ money($stats['month_revenue']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">This Month Expenses</div>
                <div class="fs-4 fw-bold">{{ money($stats['month_expenses']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Month Outstanding</div>
                <div class="fs-4 fw-bold text-warning">{{ money($stats['month_outstanding']) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Sales — Last 30 Days</div>
            <div class="card-body">
                <canvas id="salesTrendChart" height="90"></canvas>
            </div>
        </div>

        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold">Latest Daily Entries</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Customer</th><th>Date</th><th>Shift</th><th>Qty</th><th>Amount</th></tr></thead>
                    <tbody>
                    @forelse($latestEntries as $entry)
                        <tr>
                            <td>{{ $entry->customer->name ?? '—' }}</td>
                            <td>{{ $entry->entry_date->format('d M') }}</td>
                            <td class="text-capitalize">{{ $entry->shift }}</td>
                            <td>{{ number_format($entry->quantity, 2) }}</td>
                            <td>{{ money($entry->amount) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No entries yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Top Customers — This Month</div>
            <ul class="list-group list-group-flush">
                @forelse($topCustomers as $tc)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $tc->customer->name ?? '—' }}</span>
                        <span class="fw-semibold">{{ money($tc->total) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-3">No billing data yet this month</li>
                @endforelse
            </ul>
        </div>

        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent fw-semibold">Latest Payments</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Customer</th><th>Date</th><th>Amount</th></tr></thead>
                    <tbody>
                    @forelse($latestPayments as $payment)
                        <tr>
                            <td>{{ $payment->customer->name ?? '—' }}</td>
                            <td>{{ $payment->payment_date->format('d M') }}</td>
                            <td>{{ money($payment->amount) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No payments yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const salesTrendData = @json($monthlySales);
    const ctx = document.getElementById('salesTrendChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: Object.keys(salesTrendData).map(d => new Date(d).toLocaleDateString(undefined, { day: '2-digit', month: 'short' })),
            datasets: [{
                label: 'Sales (₹)',
                data: Object.values(salesTrendData),
                borderColor: '#2e7d32',
                backgroundColor: 'rgba(46,125,50,0.1)',
                tension: 0.3,
                fill: true,
            }],
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });
</script>
@endpush
