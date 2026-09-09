@extends('layouts.app')

@section('title', 'Record Incoming Milk')

@section('content')
<div class="mb-3"><h4 class="mb-0">Record Incoming Milk Purchase</h4></div>

<form action="{{ route('admin.milk-purchases.store') }}" method="POST" style="max-width:720px">
    @csrf
    <div class="card shadow-sm">
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', now()->toDateString()) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Time</label>
                <input type="time" name="purchase_time" class="form-control" value="{{ old('purchase_time', now()->format('H:i')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Shift <span class="text-danger">*</span></label>
                <select name="shift" class="form-select" required>
                    <option value="morning" @selected(old('shift', current_shift()) == 'morning')>Morning</option>
                    <option value="evening" @selected(old('shift', current_shift()) == 'evening')>Evening</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Supplier / Farmer <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }} ({{ $supplier->supplier_code }})</option>
                    @endforeach
                </select>
                @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Milk Type <span class="text-danger">*</span></label>
                <select name="milk_type" class="form-select" required>
                    <option value="cow" @selected(old('milk_type') == 'cow')>Cow</option>
                    <option value="buffalo" @selected(old('milk_type') == 'buffalo')>Buffalo</option>
                    <option value="mixed" @selected(old('milk_type') == 'mixed')>Mixed</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Quantity (Liters) <span class="text-danger">*</span></label>
                <input type="number" step="0.001" min="0.001" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Rate per Liter <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="rate" id="rate" class="form-control" value="{{ old('rate') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Total Amount</label>
                <input type="text" id="totalPreview" class="form-control" disabled placeholder="Auto-calculated">
            </div>

            <div class="col-md-4">
                <label class="form-label">Fat %</label>
                <input type="number" step="0.01" min="0" max="100" name="fat_percent" class="form-control" value="{{ old('fat_percent') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">SNF %</label>
                <input type="number" step="0.01" min="0" max="100" name="snf_percent" class="form-control" value="{{ old('snf_percent') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Quality / Grade</label>
                <input type="text" name="quality_grade" class="form-control" placeholder="e.g. A, B, Premium" value="{{ old('quality_grade') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Payment Status <span class="text-danger">*</span></label>
                <select name="payment_status" id="paymentStatus" class="form-select" required>
                    <option value="paid" @selected(old('payment_status') == 'paid')>Paid</option>
                    <option value="partial" @selected(old('payment_status') == 'partial')>Partial</option>
                    <option value="pending" @selected(old('payment_status', 'pending') == 'pending')>Pending</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Payment Method</label>
                <select name="payment_method_id" class="form-select">
                    <option value="">— Not specified —</option>
                    @foreach($methods as $method)
                        <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4" id="partialAmountWrap" style="display:none">
                <label class="form-label">Amount Paid Now</label>
                <input type="number" step="0.01" min="0" name="paid_amount" class="form-control" value="{{ old('paid_amount', 0) }}">
            </div>

            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.milk-purchases.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Record Purchase</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function recalc() {
        const qty = parseFloat(document.getElementById('quantity').value) || 0;
        const rate = parseFloat(document.getElementById('rate').value) || 0;
        document.getElementById('totalPreview').value = '₹' + (qty * rate).toFixed(2);
    }
    document.getElementById('quantity').addEventListener('input', recalc);
    document.getElementById('rate').addEventListener('input', recalc);

    function togglePartial() {
        document.getElementById('partialAmountWrap').style.display =
            document.getElementById('paymentStatus').value === 'partial' ? '' : 'none';
    }
    document.getElementById('paymentStatus').addEventListener('change', togglePartial);
    togglePartial();
</script>
@endpush
