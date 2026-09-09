@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="mb-3"><h4 class="mb-0">Edit User — {{ $user->name }}</h4></div>
<form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
    @include('admin.users._form')
</form>
@endsection
