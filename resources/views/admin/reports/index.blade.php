@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<h4 class="mb-3">Reports</h4>

@php
$reports = [
    ['Daily / Shift Report', 'View milk entries for any single date or shift.', route('admin.reports.daily'), 'bi-calendar-day'],
    ['Milk Summary', 'Total quantity and amount collected/sold per day.', route('admin.reports.milk-summary'), 'bi-droplet'],
    ['Collection Report', 'Payments received in a date range.', route('admin.reports.collection'), 'bi-cash-stack'],
    ['Outstanding Report', 'Customers with a pending balance.', route('admin.reports.outstanding'), 'bi-exclamation-circle'],
    ['Customer Ledger', 'Running statement of bills and payments for one customer.', route('admin.reports.customer-ledger', ['customer_id' => '']), 'bi-journal-text'],
    ['Area Report', 'Milk sales aggregated by area.', route('admin.reports.area'), 'bi-geo-alt'],
    ['Village Report', 'Milk sales aggregated by village.', route('admin.reports.village'), 'bi-signpost'],
    ['Route Report', 'Milk sales aggregated by delivery route.', route('admin.reports.route'), 'bi-truck'],
    ['Expense Report', 'All expenses in a date range, by category.', route('admin.reports.expense'), 'bi-wallet2'],
    ['Profit & Loss', 'Revenue vs expenses for a date range.', route('admin.reports.profit-loss'), 'bi-graph-up-arrow'],
    ['Monthly Summary', 'Milk, collections, expenses and profit rolled up by month.', route('admin.reports.monthly-summary'), 'bi-calendar3-range'],
];
@endphp

<div class="row g-3">
    @foreach($reports as [$name, $desc, $url, $icon])
    <div class="col-md-6 col-lg-4">
        <a href="{{ $url }}" class="text-decoration-none text-body">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <i class="bi {{ $icon }} fs-3 text-success mb-2 d-block"></i>
                    <h6 class="mb-1">{{ $name }}</h6>
                    <p class="text-muted small mb-0">{{ $desc }}</p>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endsection
