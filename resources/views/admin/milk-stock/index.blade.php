@extends('layouts.app')

@section('title', 'Milk Stock')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Milk Stock Ledger</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.milk-purchases.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-in-down me-1"></i> Incoming Milk</a>
    </div>
</div>

<div class="card shadow-sm mb-3 border-success">
    <div class="card-body text-center">
        <div class="text-muted small">Available Milk Stock (Right Now)</div>
        <div class="display-6 fw-bold text-success">{{ number_format($currentStock, 2) }} L</div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">Shift</label>
                <select name="shift" class="form-select form-select-sm">
                    <option value="">Both Shifts</option>
                    <option value="morning" @selected($shift === 'morning')>Morning</option>
                    <option value="evening" @selected($shift === 'evening')>Evening</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button class="btn btn-sm btn-primary w-100" type="submit">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3 text-center">
    <div class="col-6 col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Opening</div>
            <div class="fs-5 fw-bold">{{ number_format($breakdown['opening'], 2) }} L</div>
        </div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Incoming</div>
            <div class="fs-5 fw-bold text-success">+{{ number_format($breakdown['incoming'], 2) }} L</div>
        </div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Card Sales</div>
            <div class="fs-5 fw-bold text-danger">-{{ number_format($breakdown['cardSales'], 2) }} L</div>
        </div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Cash Sales</div>
            <div class="fs-5 fw-bold text-danger">-{{ number_format($breakdown['cashSales'], 2) }} L</div>
        </div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Dairy Sales</div>
            <div class="fs-5 fw-bold text-danger">-{{ number_format($breakdown['dairySales'], 2) }} L</div>
        </div></div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card shadow-sm h-100 border-success"><div class="card-body">
            <div class="text-muted small">Closing</div>
            <div class="fs-5 fw-bold">{{ number_format($breakdown['closing'], 2) }} L</div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-transparent fw-semibold">Full Ledger</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle w-100" id="ledgerTable">
            <thead class="table-light">
                <tr><th>Code</th><th>Date</th><th>Shift</th><th>Type</th><th>Dir</th><th class="text-end">Qty (L)</th><th class="text-end">Before</th><th class="text-end">After</th><th class="text-end">Amount</th></tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#ledgerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.milk-stock.data') }}',
            data: function (d) {
                d.date = '{{ $date }}';
                d.shift = '{{ $shift }}';
            }
        },
        columns: [
            { data: 'code', name: 'code' },
            { data: 'movement_date', name: 'movement_date' },
            { data: 'shift', name: 'shift' },
            { data: 'type_label', name: 'movement_type', orderable: false, searchable: false },
            { data: 'direction_badge', name: 'direction', orderable: false, searchable: false },
            { data: 'quantity', name: 'quantity', className: 'text-end' },
            { data: 'stock_before', name: 'stock_before', className: 'text-end' },
            { data: 'stock_after', name: 'stock_after', className: 'text-end' },
            { data: 'amount', name: 'amount', className: 'text-end' },
        ],
        order: [[0, 'desc']],
        pageLength: 25,
    });
});
</script>
@endpush
