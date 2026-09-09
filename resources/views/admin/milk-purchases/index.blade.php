@extends('layouts.app')

@section('title', 'Incoming Milk')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="mb-0">Incoming Milk (Purchases)</h4>
        <div class="text-muted small">Current Available Stock: <strong class="text-success">{{ number_format($currentStock, 2) }} L</strong></div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.milk-stock.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-water me-1"></i> Stock Ledger</a>
        @can('create', App\Models\MilkPurchase::class)
        <a href="{{ route('admin.milk-purchases.create') }}" class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i> Record Purchase</a>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterSupplier">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterStatus">
                    <option value="">All Payment Status</option>
                    <option value="paid">Paid</option>
                    <option value="partial">Partial</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div class="col-6 col-md-3"><input type="date" class="form-control form-control-sm" id="filterFrom"></div>
            <div class="col-6 col-md-3"><input type="date" class="form-control form-control-sm" id="filterTo"></div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="purchasesTable">
                <thead>
                    <tr>
                        <th>Incoming No.</th><th>Date</th><th>Shift</th><th>Supplier</th>
                        <th>Milk Type</th><th>Qty (L)</th><th>Rate</th><th>Amount</th><th>Status</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="paymentForm" method="POST">
            @csrf
            <div class="modal-header"><h6 class="modal-title">Record Payment</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" min="0.01" name="amount" id="paymentAmount" class="form-control" required>
                <div class="form-text">Outstanding: <span id="paymentOutstanding"></span></div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-success btn-sm">Save</button></div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const table = $('#purchasesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.milk-purchases.data') }}',
            data: function (d) {
                d.supplier_id = $('#filterSupplier').val();
                d.payment_status = $('#filterStatus').val();
                d.from = $('#filterFrom').val();
                d.to = $('#filterTo').val();
            }
        },
        columns: [
            { data: 'incoming_no', name: 'incoming_no' },
            { data: 'purchase_date', name: 'purchase_date' },
            { data: 'shift', name: 'shift' },
            { data: 'supplier_name', name: 'supplier.name', orderable: false, searchable: false },
            { data: 'milk_type', name: 'milk_type' },
            { data: 'quantity', name: 'quantity' },
            { data: 'rate', name: 'rate' },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'status_badge', name: 'payment_status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[1, 'desc']],
        pageLength: 25,
    });

    $('#filterSupplier, #filterStatus, #filterFrom, #filterTo').on('change', () => table.ajax.reload());

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({ title: 'Delete this purchase?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545' })
            .then((r) => { if (r.isConfirmed) form.submit(); });
    });

    $(document).on('click', '.js-record-payment', function () {
        $('#paymentForm').attr('action', $(this).data('url'));
        $('#paymentOutstanding').text('₹' + Number($(this).data('outstanding')).toFixed(2));
        $('#paymentAmount').attr('max', $(this).data('outstanding')).val($(this).data('outstanding'));
        new bootstrap.Modal('#paymentModal').show();
    });
});
</script>
@endpush
