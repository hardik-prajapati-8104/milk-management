@extends('layouts.app')

@section('title', 'New Birthday')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">New Birthday</h4>
</div>

<form action="{{ route('admin.birthdays.store') }}" method="POST">
    @include('admin.birthdays._form')
</form>
@endsection
