@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')
<div class="mb-3">
    <h4 class="mb-0">Edit Expense — {{ $expense->expense_number }}</h4>
</div>

<form action="{{ route('admin.expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
    @include('admin.expenses._form')
</form>
@endsection
