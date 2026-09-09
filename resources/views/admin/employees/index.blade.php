@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Employees</h4>
    <div class="d-flex gap-2">
        @can('viewAny', App\Models\Employee::class)
        <a href="{{ route('admin.employees.attendance-form') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-calendar-check me-1"></i> Mark Attendance
        </a>
        @endcan
        @can('create', App\Models\Employee::class)
        <a href="{{ route('admin.employees.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-person-plus me-1"></i> New Employee
        </a>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="employeesTable">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Mobile</th>
                        <th>Salary</th>
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
    $('#employeesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.employees.data') }}',
        columns: [
            { data: 'employee_id', name: 'employee_id' },
            { data: 'name', name: 'name' },
            { data: 'designation', name: 'designation' },
            { data: 'mobile', name: 'mobile' },
            { data: 'salary', name: 'salary' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'desc']],
        pageLength: 25,
    });

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Delete this employee?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545',
        }).then((r) => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
