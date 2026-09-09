@extends('layouts.app')

@section('title', 'Edit Cash Sale')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Edit Cash Sale — {{ $sale->cash_sale_no }}</h4>
    <p class="text-muted small mb-0">
        {{ $sale->sale_date->format('d M Y') }} · {{ ucfirst($sale->shift) }} ·
        {{ number_format($sale->quantity, 2) }} L for {{ money($sale->total_amount) }}
        <span class="text-muted">(quantity/amount/date are locked once posted to the stock ledger)</span>
    </p>
</div>

<form action="{{ route('admin.cash-sales.update', $sale) }}" method="POST" style="max-width:480px">
    @csrf
    @method('PUT')
    <div class="card shadow-sm">
        <div class="card-body row g-3">
            <div class="col-12">
                <label class="form-label">Payment Method</label>
                <select name="payment_method_id" class="form-select">
                    <option value="">— Not specified —</option>
                    @foreach($methods as $method)
                        <option value="{{ $method->id }}" @selected($sale->payment_method_id == $method->id)>{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $sale->notes) }}</textarea>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.cash-sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Save</button>
    </div>
</form>
@endsection
