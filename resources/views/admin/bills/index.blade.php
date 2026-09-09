@extends('layouts.app')

@section('title', 'Bills')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Bills</h4>
    @can('generate', App\Models\Bill::class)
    <a href="{{ route('admin.bills.generate-form') }}" class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-plus me-1"></i> Generate Bills
    </a>
    @endcan
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterStatus">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="generated">Generated</option>
                    <option value="partially_paid">Partially Paid</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterMonth">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected($m == now()->month)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterYear">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" @selected($y == now()->year)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="billsTable">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Period</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const table = $('#billsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.bills.data') }}',
            data: function (d) {
                d.status = $('#filterStatus').val();
                d.month = $('#filterMonth').val();
                d.year = $('#filterYear').val();
            }
        },
        columns: [
            { data: 'invoice_number', name: 'invoice_number' },
            { data: 'customer_name', name: 'customer.name', orderable: false, searchable: false },
            { data: 'period', name: 'bill_month', orderable: false, searchable: false },
            { data: 'total_amount', name: 'total_amount' },
            { data: 'paid_amount', name: 'paid_amount' },
            { data: 'outstanding_amount', name: 'outstanding_amount' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'desc']],
        pageLength: 25,
    });

    $('#filterStatus, #filterMonth, #filterYear').on('change', () => table.ajax.reload());

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Delete this bill?', text: 'Linked daily entries will be unlocked and unbilled.',
            icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545',
        }).then((r) => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
