@extends('layouts.app')

@section('title', 'Products & Inventory')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Products & Inventory</h4>
    <div class="d-flex gap-2">
        @can('viewAny', App\Models\Supplier::class)
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-truck me-1"></i> Suppliers</a>
        @endcan
        @can('create', App\Models\Product::class)
        <a href="{{ route('admin.products.create') }}" class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i> New Product</a>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>SKU</th><th>Name</th><th>Unit</th><th>Purchase Price</th><th>Selling Price</th><th>Current Stock</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="{{ $product->isLowStock() ? 'table-warning' : '' }}">
                        <td>{{ $product->sku }}</td>
                        <td><a href="{{ route('admin.products.show', $product) }}">{{ $product->name }}</a></td>
                        <td>{{ ucfirst($product->unit) }}</td>
                        <td>{{ money($product->purchase_price) }}</td>
                        <td>{{ money($product->selling_price) }}</td>
                        <td>
                            {{ number_format($product->current_stock, 2) }}
                            @if($product->isLowStock())
                                <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Low stock"></i>
                            @endif
                        </td>
                        <td><span class="badge text-bg-{{ $product->is_active ? 'success' : 'secondary' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            @can('update', $product)
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('delete', $product)
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline js-delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).on('submit', '.js-delete-form', function (e) {
    e.preventDefault();
    const form = this;
    Swal.fire({ title: 'Delete this product?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545' })
        .then((r) => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush
