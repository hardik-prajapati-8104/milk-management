<div class="d-flex gap-1">
    @can('update', $holiday)
    <a href="{{ route('admin.holidays.edit', $holiday) }}" class="btn btn-sm btn-outline-primary" title="{{ __('app.common.edit') }}"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $holiday)
    <form action="{{ route('admin.holidays.destroy', $holiday) }}" method="POST" class="js-delete-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('app.common.delete') }}"><i class="bi bi-trash"></i></button>
    </form>
    @endcan
</div>
