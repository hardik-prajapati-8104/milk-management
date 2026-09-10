<div class="d-flex gap-1">
    <button type="button" class="btn btn-sm btn-outline-secondary js-view-error" data-id="{{ $error->id }}" title="View stack trace">
        <i class="bi bi-eye"></i>
    </button>
    @can('manage', App\Models\ActivityLog::class)
        @if (! $error->is_resolved)
        <form action="{{ route('admin.audit.error-resolve', $error) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success" title="Mark resolved">
                <i class="bi bi-check2"></i>
            </button>
        </form>
        @endif
    @endcan
</div>
