@extends('layouts.app')

@section('title', 'Users & Roles')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Users</h4>
    <div class="d-flex gap-2">
        @can('viewAny', Spatie\Permission\Models\Role::class)
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-shield-lock me-1"></i> Manage Roles</a>
        @endcan
        @can('create', App\Models\User::class)
        <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm"><i class="bi bi-person-plus me-1"></i> New User</a>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Employee Code</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->employee_code ?? '—' }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge text-bg-primary">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td><span class="badge text-bg-{{ ['active'=>'success','inactive'=>'secondary','suspended'=>'danger'][$user->status] }}">{{ ucfirst($user->status) }}</span></td>
                        <td class="text-muted small">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                        <td class="text-end">
                            @can('update', $user)
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('delete', $user)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline js-delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No users yet.</td></tr>
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
    Swal.fire({ title: 'Delete this user?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545' })
        .then((r) => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush
