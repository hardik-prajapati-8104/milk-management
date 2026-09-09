@extends('layouts.app')

@section('title', 'Milk Rates')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="mb-0">Milk Rate History</h4>
    @can('create', App\Models\MilkRate::class)
    <a href="{{ route('admin.milk-rates.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-lg me-1"></i> New Rate Card
    </a>
    @endcan
</div>

@php
    $current = $rates->getCollection()->firstWhere('is_active', true);
@endphp

@if($current)
<div class="card shadow-sm mb-4 border-success">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <span class="badge text-bg-success mb-2">Currently Applied</span>
                <h6 class="mb-1">Effective from {{ $current->effective_date->format('d M Y') }}</h6>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-6 col-md-2"><span class="text-muted small d-block">Cow Morning</span>{{ money($current->cow_morning_rate) }}</div>
            <div class="col-6 col-md-2"><span class="text-muted small d-block">Cow Evening</span>{{ money($current->cow_evening_rate) }}</div>
            <div class="col-6 col-md-2"><span class="text-muted small d-block">Buffalo Morning</span>{{ money($current->buffalo_morning_rate) }}</div>
            <div class="col-6 col-md-2"><span class="text-muted small d-block">Buffalo Evening</span>{{ money($current->buffalo_evening_rate) }}</div>
            <div class="col-6 col-md-2"><span class="text-muted small d-block">Mixed</span>{{ money($current->mixed_rate) }}</div>
        </div>
    </div>
</div>
@endif

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Effective Date</th>
                    <th>Cow (M/E)</th>
                    <th>Buffalo (M/E)</th>
                    <th>Mixed</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rates as $rate)
                <tr>
                    <td>{{ $rate->effective_date->format('d M Y') }}</td>
                    <td>{{ money($rate->cow_morning_rate) }} / {{ money($rate->cow_evening_rate) }}</td>
                    <td>{{ money($rate->buffalo_morning_rate) }} / {{ money($rate->buffalo_evening_rate) }}</td>
                    <td>{{ money($rate->mixed_rate) }}</td>
                    <td>
                        <span class="badge text-bg-{{ $rate->is_active ? 'success' : 'secondary' }}">
                            {{ $rate->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $rate->createdBy->name ?? '—' }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            @can('update', $rate)
                            <a href="{{ route('admin.milk-rates.edit', $rate) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('delete', $rate)
                            <form action="{{ route('admin.milk-rates.destroy', $rate) }}" method="POST" class="js-delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No rate cards yet. Create the first one to start billing.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-transparent">
        {{ $rates->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).on('submit', '.js-delete-form', function (e) {
    e.preventDefault();
    const form = this;
    Swal.fire({
        title: 'Delete this rate card?',
        text: 'Prefer marking it inactive instead if past bills may reference it.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        confirmButtonColor: '#dc3545',
    }).then((result) => { if (result.isConfirmed) form.submit(); });
});
</script>
@endpush
