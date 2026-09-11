@csrf
@isset($birthday)
    @method('PUT')
@endisset

<div class="card shadow-sm" style="max-width:640px">
    <div class="card-body row g-3">
        <div class="col-md-8">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $birthday->name ?? '') }}" required maxlength="191">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                   value="{{ old('date_of_birth', isset($birthday) ? $birthday->date_of_birth->format('Y-m-d') : '') }}" required>
            @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="category" class="form-select" required>
                @foreach(['employee' => 'Employee', 'customer' => 'Customer', 'supplier' => 'Supplier', 'other' => 'Other'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('category', $birthday->category ?? 'other') == $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-text">
                Staff/customers with a Date of Birth already on their profile appear on the calendar
                automatically — use this only for people not already tracked in the system.
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Mobile</label>
            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $birthday->mobile ?? '') }}" maxlength="20">
        </div>

        <div class="col-md-6">
            <label class="form-label">Remind (days before)</label>
            <input type="number" min="0" max="30" name="reminder_days_before" class="form-control"
                   value="{{ old('reminder_days_before', $birthday->reminder_days_before ?? 1) }}">
        </div>
        <div class="col-md-6">
            <div class="form-check mt-4 pt-1">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                       @checked(old('is_active', $birthday->is_active ?? true))>
                <label class="form-check-label" for="isActive">Active</label>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="2" maxlength="500">{{ old('notes', $birthday->notes ?? '') }}</textarea>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3" style="max-width:640px">
    <a href="{{ route('admin.birthdays.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-lg me-1"></i> {{ isset($birthday) ? 'Update Birthday' : 'Save Birthday' }}
    </button>
</div>
