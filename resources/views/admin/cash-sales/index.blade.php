@extends('layouts.app')

@section('title', 'Cash Sales')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="mb-0">Cash Sales</h4>
        <div class="text-muted small">Available Stock: <strong class="text-success">{{ number_format($currentStock, 2) }} L</strong></div>
    </div>
    @can('create', App\Models\CashSale::class)
    <a href="{{ route('admin.cash-sales.create') }}" class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i> New Cash Sale</a>
    @endcan
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterShift">
                    <option value="">Both Shifts</option>
                    <option value="morning">Morning</option>
                    <option value="evening">Evening</option>
                </select>
            </div>
            <div class="col-6 col-md-3"><input type="date" class="form-control form-control-sm" id="filterFrom"></div>
            <div class="col-6 col-md-3"><input type="date" class="form-control form-control-sm" id="filterTo"></div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="cashSalesTable">
                <thead>
                    <tr><th>Cash Sale No.</th><th>Date</th><th>Shift</th><th>Milk Type</th><th>Qty (L)</th><th>Collection</th><th class="text-end">Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const table = $('#cashSalesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.cash-sales.data') }}',
            data: function (d) {
                d.shift = $('#filterShift').val();
                d.from = $('#filterFrom').val();
                d.to = $('#filterTo').val();
            }
        },
        columns: [
            { data: 'cash_sale_no', name: 'cash_sale_no' },
            { data: 'sale_date', name: 'sale_date' },
            { data: 'shift', name: 'shift' },
            { data: 'milk_type', name: 'milk_type' },
            { data: 'quantity', name: 'quantity' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[1, 'desc']],
        pageLength: 25,
    });

    $('#filterShift, #filterFrom, #filterTo').on('change', () => table.ajax.reload());

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({ title: 'Delete this cash sale?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545' })
            .then((r) => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
