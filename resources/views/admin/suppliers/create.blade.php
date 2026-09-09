@extends('layouts.app')

@section('title', 'New Supplier')

@section('content')
<div class="mb-3"><h4 class="mb-0">New Supplier</h4></div>
<form action="{{ route('admin.suppliers.store') }}" method="POST">
    @include('admin.suppliers._form')
</form>
@endsection
