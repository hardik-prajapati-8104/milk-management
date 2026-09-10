@extends('layouts.app')

@section('title', 'New Holiday')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">New Holiday</h4>
</div>

<form action="{{ route('admin.holidays.store') }}" method="POST">
    @include('admin.holidays._form')
</form>
@endsection
