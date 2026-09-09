@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="mb-0">{{ $product->name }} <span class="text-muted fs-6">({{ $product->sku }})</span></h4>
        <div class="text-muted small">Current Stock: <strong>{{ number_format($product->current_stock, 3) }} {{ $product->unit }}</strong></div>
    </div>
    @can('update', $product)
    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i> Edit</a>
    @endcan
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Record Stock Movement</div>
            <div class="card-body">
                <form action="{{ route('admin.products.record-movement', $product) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Type</label>
                        <select name="type" class="form-select form-select-sm" required>
                            <option value="collection">Collection (in)</option>
                            <option value="purchase">Purchase (in)</option>
                            <option value="transfer_in">Transfer In (in)</option>
                            <option value="adjustment">Adjustment (in)</option>
                            <option value="sale">Sale (out)</option>
                            <option value="wastage">Wastage (out)</option>
                            <option value="transfer_out">Transfer Out (out)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Supplier (optional)</label>
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">— None —</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Date</label>
                        <input type="date" name="movement_date" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Quantity ({{ $product->unit }})</label>
                        <input type="number" step="0.001" min="0.001" name="quantity" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Rate (optional — defaults to purchase/selling price)</label>
                        <input type="number" step="0.01" min="0" name="rate" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Remarks</label>
                        <input type="text" name="remarks" class="form-control form-control-sm">
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-plus-lg me-1"></i> Record Movement</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Stock Ledger</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Type</th><th>Supplier</th><th class="text-end">Qty</th><th class="text-end">Before</th><th class="text-end">After</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                    @forelse($movements as $m)
                        <tr>
                            <td>{{ $m->movement_date->format('d M Y') }}</td>
                            <td><span class="badge text-bg-{{ $m->isInbound() ? 'success' : 'danger' }}">{{ ucfirst(str_replace('_', ' ', $m->type)) }}</span></td>
                            <td>{{ $m->supplier->name ?? '—' }}</td>
                            <td class="text-end">{{ number_format($m->quantity, 3) }}</td>
                            <td class="text-end text-muted">{{ number_format($m->stock_before, 3) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($m->stock_after, 3) }}</td>
                            <td class="text-end">{{ money($m->amount) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No stock movements recorded yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent">{{ $movements->links() }}</div>
        </div>
    </div>
</div>
@endsection
