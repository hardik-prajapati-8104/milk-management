@extends('layouts.app')

@section('title', 'Activity & Audit')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Activity & Audit</h4>
    <div class="d-flex gap-2">
        @if($canExport)
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-file-earmark-excel me-1"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.audit.export', 'activity') }}">Activity Logs</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.audit.export', 'audit') }}">Audit Trail</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.audit.export', 'login') }}">Login Logs</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.audit.export', 'error') }}">Error Logs</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.audit.export', 'api') }}">API Logs</a></li>
            </ul>
        </div>
        @endif
        @if($canManage)
        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#purgeModal">
            <i class="bi bi-trash3 me-1"></i> Purge Old Logs
        </button>
        @endif
    </div>
</div>

{{-- Summary stat cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Activity Logs</div>
            <div class="fs-4 fw-semibold">{{ number_format($stats['activity_total']) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Audit Entries</div>
            <div class="fs-4 fw-semibold">{{ number_format($stats['audit_total']) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Logins Today</div>
            <div class="fs-4 fw-semibold text-success">{{ number_format($stats['logins_today']) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Failed Logins Today</div>
            <div class="fs-4 fw-semibold text-danger">{{ number_format($stats['failed_logins_today']) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Unresolved Errors</div>
            <div class="fs-4 fw-semibold text-warning">{{ number_format($stats['unresolved_errors']) }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">API Calls Today</div>
            <div class="fs-4 fw-semibold">{{ number_format($stats['api_calls_today']) }}</div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <ul class="nav nav-tabs card-header-tabs flex-nowrap overflow-auto" id="auditTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-activity" type="button">Activity Logs</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-login" type="button">Login Logs</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-error" type="button">Error Logs</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-api" type="button">API Logs</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-audit" type="button">Audit Trail</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-admin" type="button">Admin Actions</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ip" type="button">IP Tracking</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-browser" type="button">Browser Info</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-device" type="button">Device Info</button></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">

            {{-- 1. ACTIVITY LOGS --}}
            <div class="tab-pane fade show active" id="tab-activity">
                @include('admin.audit._filters', ['prefix' => 'activity', 'showAction' => true])
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="activityTable">
                        <thead><tr><th>Date/Time</th><th>User</th><th>Action</th><th>Subject</th><th>Description</th><th>IP</th><th>Device</th><th class="text-end">View</th></tr></thead>
                    </table>
                </div>
            </div>

            {{-- 2. LOGIN LOGS --}}
            <div class="tab-pane fade" id="tab-login">
                @include('admin.audit._filters', ['prefix' => 'login', 'showStatus' => ['success' => 'Success', 'failed' => 'Failed', 'locked_out' => 'Locked Out', 'logged_out' => 'Logged Out']])
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="loginTable">
                        <thead><tr><th>Date/Time</th><th>User</th><th>Status</th><th>Reason</th><th>IP</th><th>Device</th></tr></thead>
                    </table>
                </div>
            </div>

            {{-- 3. ERROR LOGS --}}
            <div class="tab-pane fade" id="tab-error">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <select class="form-select form-select-sm w-auto" id="errorLevelFilter">
                        <option value="">All Levels</option>
                        <option value="critical">Critical</option>
                        <option value="error">Error</option>
                        <option value="warning">Warning</option>
                    </select>
                    <div class="form-check align-self-center ms-2">
                        <input class="form-check-input" type="checkbox" id="errorUnresolvedOnly">
                        <label class="form-check-label small" for="errorUnresolvedOnly">Unresolved only</label>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="errorTable">
                        <thead><tr><th>Date/Time</th><th>Level</th><th>Exception</th><th>Message</th><th>User</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    </table>
                </div>
            </div>

            {{-- 4. API LOGS --}}
            <div class="tab-pane fade" id="tab-api">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <select class="form-select form-select-sm w-auto" id="apiStatusFilter">
                        <option value="">All Statuses</option>
                        <option value="success">Success (&lt; 400)</option>
                        <option value="failed">Failed (&ge; 400)</option>
                    </select>
                    <select class="form-select form-select-sm w-auto" id="apiMethodFilter">
                        <option value="">All Methods</option>
                        <option>GET</option><option>POST</option><option>PUT</option><option>PATCH</option><option>DELETE</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="apiTable">
                        <thead><tr><th>Date/Time</th><th>User</th><th>Method</th><th>Endpoint</th><th>Status</th><th>Time (ms)</th><th>IP</th><th class="text-end">View</th></tr></thead>
                    </table>
                </div>
            </div>

            {{-- 5. AUDIT TRAIL --}}
            <div class="tab-pane fade" id="tab-audit">
                @include('admin.audit._filters', ['prefix' => 'audit', 'showAction' => true])
                <p class="text-muted small">Field-level before/after values captured automatically on create, update and delete for every audited model.</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="auditTable">
                        <thead><tr><th>Date/Time</th><th>User</th><th>Action</th><th>Subject</th><th>Description</th><th>IP</th><th>Device</th><th class="text-end">Diff</th></tr></thead>
                    </table>
                </div>
            </div>

            {{-- 6. ADMIN ACTIONS --}}
            <div class="tab-pane fade" id="tab-admin">
                @include('admin.audit._filters', ['prefix' => 'admin', 'showAction' => true])
                <p class="text-muted small">Actions performed by users holding the Admin or Manager role — the highest-privilege activity in the system.</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="adminTable">
                        <thead><tr><th>Date/Time</th><th>User</th><th>Role</th><th>Action</th><th>Subject</th><th>Description</th></tr></thead>
                    </table>
                </div>
            </div>

            {{-- 7. IP TRACKING --}}
            <div class="tab-pane fade" id="tab-ip">
                <p class="text-muted small">Every IP address seen across the system, aggregated from Activity Logs. "Shared IP" flags an address used by more than 2 distinct accounts, worth a closer look.</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="ipTable">
                        <thead><tr><th>IP Address</th><th>Users Seen</th><th>Distinct Users</th><th>Failed Logins</th><th>First Seen</th><th>Last Seen</th><th>Flag</th></tr></thead>
                    </table>
                </div>
            </div>

            {{-- 8. BROWSER INFORMATION --}}
            <div class="tab-pane fade" id="tab-browser">
                <div class="row g-3">
                    <div class="col-lg-5"><canvas id="browserChart" height="260"></canvas></div>
                    <div class="col-lg-7">
                        <table class="table table-sm table-hover align-middle">
                            <thead><tr><th>Browser</th><th>Requests</th><th>Share</th></tr></thead>
                            <tbody id="browserTableBody"><tr><td colspan="3" class="text-center text-muted py-3">Loading…</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 9. DEVICE INFORMATION --}}
            <div class="tab-pane fade" id="tab-device">
                <div class="row g-3">
                    <div class="col-lg-5"><canvas id="deviceChart" height="260"></canvas></div>
                    <div class="col-lg-7">
                        <h6 class="small text-muted">By device type</h6>
                        <table class="table table-sm table-hover align-middle mb-4">
                            <thead><tr><th>Device</th><th>Requests</th><th>Share</th></tr></thead>
                            <tbody id="deviceTypeTableBody"><tr><td colspan="3" class="text-center text-muted py-3">Loading…</td></tr></tbody>
                        </table>
                        <h6 class="small text-muted">By operating system</h6>
                        <table class="table table-sm table-hover align-middle">
                            <thead><tr><th>Platform</th><th>Requests</th><th>Share</th></tr></thead>
                            <tbody id="platformTableBody"><tr><td colspan="3" class="text-center text-muted py-3">Loading…</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Log detail modal (shared by Activity Logs & Audit Trail) --}}
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Log Entry Detail</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="logDetailBody"><div class="text-center text-muted py-4">Loading…</div></div>
        </div>
    </div>
</div>

{{-- Error detail modal --}}
<div class="modal fade" id="errorDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Error Detail</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="errorDetailBody"><div class="text-center text-muted py-4">Loading…</div></div>
        </div>
    </div>
</div>

{{-- API log detail modal --}}
<div class="modal fade" id="apiDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">API Call Detail</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="apiDetailBody"><div class="text-center text-muted py-4">Loading…</div></div>
        </div>
    </div>
</div>

{{-- Purge modal --}}
@if($canManage)
<div class="modal fade" id="purgeModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('admin.audit.purge') }}" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Purge Old Logs</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="alert alert-warning small">This permanently deletes log records. It cannot be undone.</div>
                <div class="mb-3">
                    <label class="form-label">Delete records older than (days)</label>
                    <input type="number" name="days" class="form-control" min="30" max="3650" value="365" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Log types</label>
                    @foreach(['activity' => 'Activity Logs', 'audit' => 'Audit Trail', 'login' => 'Login Logs', 'error' => 'Error Logs (resolved only)', 'api' => 'API Logs'] as $val => $label)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="types[]" value="{{ $val }}" id="purge-{{ $val }}">
                        <label class="form-check-label" for="purge-{{ $val }}">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Purge</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
$(function () {
    const fmtDate = (v) => v ? new Date(v).toLocaleString() : '—';

    function buildFilters(prefix) {
        return function (d) {
            d.user_id = $('#' + prefix + 'UserFilter').val();
            d.action = $('#' + prefix + 'ActionFilter').val();
            d.date_from = $('#' + prefix + 'DateFrom').val();
            d.date_to = $('#' + prefix + 'DateTo').val();
            d.status = $('#' + prefix + 'StatusFilter').val();
        };
    }

    // 1. Activity Logs
    const activityTable = $('#activityTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: '{{ route('admin.audit.data') }}', data: buildFilters('activity') },
        columns: [
            { data: 'created_at', name: 'created_at', render: fmtDate },
            { data: 'user_name', name: 'user.name', orderable: false, searchable: false },
            { data: 'action_badge', name: 'action', orderable: false },
            { data: 'subject', name: 'subject_type', orderable: false, searchable: false },
            { data: 'description', name: 'description' },
            { data: 'ip_address', name: 'ip_address' },
            { data: 'device', name: 'browser', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'desc']], pageLength: 25,
    });
    $('#activityUserFilter, #activityActionFilter, #activityDateFrom, #activityDateTo').on('change', () => activityTable.ajax.reload());

    // 5. Audit Trail (shares the same columns, different endpoint)
    const auditTable = $('#auditTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: '{{ route('admin.audit.audit-trail-data') }}', data: buildFilters('audit') },
        columns: [
            { data: 'created_at', name: 'created_at', render: fmtDate },
            { data: 'user_name', name: 'user.name', orderable: false, searchable: false },
            { data: 'action_badge', name: 'action', orderable: false },
            { data: 'subject', name: 'subject_type', orderable: false, searchable: false },
            { data: 'description', name: 'description' },
            { data: 'ip_address', name: 'ip_address' },
            { data: 'device', name: 'browser', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'desc']], pageLength: 25,
    });
    $('#auditUserFilter, #auditActionFilter, #auditDateFrom, #auditDateTo').on('change', () => auditTable.ajax.reload());

    // 2. Login Logs
    const loginTable = $('#loginTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: '{{ route('admin.audit.login-data') }}', data: buildFilters('login') },
        columns: [
            { data: 'created_at', name: 'created_at', render: fmtDate },
            { data: 'user_name', name: 'user.name', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'reason', name: 'reason' },
            { data: 'ip_address', name: 'ip_address' },
            { data: 'device', name: 'browser', orderable: false, searchable: false },
        ],
        order: [[0, 'desc']], pageLength: 25,
    });
    $('#loginUserFilter, #loginStatusFilter, #loginDateFrom, #loginDateTo').on('change', () => loginTable.ajax.reload());

    // 3. Error Logs
    const errorTable = $('#errorTable').DataTable({
        processing: true, serverSide: true,
        ajax: {
            url: '{{ route('admin.audit.error-data') }}',
            data: function (d) {
                d.level = $('#errorLevelFilter').val();
                d.unresolved_only = $('#errorUnresolvedOnly').is(':checked') ? '1' : '';
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at', render: fmtDate },
            { data: 'level_badge', name: 'level', orderable: false },
            { data: 'exception_class', name: 'exception_class' },
            { data: 'message', name: 'message' },
            { data: 'user_name', name: 'user.name', orderable: false, searchable: false },
            { data: 'resolved_badge', name: 'is_resolved', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'desc']], pageLength: 25,
    });
    $('#errorLevelFilter, #errorUnresolvedOnly').on('change', () => errorTable.ajax.reload());

    // 4. API Logs
    const apiTable = $('#apiTable').DataTable({
        processing: true, serverSide: true,
        ajax: {
            url: '{{ route('admin.audit.api-data') }}',
            data: function (d) {
                d.status = $('#apiStatusFilter').val();
                d.method = $('#apiMethodFilter').val();
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at', render: fmtDate },
            { data: 'user_name', name: 'user.name', orderable: false, searchable: false },
            { data: 'method', name: 'method' },
            { data: 'endpoint', name: 'endpoint' },
            { data: 'status_badge', name: 'response_status', orderable: false },
            { data: 'response_time_ms', name: 'response_time_ms' },
            { data: 'ip_address', name: 'ip_address' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
        order: [[0, 'desc']], pageLength: 25,
    });
    $('#apiStatusFilter, #apiMethodFilter').on('change', () => apiTable.ajax.reload());

    // 6. Admin Actions
    const adminTable = $('#adminTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: '{{ route('admin.audit.admin-actions-data') }}', data: buildFilters('admin') },
        columns: [
            { data: 'created_at', name: 'created_at', render: fmtDate },
            { data: 'user_name', name: 'user.name', orderable: false, searchable: false },
            { data: 'role', name: 'role', orderable: false, searchable: false },
            { data: 'action_badge', name: 'action', orderable: false },
            { data: 'subject', name: 'subject_type', orderable: false, searchable: false },
            { data: 'description', name: 'description' },
        ],
        order: [[0, 'desc']], pageLength: 25,
    });
    $('#adminUserFilter, #adminActionFilter, #adminDateFrom, #adminDateTo').on('change', () => adminTable.ajax.reload());

    // 7. IP Tracking
    const ipTable = $('#ipTable').DataTable({
        processing: true, serverSide: true,
        ajax: { url: '{{ route('admin.audit.ip-data') }}' },
        columns: [
            { data: 'ip_address', name: 'ip_address' },
            { data: 'users_list', name: 'users_list', orderable: false, searchable: false },
            { data: 'distinct_users', name: 'distinct_users' },
            { data: 'failed_logins', name: 'failed_logins', orderable: false, searchable: false },
            { data: 'first_seen', name: 'first_seen', render: fmtDate },
            { data: 'last_seen', name: 'last_seen', render: fmtDate },
            { data: 'flag', name: 'flag', orderable: false, searchable: false },
        ],
        order: [[5, 'desc']], pageLength: 25,
    });

    // Lazily init tables that live inside inactive tabs (Bootstrap tabs keep them display:none,
    // and DataTables miscalculates column widths on hidden elements) + load chart tabs once.
    let browserLoaded = false, deviceLoaded = false;
    document.querySelectorAll('#auditTabs button').forEach((btn) => {
        btn.addEventListener('shown.bs.tab', function (e) {
            const target = e.target.getAttribute('data-bs-target');
            if (['#tab-login', '#tab-error', '#tab-api', '#tab-audit', '#tab-admin', '#tab-ip'].includes(target)) {
                $($.fn.dataTable.tables({ visible: true, api: true })).columns.adjust();
            }
            if (target === '#tab-browser' && !browserLoaded) { browserLoaded = true; loadBrowserStats(); }
            if (target === '#tab-device' && !deviceLoaded) { deviceLoaded = true; loadDeviceStats(); }
        });
    });

    // 8. Browser Information
    function loadBrowserStats() {
        $.get('{{ route('admin.audit.browser-stats') }}', function (res) {
            const rows = res.data || [];
            const total = rows.reduce((s, r) => s + r.total, 0) || 1;
            $('#browserTableBody').html(rows.map(r => `
                <tr><td>${r.browser}</td><td>${r.total.toLocaleString()}</td><td>${(r.total / total * 100).toFixed(1)}%</td></tr>
            `).join('') || '<tr><td colspan="3" class="text-center text-muted py-3">No data yet.</td></tr>');

            new Chart(document.getElementById('browserChart'), {
                type: 'doughnut',
                data: {
                    labels: rows.map(r => r.browser),
                    datasets: [{ data: rows.map(r => r.total), backgroundColor: ['#2e7d32','#66bb6a','#1565c0','#f9a825','#c62828','#6a1b9a','#00838f','#ef6c00','#455a64','#8d6e63'] }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });
        });
    }

    // 9. Device Information
    function loadDeviceStats() {
        $.get('{{ route('admin.audit.device-stats') }}', function (res) {
            const byDevice = res.by_device || [];
            const byPlatform = res.by_platform || [];
            const deviceTotal = byDevice.reduce((s, r) => s + r.total, 0) || 1;
            const platformTotal = byPlatform.reduce((s, r) => s + r.total, 0) || 1;

            $('#deviceTypeTableBody').html(byDevice.map(r => `
                <tr><td class="text-capitalize">${r.device_type}</td><td>${r.total.toLocaleString()}</td><td>${(r.total / deviceTotal * 100).toFixed(1)}%</td></tr>
            `).join('') || '<tr><td colspan="3" class="text-center text-muted py-3">No data yet.</td></tr>');

            $('#platformTableBody').html(byPlatform.map(r => `
                <tr><td>${r.platform}</td><td>${r.total.toLocaleString()}</td><td>${(r.total / platformTotal * 100).toFixed(1)}%</td></tr>
            `).join('') || '<tr><td colspan="3" class="text-center text-muted py-3">No data yet.</td></tr>');

            new Chart(document.getElementById('deviceChart'), {
                type: 'pie',
                data: {
                    labels: byDevice.map(r => r.device_type),
                    datasets: [{ data: byDevice.map(r => r.total), backgroundColor: ['#2e7d32','#1565c0','#f9a825','#c62828'] }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });
        });
    }

    // Log / Audit detail modal
    $(document).on('click', '.js-view-log', function () {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal('#logDetailModal');
        $('#logDetailBody').html('<div class="text-center text-muted py-4">Loading…</div>');
        modal.show();

        $.get('{{ url('admin/audit/entry') }}/' + id, function (log) {
            const oldVals = JSON.stringify(log.old_values || {}, null, 2);
            const newVals = JSON.stringify(log.new_values || {}, null, 2);
            $('#logDetailBody').html(`
                <dl class="row small mb-0">
                    <dt class="col-3">Date/Time</dt><dd class="col-9">${fmtDate(log.created_at)}</dd>
                    <dt class="col-3">User</dt><dd class="col-9">${log.user ? log.user.name + ' (' + log.user.email + ')' : 'System'}</dd>
                    <dt class="col-3">Action</dt><dd class="col-9">${log.action}</dd>
                    <dt class="col-3">Subject</dt><dd class="col-9">${log.subject_type ? log.subject_type.split('\\\\').pop() + ' #' + log.subject_id : '—'}</dd>
                    <dt class="col-3">Description</dt><dd class="col-9">${log.description ?? '—'}</dd>
                    <dt class="col-3">IP Address</dt><dd class="col-9">${log.ip_address ?? '—'}</dd>
                    <dt class="col-3">Browser</dt><dd class="col-9">${log.browser ?? '—'} ${log.browser_version ?? ''}</dd>
                    <dt class="col-3">Platform</dt><dd class="col-9">${log.platform ?? '—'}</dd>
                    <dt class="col-3">Device Type</dt><dd class="col-9 text-capitalize">${log.device_type ?? '—'}</dd>
                    <dt class="col-3">URL</dt><dd class="col-9 text-break">${log.url ?? '—'}</dd>
                </dl>
                <hr>
                <div class="row">
                    <div class="col-6"><h6 class="small text-muted">Old Values</h6><pre class="bg-body-tertiary p-2 small rounded" style="max-height:260px;overflow:auto">${oldVals}</pre></div>
                    <div class="col-6"><h6 class="small text-muted">New Values</h6><pre class="bg-body-tertiary p-2 small rounded" style="max-height:260px;overflow:auto">${newVals}</pre></div>
                </div>
            `);
        });
    });

    // Error detail modal
    $(document).on('click', '.js-view-error', function () {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal('#errorDetailModal');
        $('#errorDetailBody').html('<div class="text-center text-muted py-4">Loading…</div>');
        modal.show();

        $.get('{{ url('admin/audit/error-entry') }}/' + id, function (err) {
            $('#errorDetailBody').html(`
                <dl class="row small mb-0">
                    <dt class="col-3">Date/Time</dt><dd class="col-9">${fmtDate(err.created_at)}</dd>
                    <dt class="col-3">Level</dt><dd class="col-9 text-capitalize">${err.level}</dd>
                    <dt class="col-3">Exception</dt><dd class="col-9">${err.exception_class}</dd>
                    <dt class="col-3">Message</dt><dd class="col-9">${err.message ?? '—'}</dd>
                    <dt class="col-3">Location</dt><dd class="col-9">${err.file ?? '—'}:${err.line ?? '—'}</dd>
                    <dt class="col-3">URL</dt><dd class="col-9 text-break">${err.method ?? ''} ${err.url ?? '—'}</dd>
                    <dt class="col-3">User</dt><dd class="col-9">${err.user ? err.user.name : 'Guest'}</dd>
                    <dt class="col-3">IP Address</dt><dd class="col-9">${err.ip_address ?? '—'}</dd>
                </dl>
                <hr>
                <h6 class="small text-muted">Stack Trace</h6>
                <pre class="bg-body-tertiary p-2 small rounded" style="max-height:320px;overflow:auto">${(err.trace ?? '—')}</pre>
            `);
        });
    });

    // API log detail modal
    $(document).on('click', '.js-view-api', function () {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal('#apiDetailModal');
        $('#apiDetailBody').html('<div class="text-center text-muted py-4">Loading…</div>');
        modal.show();

        $.get('{{ url('admin/audit/api-entry') }}/' + id, function (log) {
            const payload = JSON.stringify(log.request_payload || {}, null, 2);
            $('#apiDetailBody').html(`
                <dl class="row small mb-0">
                    <dt class="col-3">Date/Time</dt><dd class="col-9">${fmtDate(log.created_at)}</dd>
                    <dt class="col-3">User</dt><dd class="col-9">${log.user ? log.user.name : 'Guest'}</dd>
                    <dt class="col-3">Method</dt><dd class="col-9">${log.method}</dd>
                    <dt class="col-3">Endpoint</dt><dd class="col-9 text-break">${log.endpoint}</dd>
                    <dt class="col-3">Status</dt><dd class="col-9">${log.response_status}</dd>
                    <dt class="col-3">Response Time</dt><dd class="col-9">${log.response_time_ms} ms</dd>
                    <dt class="col-3">IP Address</dt><dd class="col-9">${log.ip_address ?? '—'}</dd>
                </dl>
                <hr>
                <h6 class="small text-muted">Request Payload (sensitive fields redacted)</h6>
                <pre class="bg-body-tertiary p-2 small rounded" style="max-height:280px;overflow:auto">${payload}</pre>
            `);
        });
    });
});
</script>
@endpush
