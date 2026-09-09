@extends('layouts.app')

@section('title', 'New Product')

@section('content')
<div class="mb-3"><h4 class="mb-0">New Product</h4></div>
<form action="{{ route('admin.products.store') }}" method="POST">
    @include('admin.products._form')
</form>
@endsection
