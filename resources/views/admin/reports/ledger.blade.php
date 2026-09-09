@extends('layouts.app')

@section('title', 'Customer Ledger')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Customer Ledger</h4>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> All Reports</a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-12 col-md-4">
                <label class="form-label small mb-1">Customer</label>
                <select name="customer_id" class="form-select form-select-sm select2" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" @selected($customer->id == $c->id)>{{ $c->consumer_id }} — {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
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

<div class="card shadow-sm mb-3">
    <div class="card-body row g-3">
        <div class="col-md-4"><span class="text-muted small d-block">Customer</span><strong>{{ $customer->name }} ({{ $customer->consumer_id }})</strong></div>
        <div class="col-md-4"><span class="text-muted small d-block">Current Outstanding</span><strong class="text-danger">{{ money($customer->outstanding_balance) }}</strong></div>
        <div class="col-md-4"><span class="text-muted small d-block">Advance Balance</span><strong class="text-success">{{ money($customer->advance_balance) }}</strong></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Date</th><th>Type</th><th>Reference</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
                        <td><span class="badge text-bg-{{ $row['type'] === 'Bill' ? 'primary' : 'success' }}">{{ $row['type'] }}</span></td>
                        <td>{{ $row['reference'] }}</td>
                        <td class="text-end">{{ $row['debit'] ? money($row['debit']) : '—' }}</td>
                        <td class="text-end">{{ $row['credit'] ? money($row['credit']) : '—' }}</td>
                        <td class="text-end fw-semibold">{{ money($row['balance']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No bills or payments in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>$('.select2').select2({ theme: 'bootstrap-5', width: '100%' });</script>
@endpush
