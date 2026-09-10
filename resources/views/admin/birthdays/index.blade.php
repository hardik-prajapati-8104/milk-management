@extends('layouts.app')

@section('title', 'Birthdays')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Birthdays</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.calendar.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-calendar3 me-1"></i> Back to Calendar
        </a>
        @can('create', App\Models\Birthday::class)
        <a href="{{ route('admin.birthdays.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Birthday
        </a>
        @endcan
    </div>
</div>

<div class="alert alert-info small">
    <i class="bi bi-info-circle me-1"></i>
    Employees and customers with a Date of Birth on their own profile show up on the Calendar automatically.
    This list is for manually-tracked birthdays only (family, vendors, VIP contacts, etc).
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="birthdaysTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Date of Birth</th>
                        <th>Next Occurrence</th>
                        <th>Turning</th>
                        <th>Days Until</th>
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
    $('#birthdaysTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.birthdays.data') }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'category_label', name: 'category', orderable: false, searchable: false },
            { data: 'date_of_birth', name: 'date_of_birth' },
            { data: 'next_occurrence', name: 'next_occurrence', orderable: false, searchable: false },
            { data: 'turning', name: 'turning', orderable: false, searchable: false },
            { data: 'days_until', name: 'days_until', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'asc']],
        pageLength: 25,
    });

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Delete this birthday?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545',
        }).then((r) => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
