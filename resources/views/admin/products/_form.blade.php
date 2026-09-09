@csrf
@isset($product)
    @method('PUT')
@endisset

<div class="card shadow-sm" style="max-width:640px">
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">SKU <span class="text-danger">*</span></label>
            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku ?? '') }}" required>
            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Unit <span class="text-danger">*</span></label>
            <select name="unit" class="form-select" required>
                @foreach(['litre' => 'Litre', 'kg' => 'Kg', 'piece' => 'Piece'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('unit', $product->unit ?? 'litre') == $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price ?? 0) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Selling Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price ?? 0) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Low Stock Threshold</label>
            <input type="number" step="0.001" min="0" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 0) }}">
            <div class="form-text">You'll get a low-stock activity log entry once stock falls to or below this.</div>
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check form-switch">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $product->is_active ?? true))>
                <label class="form-check-label">Active</label>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3" style="max-width:640px">
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-lg me-1"></i> {{ isset($product) ? 'Update Product' : 'Create Product' }}
    </button>
</div>
