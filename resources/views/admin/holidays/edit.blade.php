@extends('layouts.app')

@section('title', 'Edit Holiday')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Edit Holiday</h4>
</div>

<form action="{{ route('admin.holidays.update', $holiday) }}" method="POST">
    @include('admin.holidays._form')
</form>
@endsection
