@extends('layouts.app')

@section('title', 'Profit & Loss')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Profit & Loss</h4>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> All Reports</a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-6 col-md-2">
                <button class="btn btn-sm btn-primary w-100" type="submit">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center"><div class="card-body">
            <div class="text-muted small">Milk Revenue</div>
            <div class="fs-5 fw-bold text-success">{{ money($milk_revenue) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center"><div class="card-body">
            <div class="text-muted small">Payments Collected</div>
            <div class="fs-5 fw-bold">{{ money($payments_collected) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center"><div class="card-body">
            <div class="text-muted small">Total Expenses</div>
            <div class="fs-5 fw-bold text-danger">{{ money($total_expenses) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm text-center border-{{ $net_profit >= 0 ? 'success' : 'danger' }}"><div class="card-body">
            <div class="text-muted small">Net Profit</div>
            <div class="fs-5 fw-bold {{ $net_profit >= 0 ? 'text-success' : 'text-danger' }}">{{ money($net_profit) }}</div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-transparent fw-semibold">Expenses by Category</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Category</th><th class="text-end">Amount</th><th class="text-end">% of Total</th></tr></thead>
            <tbody>
            @forelse($expense_by_category as $row)
                <tr>
                    <td>{{ $row->category }}</td>
                    <td class="text-end">{{ money($row->total) }}</td>
                    <td class="text-end">{{ $total_expenses > 0 ? number_format(($row->total / $total_expenses) * 100, 1) : 0 }}%</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted py-3">No expenses in this period.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
