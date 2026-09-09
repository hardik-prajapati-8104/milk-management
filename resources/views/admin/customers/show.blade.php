@extends('layouts.app')

@section('title', $customer->consumer_id)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">{{ $customer->name }} <span class="text-muted fs-6">({{ $customer->consumer_id }})</span></h4>
    <div class="d-flex gap-2">
        @can('update', $customer)
        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @endcan
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3 text-center">
            <div class="card-body">
                @if($customer->photo)
                    <img src="{{ Storage::url($customer->photo) }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover">
                @else
                    <i class="bi bi-person-circle text-muted mb-3" style="font-size:5rem"></i>
                @endif
                <h5 class="mb-0">{{ $customer->name }}</h5>
                <div class="text-muted small mb-2">{{ $customer->mobile }}</div>
                <span class="badge text-bg-{{ ['active'=>'success','inactive'=>'secondary','suspended'=>'danger'][$customer->status] }}">
                    {{ ucfirst($customer->status) }}
                </span>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Identification</div>
            <div class="card-body text-center">
                @if($customer->qr_code_path)
                    <div class="mb-3">
                        <div class="text-muted small mb-1">QR Code</div>
                        {!! Storage::disk('public')->get($customer->qr_code_path) !!}
                    </div>
                @endif
                <div class="text-muted small">Barcode: {{ $customer->barcode }}</div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Balances</div>
            <div class="card-body">
                <div class="d-flex justify-content-between"><span class="text-muted">Opening Balance</span><span>{{ money($customer->opening_balance) }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Advance</span><span>{{ money($customer->advance_balance) }}</span></div>
                <div class="d-flex justify-content-between fw-semibold border-top pt-2 mt-2">
                    <span>Outstanding</span><span class="text-danger">{{ money($customer->outstanding_balance) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Details</div>
            <div class="card-body row g-3">
                <div class="col-md-6"><span class="text-muted small d-block">Address</span>{{ $customer->address ?: '—' }}</div>
                <div class="col-md-3"><span class="text-muted small d-block">Village</span>{{ $customer->village->name ?? '—' }}</div>
                <div class="col-md-3"><span class="text-muted small d-block">Route</span>{{ $customer->route->name ?? '—' }}</div>
                <div class="col-md-3"><span class="text-muted small d-block">Milk Type</span>{{ ucfirst($customer->milk_type) }}</div>
                <div class="col-md-3"><span class="text-muted small d-block">Morning Rate</span>{{ money($customer->morning_rate) }}</div>
                <div class="col-md-3"><span class="text-muted small d-block">Evening Rate</span>{{ money($customer->evening_rate) }}</div>
                <div class="col-md-3"><span class="text-muted small d-block">Category</span>{{ $customer->category->name ?? '—' }}</div>
                <div class="col-md-3"><span class="text-muted small d-block">Joining Date</span>{{ $customer->joining_date?->format('d M Y') ?? '—' }}</div>
                @if($customer->notes)
                    <div class="col-12"><span class="text-muted small d-block">Notes</span>{{ $customer->notes }}</div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Recent Daily Entries</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Shift</th><th>Qty</th><th>Rate</th><th>Amount</th></tr></thead>
                    <tbody>
                    @forelse($customer->dailyEntries as $entry)
                        <tr>
                            <td>{{ $entry->entry_date->format('d M Y') }}</td>
                            <td class="text-capitalize">{{ $entry->shift }}</td>
                            <td>{{ number_format($entry->quantity, 2) }}</td>
                            <td>{{ money($entry->rate) }}</td>
                            <td>{{ money($entry->amount) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No entries recorded yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($customer->documents->isNotEmpty())
        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Documents</div>
            <ul class="list-group list-group-flush">
                @foreach($customer->documents as $doc)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank">{{ $doc->title }}</a>
                        <span class="text-muted small">{{ number_format($doc->file_size / 1024, 1) }} KB</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection
