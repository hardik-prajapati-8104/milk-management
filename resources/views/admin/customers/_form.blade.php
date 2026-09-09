@csrf
@isset($customer)
    @method('PUT')
@endisset

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Basic Details</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $customer->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                           value="{{ old('mobile', $customer->mobile ?? '') }}" required>
                    @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Alternative Mobile</label>
                    <input type="text" name="alternative_mobile" class="form-control"
                           value="{{ old('alternative_mobile', $customer->alternative_mobile ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $customer->email ?? '') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $customer->address ?? '') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Area</label>
                    <select name="area_id" class="form-select select2">
                        <option value="">Select Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" @selected(old('area_id', $customer->area_id ?? '') == $area->id)>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Village</label>
                    <select name="village_id" class="form-select select2">
                        <option value="">Select Village</option>
                        @foreach($villages as $village)
                            <option value="{{ $village->id }}" @selected(old('village_id', $customer->village_id ?? '') == $village->id)>{{ $village->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Route</label>
                    <select name="route_id" class="form-select select2">
                        <option value="">Select Route</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" @selected(old('route_id', $customer->route_id ?? '') == $route->id)>{{ $route->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state', $customer->state ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $customer->pincode ?? '') }}">
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Milk & Billing</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Milk Type <span class="text-danger">*</span></label>
                    <select name="milk_type" class="form-select" required>
                        @foreach(['cow' => 'Cow', 'buffalo' => 'Buffalo', 'mixed' => 'Mixed'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('milk_type', $customer->milk_type ?? 'cow') == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Morning Rate (₹/L) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="morning_rate" class="form-control"
                           value="{{ old('morning_rate', $customer->morning_rate ?? 0) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Evening Rate (₹/L) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="evening_rate" class="form-control"
                           value="{{ old('evening_rate', $customer->evening_rate ?? 0) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Default Morning Qty (L)</label>
                    <input type="number" step="0.001" name="default_qty_morning" class="form-control"
                           value="{{ old('default_qty_morning', $customer->default_qty_morning ?? 0) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Default Evening Qty (L)</label>
                    <input type="number" step="0.001" name="default_qty_evening" class="form-control"
                           value="{{ old('default_qty_evening', $customer->default_qty_evening ?? 0) }}">
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Documents</div>
            <div class="card-body">
                <input type="file" name="documents[]" class="form-control" multiple>
                <div class="form-text">PDF, JPG, PNG, DOC — max 5MB each.</div>

                @isset($customer)
                    @if($customer->documents->isNotEmpty())
                        <ul class="list-group mt-3">
                            @foreach($customer->documents as $doc)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank">{{ $doc->title }}</a>
                                    <span class="text-muted small">{{ number_format($doc->file_size / 1024, 1) }} KB</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endisset
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Photo</div>
            <div class="card-body text-center">
                @isset($customer)
                    @if($customer->photo)
                        <img src="{{ Storage::url($customer->photo) }}" class="rounded mb-2" style="width:120px;height:120px;object-fit:cover">
                    @endif
                @endisset
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent fw-semibold">Category & Status</div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <label class="form-label">Customer Category</label>
                    <select name="customer_category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('customer_category_id', $customer->customer_category_id ?? '') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', $customer->status ?? 'active') == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control"
                           value="{{ old('joining_date', isset($customer) ? $customer->joining_date?->format('Y-m-d') : now()->toDateString()) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $customer->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Identity Proof</div>
            <div class="card-body row g-3">
                <div class="col-12">
                    <label class="form-label">Proof Type</label>
                    <input type="text" name="identity_proof_type" class="form-control" placeholder="e.g. Aadhaar, Voter ID"
                           value="{{ old('identity_proof_type', $customer->identity_proof_type ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Proof Number</label>
                    <input type="text" name="identity_proof_number" class="form-control"
                           value="{{ old('identity_proof_number', $customer->identity_proof_number ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-lg me-1"></i> {{ isset($customer) ? 'Update Customer' : 'Create Customer' }}
    </button>
</div>

@push('scripts')
<script>
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
</script>
@endpush
