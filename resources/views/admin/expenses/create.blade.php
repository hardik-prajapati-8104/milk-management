@extends('layouts.app')

@section('title', 'New Expense')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">New Expense</h4>
</div>

<form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.expenses._form')
</form>
@endsection
