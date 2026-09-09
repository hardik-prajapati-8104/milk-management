@extends('layouts.app')

@section('title', 'New Role')

@section('content')
<div class="mb-3"><h4 class="mb-0">New Role</h4></div>

<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    <div class="card shadow-sm mb-3" style="max-width:400px">
        <div class="card-body">
            <label class="form-label">Role Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-transparent fw-semibold">Permissions</div>
        <div class="card-body p-0">
            @include('admin.roles._permission-matrix', ['checked' => old('permissions', [])])
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Create Role</button>
    </div>
</form>
@endsection
