@extends('layouts.app')

@section('title', 'Edit Purchase')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Edit Purchase — {{ $purchase->incoming_no }}</h4>
    <p class="text-muted small mb-0">
        {{ $purchase->purchase_date->format('d M Y') }} · {{ ucfirst($purchase->shift) }} ·
        {{ $purchase->supplier->name }} · {{ number_format($purchase->quantity, 2) }} L @ {{ money($purchase->rate) }}
        <span class="text-muted">(quantity/rate/date are locked once posted to the stock ledger)</span>
    </p>
</div>

<form action="{{ route('admin.milk-purchases.update', $purchase) }}" method="POST" style="max-width:640px">
    @csrf
    @method('PUT')
    <div class="card shadow-sm">
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Fat %</label>
                <input type="number" step="0.01" min="0" max="100" name="fat_percent" class="form-control" value="{{ old('fat_percent', $purchase->fat_percent) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">SNF %</label>
                <input type="number" step="0.01" min="0" max="100" name="snf_percent" class="form-control" value="{{ old('snf_percent', $purchase->snf_percent) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Quality / Grade</label>
                <input type="text" name="quality_grade" class="form-control" value="{{ old('quality_grade', $purchase->quality_grade) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Payment Status</label>
                <select name="payment_status" class="form-select">
                    <option value="paid" @selected($purchase->payment_status == 'paid')>Paid</option>
                    <option value="partial" @selected($purchase->payment_status == 'partial')>Partial</option>
                    <option value="pending" @selected($purchase->payment_status == 'pending')>Pending</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Payment Method</label>
                <select name="payment_method_id" class="form-select">
                    <option value="">— Not specified —</option>
                    @foreach($methods as $method)
                        <option value="{{ $method->id }}" @selected($purchase->payment_method_id == $method->id)>{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $purchase->notes) }}</textarea>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.milk-purchases.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Save</button>
    </div>
</form>
@endsection
