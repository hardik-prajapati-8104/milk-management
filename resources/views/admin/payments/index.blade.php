@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Payments</h4>
    @can('create', App\Models\Payment::class)
    <a href="{{ route('admin.payments.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-cash-coin me-1"></i> Record Payment
    </a>
    @endcan
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterMethod">
                    <option value="">All Methods</option>
                    @foreach($methods as $method)
                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <input type="date" class="form-control form-control-sm" id="filterFrom" placeholder="From">
            </div>
            <div class="col-6 col-md-3">
                <input type="date" class="form-control form-control-sm" id="filterTo" placeholder="To">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="paymentsTable">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Type</th>
                        <th>Amount</th>
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
    const table = $('#paymentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.payments.data') }}',
            data: function (d) {
                d.payment_method_id = $('#filterMethod').val();
                d.from = $('#filterFrom').val();
                d.to = $('#filterTo').val();
            }
        },
        columns: [
            { data: 'receipt_number', name: 'receipt_number' },
            { data: 'customer_name', name: 'customer.name', orderable: false, searchable: false },
            { data: 'payment_date', name: 'payment_date' },
            { data: 'method_name', name: 'paymentMethod.name', orderable: false, searchable: false },
            { data: 'type_badge', name: 'is_advance', orderable: false, searchable: false },
            { data: 'amount', name: 'amount' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'desc']],
        pageLength: 25,
    });

    $('#filterMethod, #filterFrom, #filterTo').on('change', () => table.ajax.reload());

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Reverse this payment?', text: 'It will be removed and the linked bill/advance balance restored.',
            icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, reverse', confirmButtonColor: '#dc3545',
        }).then((r) => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
