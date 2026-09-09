@csrf
@isset($user)
    @method('PUT')
@endisset

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('status', $user->status ?? 'active') == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password {{ isset($user) ? '(leave blank to keep current)' : '' }} @if(!isset($user))<span class="text-danger">*</span>@endif</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ isset($user) ? '' : 'required' }}>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption->name }}" @selected(old('role', isset($user) ? $user->getRoleNames()->first() : '') == $roleOption->name)>{{ $roleOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assigned Route (for Delivery Boy role)</label>
                    <select name="route_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" @selected(old('route_id', $user->route_id ?? '') == $route->id)>{{ $route->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent fw-semibold">Photo</div>
            <div class="card-body text-center">
                @isset($user)
                    @if($user->photo)
                        <img src="{{ Storage::url($user->photo) }}" class="rounded-circle mb-2" style="width:100px;height:100px;object-fit:cover">
                    @endif
                @endisset
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-lg me-1"></i> {{ isset($user) ? 'Update User' : 'Create User' }}
    </button>
</div>
