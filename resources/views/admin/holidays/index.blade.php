@extends('layouts.app')

@section('title', 'Holidays')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Holidays</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.calendar.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-calendar3 me-1"></i> Back to Calendar
        </a>
        @can('create', App\Models\Holiday::class)
        <a href="{{ route('admin.holidays.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Holiday
        </a>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="holidaysTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Recurrence</th>
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
    $('#holidaysTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.holidays.data') }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'date', name: 'date' },
            { data: 'type_label', name: 'type', orderable: false, searchable: false },
            { data: 'recurring_label', name: 'is_recurring_yearly', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[1, 'asc']],
        pageLength: 25,
    });

    $(document).on('submit', '.js-delete-form', function (e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Delete this holiday?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545',
        }).then((r) => { if (r.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
