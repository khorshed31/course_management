@extends('frontend.layouts.app')
@section('title','Forgot Password')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center min-vh-100" style="background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);">
    <div class="card shadow-lg border-0 w-100" style="max-width: 500px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <div class="mb-2">
                    <i class="bi bi-shield-lock" style="font-size:2.5rem; color:#6366f1;"></i>
                </div>
                <h4 class="fw-bold mb-1" style="color:#1e293b;">Forgot your password?</h4>
                <p class="text-muted mb-0">Enter your email and we’ll email you a reset link.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="email">Email address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" required autofocus placeholder="you@example.com">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button class="btn btn-primary btn-lg w-100 shadow-sm d-flex justify-content-center align-items-center" type="submit" style="background: linear-gradient(90deg,#6366f1,#2563eb); border: none;" id="resetBtn">
                    <span id="btnText"><i class="bi bi-envelope-paper me-2"></i>Email Password Reset Link</span>
                    <span id="btnLoader" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.querySelector('form');
                        const btn = document.getElementById('resetBtn');
                        const btnText = document.getElementById('btnText');
                        const btnLoader = document.getElementById('btnLoader');
                        form.addEventListener('submit', function() {
                            btn.disabled = true;
                            btnText.classList.add('d-none');
                            btnLoader.classList.remove('d-none');
                        });
                    });
                </script>
            </form>
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-decoration-none text-primary fw-semibold">
                    <i class="bi bi-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection