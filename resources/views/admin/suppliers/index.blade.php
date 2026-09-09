@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Suppliers</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-seam me-1"></i> Products</a>
        @can('create', App\Models\Supplier::class)
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i> New Supplier</a>
        @endcan
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Code</th><th>Name</th><th>Mobile</th><th>Outstanding</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->supplier_code }}</td>
                        <td><a href="{{ route('admin.suppliers.show', $supplier) }}">{{ $supplier->name }}</a></td>
                        <td>{{ $supplier->mobile ?: '—' }}</td>
                        <td>{{ money($supplier->outstanding_balance) }}</td>
                        <td><span class="badge text-bg-{{ $supplier->is_active ? 'success' : 'secondary' }}">{{ $supplier->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            @can('update', $supplier)
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('delete', $supplier)
                            <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline js-delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No suppliers yet.</td></tr>
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
    Swal.fire({ title: 'Delete this supplier?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete', confirmButtonColor: '#dc3545' })
        .then((r) => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush
