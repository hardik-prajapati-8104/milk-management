<div class="d-flex gap-1">
    @can('update', $sale)
    <a href="{{ route('admin.cash-sales.edit', $sale) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $sale)
    <form action="{{ route('admin.cash-sales.destroy', $sale) }}" method="POST" class="d-inline js-delete-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
    </form>
    @endcan
</div>
