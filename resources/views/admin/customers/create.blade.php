@extends('layouts.app')

@section('title', 'New Customer')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">New Customer</h4>
</div>

<form action="{{ route('admin.customers.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.customers._form')
</form>
@endsection
