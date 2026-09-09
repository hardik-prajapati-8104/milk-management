@extends('layouts.app')

@section('title', $supplier->name)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="mb-0">{{ $supplier->name }} <span class="text-muted fs-6">({{ $supplier->supplier_code }})</span></h4>
        <div class="text-muted small">Outstanding: <strong class="text-danger">{{ money($supplier->outstanding_balance) }}</strong></div>
    </div>
    @can('update', $supplier)
    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i> Edit</a>
    @endcan
</div>

<div class="card shadow-sm">
    <div class="card-header bg-transparent fw-semibold">Purchase / Collection History</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead><tr><th>Date</th><th>Product</th><th>Type</th><th class="text-end">Qty</th><th class="text-end">Amount</th></tr></thead>
            <tbody>
            @forelse($movements as $m)
                <tr>
                    <td>{{ $m->movement_date->format('d M Y') }}</td>
                    <td>{{ $m->product->name ?? '—' }}</td>
                    <td><span class="badge text-bg-secondary">{{ ucfirst($m->type) }}</span></td>
                    <td class="text-end">{{ number_format($m->quantity, 3) }}</td>
                    <td class="text-end">{{ money($m->amount) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No history yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-transparent">{{ $movements->links() }}</div>
</div>
@endsection
