@extends('layouts.app')

@section('title', 'Generate Bills')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Generate Monthly Bills</h4>
    <p class="text-muted small">Pulls all unbilled daily entries within the selected period for each customer and creates one bill per customer. Customers already billed for this period, or with no unbilled entries, are skipped automatically.</p>
</div>

<form action="{{ route('admin.bills.generate') }}" method="POST">
    @csrf
    <div class="card shadow-sm mb-3" style="max-width:720px">
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Month <span class="text-danger">*</span></label>
                <select name="month" class="form-select" required>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected($m == now()->month)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Year <span class="text-danger">*</span></label>
                <select name="year" class="form-select" required>
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" @selected($y == now()->year)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">GST % (optional)</label>
                <input type="number" step="0.01" min="0" max="28" name="gst_percent" class="form-control" value="0">
            </div>
            <div class="col-md-6">
                <label class="form-label">Delivery Charges per Bill (optional)</label>
                <input type="number" step="0.01" min="0" name="delivery_charges" class="form-control" value="0">
            </div>
            <div class="col-md-6">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-control" value="{{ now()->addDays(7)->toDateString() }}">
            </div>
        </div>
    </div>

    <div class="card shadow-sm" style="max-width:720px">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Customers</span>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="selectAll" checked>
                <label class="form-check-label small" for="selectAll">All active customers</label>
            </div>
        </div>
        <div class="card-body" style="max-height:320px; overflow-y:auto" id="customerListWrap">
            @foreach($customers as $customer)
                <div class="form-check">
                    <input type="checkbox" class="form-check-input js-customer" name="customer_ids[]" value="{{ $customer->id }}" checked disabled>
                    <label class="form-check-label small">{{ $customer->consumer_id }} — {{ $customer->name }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3" style="max-width:720px">
        <a href="{{ route('admin.bills.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Generate Bills</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // "All active customers" toggles individual checkboxes on/off; unchecking it
    // lets the operator hand-pick a subset (e.g. re-run billing for stragglers only).
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.js-customer').forEach(cb => {
            cb.checked = this.checked;
            cb.disabled = this.checked;
        });
    });
</script>
@endpush
