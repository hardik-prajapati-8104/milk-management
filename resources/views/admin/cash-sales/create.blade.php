@extends('layouts.app')

@section('title', 'New Cash Sale')

@section('content')
<div class="mb-3"><h4 class="mb-0">Record Cash Sale</h4></div>

<form action="{{ route('admin.cash-sales.store') }}" method="POST" style="max-width:560px">
    @csrf
    <div class="card shadow-sm">
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="sale_date" class="form-control" value="{{ old('sale_date', now()->toDateString()) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Shift <span class="text-danger">*</span></label>
                <select name="shift" class="form-select" required>
                    <option value="morning" @selected(old('shift', current_shift()) == 'morning')>Morning</option>
                    <option value="evening" @selected(old('shift', current_shift()) == 'evening')>Evening</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Milk Type <span class="text-danger">*</span></label>
                <select name="milk_type" class="form-select" required>
                    <option value="cow" @selected(old('milk_type') == 'cow')>Cow</option>
                    <option value="buffalo" @selected(old('milk_type') == 'buffalo')>Buffalo</option>
                    <option value="mixed" @selected(old('milk_type') == 'mixed')>Mixed</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Payment Method</label>
                <select name="payment_method_id" class="form-select">
                    <option value="">— Not specified —</option>
                    @foreach($methods as $method)
                        <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Milk Sold (Liters) <span class="text-danger">*</span></label>
                <input type="number" step="0.001" min="0.001" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Total Collection <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" name="total_amount" class="form-control" value="{{ old('total_amount') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.cash-sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Save</button>
    </div>
</form>
@endsection
