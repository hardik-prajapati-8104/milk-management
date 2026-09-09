@extends('layouts.app')

@section('title', 'Edit Bill')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Edit Bill — {{ $bill->bill_number }}</h4>
    <p class="text-muted small">{{ $bill->customer->name }} ({{ $bill->customer->consumer_id }}) — {{ date('F Y', mktime(0,0,0,$bill->bill_month,1,$bill->bill_year)) }}</p>
</div>

<form action="{{ route('admin.bills.update', $bill) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card shadow-sm" style="max-width:640px">
        <div class="card-body row g-3">
            <div class="col-12">
                <div class="alert alert-light border small mb-0">
                    Milk Amount (from daily entries, not editable here): <strong>{{ money($bill->milk_amount) }}</strong>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Extra Charges</label>
                <input type="number" step="0.01" min="0" name="extra_charges" class="form-control" value="{{ old('extra_charges', $bill->extra_charges) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Delivery Charges</label>
                <input type="number" step="0.01" min="0" name="delivery_charges" class="form-control" value="{{ old('delivery_charges', $bill->delivery_charges) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Discount</label>
                <input type="number" step="0.01" min="0" name="discount" class="form-control" value="{{ old('discount', $bill->discount) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Penalty</label>
                <input type="number" step="0.01" min="0" name="penalty" class="form-control" value="{{ old('penalty', $bill->penalty) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">GST %</label>
                <input type="number" step="0.01" min="0" max="28" name="gst_percent" class="form-control" value="{{ old('gst_percent', $bill->gst_percent) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Advance Adjusted</label>
                <input type="number" step="0.01" min="0" name="advance_adjusted" class="form-control" value="{{ old('advance_adjusted', $bill->advance_adjusted) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $bill->due_date?->format('Y-m-d')) }}">
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3" style="max-width:640px">
        <a href="{{ route('admin.bills.show', $bill) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Save & Recalculate</button>
    </div>
</form>
@endsection
