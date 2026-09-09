@extends('layouts.app')

@section('title', 'Edit Rate Card')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Edit Rate Card — {{ $rate->effective_date->format('d M Y') }}</h4>
</div>

<form action="{{ route('admin.milk-rates.update', $rate) }}" method="POST">
    @include('admin.milk-rates._form')
</form>
@endsection
