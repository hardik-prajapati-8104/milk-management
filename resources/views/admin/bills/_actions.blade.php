<div class="d-flex gap-1">
    <a href="{{ route('admin.bills.show', $bill) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
    <a href="{{ route('admin.bills.pdf', $bill) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="PDF"><i class="bi bi-file-earmark-pdf"></i></a>
    @can('update', $bill)
    <a href="{{ route('admin.bills.edit', $bill) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $bill)
    <form action="{{ route('admin.bills.destroy', $bill) }}" method="POST" class="js-delete-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
    </form>
    @endcan
</div>
