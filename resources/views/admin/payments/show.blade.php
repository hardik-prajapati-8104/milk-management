@extends('layouts.app')

@section('title', $payment->receipt_number)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Receipt {{ $payment->receipt_number }}</h4>
    <div class="d-flex gap-2">
        @can('print', $payment)
        <a href="{{ route('admin.payments.pdf', $payment) }}" target="_blank" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF Receipt
        </a>
        @endcan
        @if($payment->bill)
        <a href="{{ route('admin.bills.show', $payment->bill) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-receipt me-1"></i> View Bill
        </a>
        @endif
    </div>
</div>

<div class="card shadow-sm" style="max-width:600px">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <div>
                <div class="text-muted small">Received From</div>
                <div class="fw-semibold">{{ $payment->customer->name }}</div>
                <div class="text-muted small">{{ $payment->customer->consumer_id }}</div>
            </div>
            <div class="text-end">
                <div class="text-muted small">Date</div>
                <div class="fw-semibold">{{ $payment->payment_date->format('d M Y') }}</div>
            </div>
        </div>

        <table class="table table-sm">
            <tr><td class="text-muted">Payment Method</td><td class="text-end">{{ $payment->paymentMethod->name }}</td></tr>
            <tr><td class="text-muted">Reference</td><td class="text-end">{{ $payment->reference_number ?: '—' }}</td></tr>
            <tr><td class="text-muted">Type</td><td class="text-end">{{ $payment->is_advance ? 'Advance / No Bill' : ($payment->bill->invoice_number ?? '—') }}</td></tr>
            <tr><td class="text-muted">Amount</td><td class="text-end">{{ money($payment->amount) }}</td></tr>
            <tr><td class="text-muted">Discount</td><td class="text-end">-{{ money($payment->discount) }}</td></tr>
            <tr><td class="text-muted">Adjustment</td><td class="text-end">{{ money($payment->adjustment) }}</td></tr>
            <tr class="border-top"><td class="fw-bold">Net Amount</td><td class="text-end fw-bold fs-5">{{ money($payment->netAmount()) }}</td></tr>
        </table>

        @if($payment->remarks)
            <div class="text-muted small mt-2">Remarks: {{ $payment->remarks }}</div>
        @endif

        <div class="text-muted small mt-3">Received by {{ $payment->receivedBy->name ?? '—' }}</div>
    </div>
</div>
@endsection
