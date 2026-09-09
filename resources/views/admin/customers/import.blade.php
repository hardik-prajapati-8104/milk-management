@extends('layouts.app')

@section('title', 'Import Customers')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Import Customers from Excel</h4>
</div>

<div class="card shadow-sm" style="max-width:560px">
    <div class="card-body">
        <p class="text-muted small">
            Upload an .xlsx, .xls, or .csv file with columns: <code>name, mobile, email, address,
            city, state, pincode, milk_type, morning_rate, evening_rate, default_qty_morning,
            default_qty_evening, joining_date</code>. Consumer IDs are always auto-generated.
        </p>
        <form action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
                @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-upload me-1"></i> Import</button>
            </div>
        </form>
    </div>
</div>
@endsection
