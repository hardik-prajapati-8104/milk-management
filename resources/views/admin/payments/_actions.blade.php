<div class="d-flex gap-1">
    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
    <a href="{{ route('admin.payments.pdf', $payment) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Receipt"><i class="bi bi-file-earmark-pdf"></i></a>
    @can('delete', $payment)
    <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="js-delete-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Reverse"><i class="bi bi-arrow-counterclockwise"></i></button>
    </form>
    @endcan
</div>
