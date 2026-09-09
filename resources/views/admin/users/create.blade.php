@extends('layouts.app')

@section('title', 'New User')

@section('content')
<div class="mb-3"><h4 class="mb-0">New User</h4></div>
<form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.users._form')
</form>
@endsection
