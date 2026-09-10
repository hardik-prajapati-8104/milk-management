<div class="d-flex gap-1">
    @can('update', $birthday)
    <a href="{{ route('admin.birthdays.edit', $birthday) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $birthday)
    <form action="{{ route('admin.birthdays.destroy', $birthday) }}" method="POST" class="js-delete-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
    </form>
    @endcan
</div>
