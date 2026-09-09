@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="mb-0">{{ $title }}</h4>
        <p class="text-muted small mb-0">{{ $subtitle }}</p>
    </div>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> All Reports
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
            @if(array_key_exists('date', $filters))
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">Date</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ $filters['date'] ?? now()->toDateString() }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">Shift</label>
                    <select name="shift" class="form-select form-select-sm">
                        <option value="">Both</option>
                        <option value="morning" @selected(($filters['shift'] ?? '') === 'morning')>Morning</option>
                        <option value="evening" @selected(($filters['shift'] ?? '') === 'evening')>Evening</option>
                    </select>
                </div>
            @endif
            @if(array_key_exists('from', $filters) || array_key_exists('to', $filters))
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">From</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ $filters['from'] ?? now()->startOfMonth()->toDateString() }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">To</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ $filters['to'] ?? now()->toDateString() }}">
                </div>
            @endif
            @if(array_key_exists('year', $filters))
                <div class="col-6 col-md-3">
                    <label class="form-label small mb-1">Year</label>
                    <select name="year" class="form-select form-select-sm">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" @selected(($filters['year'] ?? now()->year) == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            @endif
            <div class="col-6 col-md-2">
                <button class="btn btn-sm btn-primary w-100" type="submit">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <span class="fw-semibold">{{ $rows->count() }} rows</span>
        @can('reports.export')
        <div class="d-flex gap-2">
            @php $exportParams = array_merge($filters, ['format' => '']); @endphp
            <a href="{{ route('admin.reports.export', ['type' => $reportType] + array_merge($filters, ['format' => 'xlsx'])) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
            <a href="{{ route('admin.reports.export', ['type' => $reportType] + array_merge($filters, ['format' => 'csv'])) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-filetype-csv me-1"></i> CSV</a>
            <a href="{{ route('admin.reports.export', ['type' => $reportType] + array_merge($filters, ['format' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
        </div>
        @endcan
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    @foreach($headings as $heading)
                        <th>{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ count($headings) }}" class="text-center text-muted py-4">No data for the selected filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
