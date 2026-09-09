@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="mb-3"><h4 class="mb-0">Edit Supplier — {{ $supplier->name }}</h4></div>
<form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
    @include('admin.suppliers._form')
</form>
@endsection
