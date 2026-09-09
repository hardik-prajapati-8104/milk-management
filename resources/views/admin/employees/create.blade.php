@extends('layouts.app')

@section('title', 'New Employee')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">New Employee</h4>
</div>

<form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.employees._form')
</form>
@endsection
