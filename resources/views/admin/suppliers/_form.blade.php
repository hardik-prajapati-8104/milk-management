@csrf
@isset($supplier)
    @method('PUT')
@endisset

<div class="card shadow-sm" style="max-width:560px">
    <div class="card-body row g-3">
        <div class="col-12">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name ?? '') }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">Mobile</label>
            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $supplier->mobile ?? '') }}">
        </div>
        <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <div class="form-check form-switch">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $supplier->is_active ?? true))>
                <label class="form-check-label">Active</label>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3" style="max-width:560px">
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-lg me-1"></i> {{ isset($supplier) ? 'Update Supplier' : 'Create Supplier' }}
    </button>
</div>
