@extends('layouts.app')

@section('title', 'Timeline — ' . $timelineUser->name)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">
        <i class="bi bi-clock-history me-1"></i> {{ $timelineUser->name }}
        <span class="text-muted fs-6">({{ $timelineUser->email }})</span>
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Activity & Audit
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center">
                @if($timelineUser->photo)
                    <img src="{{ Storage::url($timelineUser->photo) }}" class="rounded-circle mb-3" style="width:90px;height:90px;object-fit:cover">
                @else
                    <i class="bi bi-person-circle text-muted mb-3 d-block" style="font-size:4.5rem"></i>
                @endif
                <h6 class="mb-1">{{ $timelineUser->name }}</h6>
                <div class="text-muted small mb-2">{{ $timelineUser->getRoleNames()->join(', ') ?: 'No role assigned' }}</div>
                <span class="badge text-bg-{{ ['active' => 'success', 'inactive' => 'secondary', 'suspended' => 'danger'][$timelineUser->status] ?? 'secondary' }}">
                    {{ ucfirst($timelineUser->status) }}
                </span>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Session Info</div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Last Login</span>
                    <span>{{ $timelineUser->last_login_at?->diffForHumans() ?? 'Never' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Last Login IP</span>
                    <span>{{ $timelineUser->last_login_ip ?? '—' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Employee Code</span>
                    <span>{{ $timelineUser->employee_code ?? '—' }}</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Activity Timeline</span>
                <span class="text-muted small">Most recent 300 events, across all activity, audit and login records</span>
            </div>
            <div class="card-body">
                <div id="timelineLoading" class="text-center text-muted py-4">Loading timeline…</div>
                <div id="timelineEmpty" class="text-center text-muted py-4 d-none">No activity recorded yet for this user.</div>
                <div class="timeline" id="timelineList" style="position:relative; padding-left:1.75rem;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline-item { position: relative; padding-bottom: 1.25rem; border-left: 2px solid var(--bs-border-color); padding-left: 1rem; margin-left: -1px; }
    .timeline-item:last-child { border-left-color: transparent; padding-bottom: 0; }
    .timeline-item::before {
        content: ''; position: absolute; left: -7px; top: 2px;
        width: 12px; height: 12px; border-radius: 50%; background: var(--brand); border: 2px solid #fff;
    }
</style>
@endpush

@push('scripts')
<script>
$(function () {
    const fmtDate = (v) => v ? new Date(v).toLocaleString() : '—';

    const sourceBadge = {
        'Activity': 'primary',
        'Audit Trail': 'dark',
        'Login': 'info',
    };

    $.get('{{ route('admin.audit.timeline-data') }}', { user_id: {{ $timelineUser->id }} }, function (res) {
        const rows = res.data || [];
        $('#timelineLoading').addClass('d-none');

        if (! rows.length) {
            $('#timelineEmpty').removeClass('d-none');
            return;
        }

        const html = rows.map(r => `
            <div class="timeline-item">
                <div class="d-flex justify-content-between flex-wrap">
                    <div>
                        <span class="badge text-bg-${sourceBadge[r.source] || 'secondary'} me-1">${r.source}</span>
                        <strong>${r.title}</strong>
                    </div>
                    <span class="text-muted small">${fmtDate(r.created_at)}</span>
                </div>
                ${r.description ? `<div class="small text-muted mt-1">${r.description}</div>` : ''}
                <div class="small text-muted mt-1">
                    <i class="bi bi-geo-alt"></i> ${r.ip_address ?? '—'}
                    &nbsp;·&nbsp; <i class="bi bi-display"></i> ${r.device ?? '—'}
                </div>
            </div>
        `).join('');

        $('#timelineList').html(html);
    });
});
</script>
@endpush
