<div class="d-flex gap-1">
    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-secondary" title="View">
        <i class="bi bi-eye"></i>
    </a>
    @can('update', $customer)
    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    @endcan
    @can('delete', $customer)
    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline js-delete-form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
            <i class="bi bi-trash"></i>
        </button>
    </form>
    @endcan
</div>
