@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Edit Customer — {{ $customer->consumer_id }}</h4>
</div>

<form action="{{ route('admin.customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
    @include('admin.customers._form')
</form>
@endsection
