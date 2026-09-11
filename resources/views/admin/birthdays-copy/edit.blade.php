@extends('layouts.app')

@section('title', 'Edit Birthday')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Edit Birthday</h4>
</div>

<form action="{{ route('admin.birthdays.update', $birthday) }}" method="POST">
    @include('admin.birthdays._form')
</form>
@endsection
