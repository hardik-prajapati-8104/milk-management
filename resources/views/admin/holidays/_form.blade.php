@csrf
@isset($holiday)
    @method('PUT')
@endisset

<div class="card shadow-sm" style="max-width:640px">
    <div class="card-body row g-3">
        <div class="col-md-8">
            <label class="form-label">Holiday Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $holiday->name ?? '') }}" required maxlength="191">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Date <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                   value="{{ old('date', isset($holiday) ? $holiday->date->format('Y-m-d') : '') }}" required>
            @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Type <span class="text-danger">*</span></label>
            <select name="type" class="form-select" required>
                @foreach(['national' => 'National', 'regional' => 'Regional', 'company' => 'Company', 'optional' => 'Optional'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('type', $holiday->type ?? 'company') == $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Color</label>
            <input type="color" name="color" class="form-control form-control-color w-100"
                   value="{{ old('color', $holiday->color ?? '#dc3545') }}">
        </div>

        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_recurring_yearly" id="isRecurringYearly" value="1"
                       @checked(old('is_recurring_yearly', $holiday->is_recurring_yearly ?? true))>
                <label class="form-check-label" for="isRecurringYearly">
                    Recurs every year on this month/day (uncheck for a one-time date)
                </label>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2" maxlength="1000">{{ old('description', $holiday->description ?? '') }}</textarea>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3" style="max-width:640px">
    <a href="{{ route('admin.holidays.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-lg me-1"></i> {{ isset($holiday) ? 'Update Holiday' : 'Save Holiday' }}
    </button>
</div>
