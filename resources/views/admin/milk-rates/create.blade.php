@extends('layouts.app')

@section('title', 'New Rate Card')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">New Milk Rate Card</h4>
</div>

<form action="{{ route('admin.milk-rates.store') }}" method="POST">
    @include('admin.milk-rates._form')
</form>
@endsection
