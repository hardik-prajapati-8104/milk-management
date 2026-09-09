@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Expenses</h4>
    <div class="d-flex gap-2">
        @can('export', App\Models\Expense::class)
        <a href="{{ route('admin.expenses.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Export
        </a>
        @endcan
        @can('create', App\Models\Expense::class)
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Expense
        </a>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterCategory">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <input type="date" class="form-control form-control-sm" id="filterFrom">
            </div>
            <div class="col-6 col-md-3">
                <input type="date" class="form-control form-control-sm" id="filterTo">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="expensesTable">
                <thead>
                    <tr>
                        <th>Expense #</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Paid To</th>
                        <th>Method</th>
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
    const table = $('#expensesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.expenses.data') }}',
            data: function (d) {
                d.category_id = $('#filterCategory').val();
                d.from = $('#filterFrom').val();
                d.to = $('#filterTo').val();
            }
        },
        columns: [
            { data: 'expense_number', name: 'expense_number' },
            { data: 'expense_date', name: 'expense_date' },
            { data: 'category_name', name: 'category.name', orderable: false, searchable: false },
            { data: 'paid_to', name: 'paid_to' },
            { data: 'method_name', name: 'paymentMethod.name', orderable: false, searchable: false },
            { data: 'amount', name: 'amount' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[1, 'desc']],
        pageLength: 25,
    });

    $('#filterCategory, #filterFrom, #filterTo').on('change', () => table.ajax.reload());

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Delete this expense?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545',
        }).then((r) => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
