<div class="d-flex gap-1">
    <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
    @can('update', $employee)
    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $employee)
    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="js-delete-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
    </form>
    @endcan
</div>
