@extends('frontend.layouts.app')
@section('title','Reset Password')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg border-0 w-100" style="max-width: 500px; margin-top: 90px">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:48px;"> --}}
                <h4 class="mt-3 fw-bold text-primary">Reset Password</h4>
                <p class="text-muted small">Enter your email and new password below.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus placeholder="Enter your email">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">New Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="New password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm password">
                </div>

                <button class="btn btn-primary w-100 py-2 fw-bold" type="submit">
                    <i class="bi bi-shield-lock"></i> Reset Password
                </button>
            </form>
        </div>
        <div class="card-footer bg-white text-center py-3">
            <a href="{{ route('login') }}" class="text-decoration-none text-primary small">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>
</div>
@endsection