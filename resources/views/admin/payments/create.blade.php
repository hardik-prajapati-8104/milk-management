@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Record Payment</h4>
</div>

<form action="{{ route('admin.payments.store') }}" method="POST" style="max-width:640px">
    @csrf
    <div class="card shadow-sm">
        <div class="card-body row g-3">
            <div class="col-12">
                <label class="form-label">Customer <span class="text-danger">*</span></label>
                <select name="customer_id" id="customerSelect" class="form-select select2" required {{ $bill ? 'disabled' : '' }}>
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id', $bill->customer_id ?? '') == $customer->id)>
                            {{ $customer->consumer_id }} — {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                @if($bill)
                    <input type="hidden" name="customer_id" value="{{ $bill->customer_id }}">
                @endif
            </div>

            <div class="col-12">
                <div class="alert alert-light border small py-2 mb-0" id="balanceInfo" style="display:{{ $bill ? 'block' : 'none' }}">
                    Outstanding: <strong id="balOutstanding">{{ $bill ? money($bill->outstanding_amount) : '—' }}</strong>
                    &nbsp;·&nbsp; Advance: <strong id="balAdvance">—</strong>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Apply to Bill (optional — leave blank to credit as advance)</label>
                <select name="bill_id" id="billSelect" class="form-select">
                    <option value="">— Advance / No specific bill —</option>
                    @if($bill)
                        <option value="{{ $bill->id }}" selected>{{ $bill->invoice_number }} — Outstanding {{ money($bill->outstanding_amount) }}</option>
                    @endif
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                <select name="payment_method_id" class="form-select" required>
                    @foreach($methods as $method)
                        <option value="{{ $method->id }}" @selected(old('payment_method_id') == $method->id)>{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', now()->toDateString()) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" id="amountInput" class="form-control @error('amount') is-invalid @enderror"
                       value="{{ old('amount', $bill->outstanding_amount ?? '') }}" required>
                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Discount</label>
                <input type="number" step="0.01" min="0" name="discount" class="form-control" value="{{ old('discount', 0) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Adjustment (+/-)</label>
                <input type="number" step="0.01" name="adjustment" class="form-control" value="{{ old('adjustment', 0) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Reference Number</label>
                <input type="text" name="reference_number" class="form-control" placeholder="Cheque no. / UPI ref / txn ID" value="{{ old('reference_number') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Remarks</label>
                <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}">
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Save Payment</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(function () {
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });

    function loadCustomerInfo(customerId) {
        if (!customerId) return;
        $.get(`/admin/payments/customer/${customerId}/outstanding`, function (res) {
            $('#balanceInfo').show();
            $('#balOutstanding').text('₹' + Number(res.outstanding_balance).toFixed(2));
            $('#balAdvance').text('₹' + Number(res.advance_balance).toFixed(2));

            const billSelect = $('#billSelect');
            billSelect.find('option:not(:first)').remove();
            res.bills.forEach(bill => {
                billSelect.append(`<option value="${bill.id}">${bill.invoice_number} — Outstanding ₹${Number(bill.outstanding_amount).toFixed(2)}</option>`);
            });
        });
    }

    $('#customerSelect').on('change', function () { loadCustomerInfo(this.value); });
    @if($bill)
        loadCustomerInfo({{ $bill->customer_id }});
    @endif

    $('#billSelect').on('change', function () {
        const selected = $(this).find('option:selected').text();
        const match = selected.match(/Outstanding ₹([\d.]+)/);
        if (match) $('#amountInput').val(match[1]);
    });
});
</script>
@endpush
