@extends('layouts.app')

@section('title', $bill->bill_number)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Invoice {{ $bill->invoice_number }}</h4>
    <div class="d-flex gap-2">
        @can('update', $bill)
        <a href="{{ route('admin.bills.edit', $bill) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i> Edit</a>
        @endcan
        @can('print', $bill)
        <a href="{{ route('admin.bills.pdf', $bill) }}" target="_blank" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
        <a href="{{ route('admin.bills.whatsapp', $bill) }}" target="_blank" class="btn btn-outline-success btn-sm"><i class="bi bi-whatsapp me-1"></i> WhatsApp</a>
        @endcan
        @can('create', App\Models\Payment::class)
        <a href="{{ route('admin.payments.create', ['bill_id' => $bill->id]) }}" class="btn btn-success btn-sm"><i class="bi bi-cash-coin me-1"></i> Record Payment</a>
        @endcan
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-muted small">Bill To</div>
                        <div class="fw-semibold">{{ $bill->customer->name }}</div>
                        <div class="small text-muted">{{ $bill->customer->consumer_id }} · {{ $bill->customer->mobile }}</div>
                        <div class="small text-muted">{{ $bill->customer->address }}</div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="text-muted small">Period</div>
                        <div class="fw-semibold">{{ $bill->period_start->format('d M') }} – {{ $bill->period_end->format('d M Y') }}</div>
                        <div class="text-muted small mt-2">Due Date</div>
                        <div>{{ $bill->due_date?->format('d M Y') ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Line Items</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Description</th><th>Qty</th><th>Rate</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                    @foreach($bill->items as $item)
                        <tr>
                            <td>{{ optional($item->item_date)->format('d M') }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ number_format($item->quantity, 2) }}</td>
                            <td>{{ money($item->rate) }}</td>
                            <td class="text-end">{{ money($item->amount) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($bill->payments->isNotEmpty())
        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Payments Against This Bill</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Receipt #</th><th>Date</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                    @foreach($bill->payments as $payment)
                        <tr>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                            <td class="text-end">{{ money($payment->amount) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Morning Qty</span><span>{{ number_format($bill->morning_qty, 2) }} L</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Evening Qty</span><span>{{ number_format($bill->evening_qty, 2) }} L</span></div>
                <div class="d-flex justify-content-between mb-2 border-bottom pb-2"><span class="text-muted">Total Qty</span><span class="fw-semibold">{{ number_format($bill->total_qty, 2) }} L</span></div>

                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Milk Amount</span><span>{{ money($bill->milk_amount) }}</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Extra Charges</span><span>{{ money($bill->extra_charges) }}</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Delivery Charges</span><span>{{ money($bill->delivery_charges) }}</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Penalty</span><span>{{ money($bill->penalty) }}</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Discount</span><span>-{{ money($bill->discount) }}</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Previous Due</span><span>{{ money($bill->previous_due) }}</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Advance Adjusted</span><span>-{{ money($bill->advance_adjusted) }}</span></div>
                <div class="d-flex justify-content-between mb-2 border-bottom pb-2"><span class="text-muted">GST ({{ $bill->gst_percent }}%)</span><span>{{ money($bill->gst_amount) }}</span></div>

                <div class="d-flex justify-content-between mb-1 fs-5 fw-bold"><span>Total</span><span>{{ money($bill->total_amount) }}</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">Paid</span><span class="text-success">{{ money($bill->paid_amount) }}</span></div>
                <div class="d-flex justify-content-between fw-semibold"><span>Outstanding</span><span class="text-danger">{{ money($bill->outstanding_amount) }}</span></div>

                <div class="mt-3">
                    <span class="badge text-bg-{{ ['draft'=>'secondary','generated'=>'primary','partially_paid'=>'warning','paid'=>'success','cancelled'=>'danger'][$bill->status] }} w-100 py-2">
                        {{ ucwords(str_replace('_', ' ', $bill->status)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
