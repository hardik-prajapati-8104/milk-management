@extends('layouts.app')

@section('title', 'Mark Attendance')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Mark Attendance</h4>
    <input type="date" id="attendanceDate" class="form-control form-control-sm" style="width:auto" value="{{ $date }}">
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Employee</th><th>Designation</th><th style="width:320px">Status</th><th>Remarks</th></tr>
            </thead>
            <tbody id="attendanceBody">
                @foreach($employees as $employee)
                    @php $record = $existing->get($employee->id); @endphp
                    <tr data-employee-id="{{ $employee->id }}">
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->designation }}</td>
                        <td>
                            <div class="btn-group btn-group-sm js-status-group" role="group">
                                @foreach(['present' => 'Present', 'absent' => 'Absent', 'half_day' => 'Half Day', 'leave' => 'Leave', 'holiday' => 'Holiday'] as $val => $label)
                                    <input type="radio" class="btn-check" name="status_{{ $employee->id }}" id="status_{{ $employee->id }}_{{ $val }}"
                                           value="{{ $val }}" {{ ($record->status ?? 'present') === $val ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="status_{{ $employee->id }}_{{ $val }}">{{ $label }}</label>
                                @endforeach
                            </div>
                        </td>
                        <td><input type="text" class="form-control form-control-sm js-remarks" value="{{ $record->remarks ?? '' }}"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer text-end bg-transparent">
        <button class="btn btn-success" id="saveAttendanceBtn"><i class="bi bi-save me-1"></i> Save Attendance</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('attendanceDate').addEventListener('change', function () {
    window.location = '{{ route('admin.employees.attendance-form') }}?date=' + this.value;
});

document.getElementById('saveAttendanceBtn').addEventListener('click', function () {
    const records = Array.from(document.querySelectorAll('#attendanceBody tr')).map(row => {
        const checked = row.querySelector('input[type=radio]:checked');
        return {
            employee_id: row.dataset.employeeId,
            status: checked ? checked.value : 'present',
            remarks: row.querySelector('.js-remarks').value,
        };
    });

    this.disabled = true;
    $.post('{{ route('admin.employees.mark-attendance') }}', {
        attendance_date: document.getElementById('attendanceDate').value,
        records: records,
    }).done(function (res) {
        Swal.fire({ icon: 'success', title: 'Saved', text: res.message, timer: 2000, showConfirmButton: false });
    }).fail(function () {
        Swal.fire({ icon: 'error', title: 'Save failed' });
    }).always(() => { document.getElementById('saveAttendanceBtn').disabled = false; });
});
</script>
@endpush
