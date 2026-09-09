@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="mb-3"><h4 class="mb-0">Edit Role — {{ $role->name }}</h4></div>

<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card shadow-sm">
        <div class="card-header bg-transparent fw-semibold">Permissions</div>
        <div class="card-body p-0">
            @include('admin.roles._permission-matrix', ['checked' => old('permissions', $rolePermissions)])
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Save Permissions</button>
    </div>
</form>
@endsection
