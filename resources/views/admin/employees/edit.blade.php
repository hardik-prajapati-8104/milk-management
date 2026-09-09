@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Edit Employee — {{ $employee->employee_id }}</h4>
</div>

<form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
    @include('admin.employees._form')
</form>
@endsection
