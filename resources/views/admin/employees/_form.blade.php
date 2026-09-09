@csrf
@isset($employee)
    @method('PUT')
@endisset

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $employee->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Designation <span class="text-danger">*</span></label>
                    <input type="text" name="designation" class="form-control" value="{{ old('designation', $employee->designation ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $employee->mobile ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Salary <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="salary" class="form-control" value="{{ old('salary', $employee->salary ?? 0) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control"
                           value="{{ old('joining_date', isset($employee) ? $employee->joining_date?->format('Y-m-d') : now()->toDateString()) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'terminated' => 'Terminated'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', $employee->status ?? 'active') == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address ?? '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Documents</label>
                    <input type="file" name="documents[]" class="form-control" multiple>
                    @isset($employee)
                        @if($employee->documents->isNotEmpty())
                            <ul class="list-group mt-2">
                                @foreach($employee->documents as $doc)
                                    <li class="list-group-item"><a href="{{ Storage::url($doc->file_path) }}" target="_blank">{{ $doc->title }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    @endisset
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Photo</div>
            <div class="card-body text-center">
                @isset($employee)
                    @if($employee->photo)
                        <img src="{{ Storage::url($employee->photo) }}" class="rounded-circle mb-2" style="width:100px;height:100px;object-fit:cover">
                    @endif
                @endisset
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-lg me-1"></i> {{ isset($employee) ? 'Update Employee' : 'Create Employee' }}
    </button>
</div>
