<div class="d-flex gap-1">
    @can('update', $purchase)
    <a href="{{ route('admin.milk-purchases.edit', $purchase) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @if($purchase->payment_status !== 'paid')
        <button class="btn btn-sm btn-outline-success js-record-payment"
                data-id="{{ $purchase->id }}" data-outstanding="{{ $purchase->outstandingAmount() }}"
                data-url="{{ route('admin.milk-purchases.record-payment', $purchase) }}" title="Record Payment">
            <i class="bi bi-cash-coin"></i>
        </button>
    @endif
    @can('delete', $purchase)
    <form action="{{ route('admin.milk-purchases.destroy', $purchase) }}" method="POST" class="d-inline js-delete-form">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
    </form>
    @endcan
</div>
