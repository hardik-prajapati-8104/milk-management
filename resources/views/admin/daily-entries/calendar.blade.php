@extends('layouts.app')

@section('title', 'Entry Calendar')

@section('content')
@php
    $prevMonth = $month === 1 ? 12 : $month - 1;
    $prevYear = $month === 1 ? $year - 1 : $year;
    $nextMonth = $month === 12 ? 1 : $month + 1;
    $nextYear = $month === 12 ? $year + 1 : $year;
    $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
    $startOffset = $firstDay->dayOfWeek; // 0 = Sunday
    $daysInMonth = $firstDay->daysInMonth;
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Entry Completion Calendar — {{ $firstDay->format('F Y') }}</h4>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.daily-entries.calendar', ['year' => $prevYear, 'month' => $prevMonth]) }}">
            <i class="bi bi-chevron-left"></i> Prev
        </a>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.daily-entries.calendar', ['year' => $nextYear, 'month' => $nextMonth]) }}">
            Next <i class="bi bi-chevron-right"></i>
        </a>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.daily-entries.index') }}">
            <i class="bi bi-journal-check me-1"></i> Entry Grid
        </a>
    </div>
</div>

<div class="d-flex gap-3 mb-3 small">
    <span><span class="badge bg-success">&nbsp;</span> Completed (all customers, both shifts)</span>
    <span><span class="badge bg-warning">&nbsp;</span> Partial</span>
    <span><span class="badge bg-danger">&nbsp;</span> Missing</span>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row row-cols-7 g-2 text-center small fw-semibold mb-1">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                <div class="col text-muted">{{ $d }}</div>
            @endforeach
        </div>
        <div class="row row-cols-7 g-2">
            @for($i = 0; $i < $startOffset; $i++)
                <div class="col"></div>
            @endfor
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $dayStatus = $status[$dateStr] ?? 'red';
                    $colorClass = ['green' => 'success', 'yellow' => 'warning', 'red' => 'danger'][$dayStatus];
                @endphp
                <div class="col">
                    <a href="{{ route('admin.daily-entries.index', ['date' => $dateStr]) }}"
                       class="d-block rounded p-2 text-decoration-none border border-{{ $colorClass }} bg-{{ $colorClass }}-subtle text-body">
                        <div class="fw-semibold">{{ $day }}</div>
                    </a>
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection
