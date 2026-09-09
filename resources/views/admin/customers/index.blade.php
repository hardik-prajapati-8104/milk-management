@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Customers</h4>
    <div class="d-flex gap-2">
        @can('export', App\Models\Customer::class)
        <a href="{{ route('admin.customers.export', request()->query()) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Export
        </a>
        @endcan
        @can('create', App\Models\Customer::class)
        <a href="{{ route('admin.customers.import-form') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-upload me-1"></i> Import
        </a>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Customer
        </a>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterStatus">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select form-select-sm" id="filterRoute">
                    <option value="">All Routes</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}">{{ $route->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="customersTable">
                <thead>
                    <tr>
                        <th>Consumer ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Village</th>
                        <th>Route</th>
                        <th>Milk Type</th>
                        <th>Status</th>
                        <th>Outstanding</th>
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
    const table = $('#customersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('admin.customers.data') }}',
            data: function (d) {
                d.status = $('#filterStatus').val();
                d.route_id = $('#filterRoute').val();
            }
        },
        columns: [
            { data: 'consumer_id', name: 'consumer_id' },
            { data: 'name', name: 'name' },
            { data: 'mobile', name: 'mobile' },
            { data: 'village_name', name: 'village.name', orderable: false, searchable: false },
            { data: 'route_name', name: 'route.name', orderable: false, searchable: false },
            { data: 'milk_type', name: 'milk_type' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'outstanding_balance', name: 'outstanding_balance' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'desc']],
        pageLength: 25,
    });

    $('#filterStatus, #filterRoute').on('change', () => table.ajax.reload());

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Delete this customer?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            confirmButtonColor: '#dc3545',
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endpush
