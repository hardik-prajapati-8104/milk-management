@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Roles</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-people me-1"></i> Users</a>
        @can('create', Spatie\Permission\Models\Role::class)
        <a href="{{ route('admin.roles.create') }}" class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i> New Role</a>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Role</th><th>Permissions</th><th>Users</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->permissions->count() }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td class="text-end">
                            @can('update', $role)
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('delete', $role)
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline js-delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No roles yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).on('submit', '.js-delete-form', function (e) {
    e.preventDefault();
    const form = this;
    Swal.fire({ title: 'Delete this role?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545' })
        .then((r) => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush
