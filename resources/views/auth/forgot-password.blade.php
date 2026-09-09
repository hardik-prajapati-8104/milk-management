@extends('layouts.guest')

@section('title', 'Forgot Password')
@section('subtitle', 'Enter your email to receive a reset link')

@section('content')
    <form method="POST" action="#">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <button type="submit" class="btn btn-success w-100">
            <i class="bi bi-envelope me-1"></i> Send Reset Link
        </button>
        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="small text-muted">Back to login</a>
        </div>
    </form>
@endsection
