<div class="d-flex gap-1">
    @can('update', $expense)
    <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $expense)
    <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" class="js-delete-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
    </form>
    @endcan
</div>
