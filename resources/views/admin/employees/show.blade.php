@extends('layouts.app')

@section('title', $employee->employee_id)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">{{ $employee->name }} <span class="text-muted fs-6">({{ $employee->employee_id }})</span></h4>
    @can('update', $employee)
    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i> Edit</a>
    @endcan
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                @if($employee->photo)
                    <img src="{{ Storage::url($employee->photo) }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover">
                @else
                    <i class="bi bi-person-circle text-muted mb-3" style="font-size:5rem"></i>
                @endif
                <h5 class="mb-0">{{ $employee->name }}</h5>
                <div class="text-muted small mb-2">{{ $employee->designation }}</div>
                <span class="badge text-bg-{{ ['active'=>'success','inactive'=>'secondary','terminated'=>'danger'][$employee->status] }}">
                    {{ ucfirst($employee->status) }}
                </span>
                <hr>
                <div class="text-start small">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Mobile</span><span>{{ $employee->mobile ?: '—' }}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Salary</span><span>{{ money($employee->salary) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Joined</span><span>{{ $employee->joining_date?->format('d M Y') ?? '—' }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Recent Attendance</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Status</th><th>Remarks</th></tr></thead>
                    <tbody>
                    @forelse($employee->attendances as $att)
                        <tr>
                            <td>{{ $att->attendance_date->format('d M Y') }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $att->status) }}</td>
                            <td>{{ $att->remarks }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No attendance recorded yet</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($employee->documents->isNotEmpty())
        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Documents</div>
            <ul class="list-group list-group-flush">
                @foreach($employee->documents as $doc)
                    <li class="list-group-item"><a href="{{ Storage::url($doc->file_path) }}" target="_blank">{{ $doc->title }}</a></li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection
