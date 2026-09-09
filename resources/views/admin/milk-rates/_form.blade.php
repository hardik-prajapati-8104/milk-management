@csrf
@isset($rate)
    @method('PUT')
@endisset

<div class="card shadow-sm" style="max-width:640px">
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Effective Date <span class="text-danger">*</span></label>
            <input type="date" name="effective_date" class="form-control @error('effective_date') is-invalid @enderror"
                   value="{{ old('effective_date', isset($rate) ? $rate->effective_date->format('Y-m-d') : ($defaultDate ?? now()->toDateString())) }}" required>
            @error('effective_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">This rate card applies to all daily entries and bills from this date onward, until a newer one takes effect.</div>
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check form-switch">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive"
                       @checked(old('is_active', $rate->is_active ?? true))>
                <label class="form-check-label" for="isActive">Active</label>
            </div>
        </div>

        <div class="col-12"><hr class="my-1"></div>

        <div class="col-md-6">
            <label class="form-label">Cow — Morning Rate (₹/L) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="cow_morning_rate" class="form-control"
                   value="{{ old('cow_morning_rate', $rate->cow_morning_rate ?? 0) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Cow — Evening Rate (₹/L) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="cow_evening_rate" class="form-control"
                   value="{{ old('cow_evening_rate', $rate->cow_evening_rate ?? 0) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Buffalo — Morning Rate (₹/L) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="buffalo_morning_rate" class="form-control"
                   value="{{ old('buffalo_morning_rate', $rate->buffalo_morning_rate ?? 0) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Buffalo — Evening Rate (₹/L) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="buffalo_evening_rate" class="form-control"
                   value="{{ old('buffalo_evening_rate', $rate->buffalo_evening_rate ?? 0) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Mixed Rate (₹/L) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="mixed_rate" class="form-control"
                   value="{{ old('mixed_rate', $rate->mixed_rate ?? 0) }}" required>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3" style="max-width:640px">
    <a href="{{ route('admin.milk-rates.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-lg me-1"></i> {{ isset($rate) ? 'Update Rate Card' : 'Create Rate Card' }}
    </button>
</div>
