@extends('layouts.app')

@section('title', 'Daily Entries')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="mb-0">Daily Milk Entry</h4>
        <div class="text-muted small">Available Stock: <strong class="text-success">{{ number_format($currentStock, 2) }} L</strong></div>
    </div>
    <a href="{{ route('admin.daily-entries.calendar') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-calendar3 me-1"></i> Calendar View
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">Date</label>
                <input type="date" id="entryDate" class="form-control form-control-sm" value="{{ $date }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">Route</label>
                <select id="routeFilter" class="form-select form-select-sm">
                    <option value="">All Routes</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" @selected($routeId == $route->id)>{{ $route->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small mb-1">Search Customer (ID / Mobile / Name / Barcode)</label>
                <input type="search" id="customerSearch" class="form-control form-control-sm" placeholder="Type to jump to a customer...">
            </div>
            <div class="col-12 col-md-2 text-md-end">
                <button class="btn btn-sm btn-outline-secondary w-100" id="copyPreviousBtn">
                    <i class="bi bi-clipboard-plus me-1"></i> Copy Yesterday
                </button>
            </div>
        </div>

        <ul class="nav nav-tabs mt-3" id="shiftTabs">
            <li class="nav-item">
                <button class="nav-link {{ $shift === 'morning' ? 'active' : '' }}" data-shift="morning">
                    <i class="bi bi-sunrise me-1"></i> Morning
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $shift === 'evening' ? 'active' : '' }}" data-shift="evening">
                    <i class="bi bi-sunset me-1"></i> Evening
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="entryGrid">
            <thead class="table-light">
                <tr>
                    <th style="width:40px">#</th>
                    <th>Consumer ID</th>
                    <th>Customer</th>
                    <th style="width:120px">Qty (L)</th>
                    <th style="width:100px">Rate</th>
                    <th style="width:110px">Amount</th>
                    <th style="width:80px" class="text-center">Absent</th>
                    <th style="width:80px" class="text-center">Holiday</th>
                    <th>Remarks</th>
                    <th style="width:60px" class="text-center">Locked</th>
                </tr>
            </thead>
            <tbody id="entryGridBody">
                @foreach($customers as $i => $customer)
                    @php $existing = $existingEntries->get($customer->id); @endphp
                    <tr data-customer-id="{{ $customer->id }}" data-name="{{ $customer->name }} {{ $customer->consumer_id }} {{ $customer->mobile }} {{ $customer->barcode }}"
                        class="{{ $existing?->is_locked ? 'table-secondary' : '' }}">
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>{{ $customer->consumer_id }}</td>
                        <td>{{ $customer->name }}<div class="text-muted small">{{ $customer->mobile }}</div></td>
                        <td>
                            <input type="number" step="0.001" min="0" class="form-control form-control-sm js-qty"
                                   value="{{ $existing?->quantity ?? $customer->defaultQtyForShift($shift) }}"
                                   {{ $existing?->is_locked && ! auth()->user()->hasRole('Admin') ? 'disabled' : '' }}>
                        </td>
                        <td class="js-rate text-muted small">
                            {{ money($existing?->rate ?? $customer->rateForShift($shift)) }}
                        </td>
                        <td class="js-amount fw-semibold">
                            {{ money($existing?->amount ?? 0) }}
                        </td>
                        <td class="text-center"><input type="checkbox" class="form-check-input js-absent" {{ $existing?->is_absent ? 'checked' : '' }}></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input js-holiday" {{ $existing?->is_holiday ? 'checked' : '' }}></td>
                        <td><input type="text" class="form-control form-control-sm js-remarks" value="{{ $existing?->remarks }}" maxlength="255"></td>
                        <td class="text-center">
                            @if($existing?->is_locked)
                                <i class="bi bi-lock-fill text-danger"></i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center bg-transparent">
        <div class="text-muted small">
            <span id="gridSummary">{{ $customers->count() }} customers</span>
        </div>
        <button class="btn btn-success" id="saveGridBtn">
            <i class="bi bi-save me-1"></i> Save All Entries
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    let currentShift = @json($shift);

    const dateInput = document.getElementById('entryDate');
    const routeFilter = document.getElementById('routeFilter');
    const searchInput = document.getElementById('customerSearch');
    const gridBody = document.getElementById('entryGridBody');

    function reloadGrid() {
        const params = new URLSearchParams({
            date: dateInput.value,
            shift: currentShift,
            route_id: routeFilter.value || '',
        });
        window.location = '{{ route('admin.daily-entries.index') }}?' + params.toString();
    }

    dateInput.addEventListener('change', reloadGrid);
    routeFilter.addEventListener('change', reloadGrid);

    document.querySelectorAll('#shiftTabs .nav-link').forEach(tab => {
        tab.addEventListener('click', () => {
            currentShift = tab.dataset.shift;
            reloadGrid();
        });
    });

    // Live filter (client-side, since the route already narrows the page server-side)
    searchInput.addEventListener('input', function () {
        const term = this.value.toLowerCase();
        gridBody.querySelectorAll('tr').forEach(row => {
            row.style.display = row.dataset.name.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    // Recalculate amount live as qty/absent/holiday change
    function recalcRow(row) {
        const qty = parseFloat(row.querySelector('.js-qty').value) || 0;
        const isAbsent = row.querySelector('.js-absent').checked;
        const isHoliday = row.querySelector('.js-holiday').checked;
        const rateText = row.querySelector('.js-rate').textContent.replace(/[^0-9.]/g, '');
        const rate = parseFloat(rateText) || 0;
        const amount = (isAbsent || isHoliday) ? 0 : qty * rate;
        row.querySelector('.js-amount').textContent = '₹' + amount.toFixed(2);
        row.querySelector('.js-qty').disabled = isAbsent || isHoliday;
    }

    gridBody.addEventListener('input', (e) => {
        if (e.target.matches('.js-qty')) recalcRow(e.target.closest('tr'));
    });
    gridBody.addEventListener('change', (e) => {
        if (e.target.matches('.js-absent, .js-holiday')) recalcRow(e.target.closest('tr'));
    });

    // Keyboard navigation: Enter / Arrow keys move between qty fields
    gridBody.addEventListener('keydown', (e) => {
        if (!e.target.matches('.js-qty')) return;
        const rows = Array.from(gridBody.querySelectorAll('tr')).filter(r => r.style.display !== 'none');
        const currentRow = e.target.closest('tr');
        const idx = rows.indexOf(currentRow);

        if (e.key === 'Enter' || e.key === 'ArrowDown') {
            e.preventDefault();
            rows[idx + 1]?.querySelector('.js-qty')?.focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            rows[idx - 1]?.querySelector('.js-qty')?.focus();
        }
    });

    // Bulk save
    document.getElementById('saveGridBtn').addEventListener('click', function () {
        const btn = this;
        const entries = Array.from(gridBody.querySelectorAll('tr')).map(row => ({
            customer_id: row.dataset.customerId,
            quantity: row.querySelector('.js-qty').value,
            is_absent: row.querySelector('.js-absent').checked,
            is_holiday: row.querySelector('.js-holiday').checked,
            remarks: row.querySelector('.js-remarks').value,
        }));

        btn.disabled = true;
        $.post('{{ route('admin.daily-entries.bulk-save') }}', {
            entry_date: dateInput.value,
            shift: currentShift,
            entries: entries,
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: 'Saved', text: res.message, timer: 2000, showConfirmButton: false });
            if (res.blocked && res.blocked.length) {
                Swal.fire({ icon: 'warning', title: 'Some rows were locked', text: res.blocked.join(', ') + ' were skipped (locked entries).' });
            }
        }).fail(function (xhr) {
            Swal.fire({ icon: 'error', title: 'Save failed', text: xhr.responseJSON?.message || 'Please check the form and try again.' });
        }).always(() => { btn.disabled = false; });
    });

    // Copy previous day
    document.getElementById('copyPreviousBtn').addEventListener('click', function () {
        $.post('{{ route('admin.daily-entries.copy-previous') }}', {
            entry_date: dateInput.value,
            shift: currentShift,
        }).done(function (res) {
            Swal.fire({ icon: 'success', title: 'Copied', text: res.message }).then(() => reloadGrid());
        });
    });

    // Enter in the search box jumps focus to the first visible qty field
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstVisible = Array.from(gridBody.querySelectorAll('tr')).find(r => r.style.display !== 'none');
            firstVisible?.querySelector('.js-qty')?.focus();
        }
    });
})();
</script>
@endpush
