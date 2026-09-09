@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="mb-3"><h4 class="mb-0">Edit Product — {{ $product->name }}</h4></div>
<form action="{{ route('admin.products.update', $product) }}" method="POST">
    @include('admin.products._form')
</form>
@endsection
