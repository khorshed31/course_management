@extends('frontend.layouts.app')

@section('title', 'Register')

@push('styles')
<style>
  body {
    background: linear-gradient(135deg, #4f8cff 0%, #6dd5ed 100%);
    min-height: 100vh;
  }
  .register-card {
    border-radius: 1rem;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
    background: rgba(255,255,255,0.95);
    padding: 2rem 1.5rem;
  }
  .brand-logo img {
    max-width: 160px;
  }
  .form-control-lg {
    border-radius: 0.5rem;
  }
  .btn-primary {
    background: linear-gradient(90deg, #4f8cff 0%, #6dd5ed 100%);
    border: none;
    font-weight: 600;
    letter-spacing: 1px;
    box-shadow: 0 4px 12px rgba(79,140,255,0.15);
    transition: background 0.3s;
  }
  .btn-primary:hover {
    background: linear-gradient(90deg, #6dd5ed 0%, #4f8cff 100%);
  }
  .form-note-s2 a {
    color: #4f8cff;
    font-weight: 500;
  }
  @media (max-width: 576px) {
    .register-card {
      padding: 1rem 0.5rem;
    }
    .brand-logo img {
      max-width: 120px;
    }
  }
</style>
@endpush

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="w-100" style="max-width: 600px;margin-top: 90px;">
      <div class="register-card">
        <h3 class="mb-3 text-center fw-bold" style="color:#4f8cff;">Create Account</h3>
        <p class="text-center text-muted mb-4">Join and start using <span class="fw-semibold">{{ config('app.name') }}</span>.</p>
        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Your name" required autofocus>
            @error('name')
              <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
            @error('email')
              <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control form-control-lg @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Your phone number" required>
            @error('phone')
              <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
              <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" placeholder="Create a password" required>
              <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                <i class="fa fa-eye"></i>
              </button>
            </div>
            @error('password')
              <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group">
              <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" placeholder="Re-type password" required>
              <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                <i class="fa fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="agree" required>
            <label class="form-check-label" for="agree">
              I agree to the <a href="#" class="link-primary">Terms &amp; Conditions</a>.
            </label>
          </div>

          <div class="d-grid mb-3">
            <button class="btn btn-lg btn-primary" type="submit">Create Account</button>
          </div>
        </form>
        <div class="form-note-s2 text-center pt-3">
          Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('auth-footer')
<div class="container py-3">
  <div class="row g-3 align-items-center">
    <div class="col-12 col-lg-6 text-center text-lg-start mb-2 mb-lg-0">
      <p class="text-soft mb-0">&copy; {{ now()->year }} {{ config('app.name') }}. All Rights Reserved.</p>
    </div>
    <div class="col-12 col-lg-6 text-center text-lg-end">
      <ul class="nav justify-content-center justify-content-lg-end">
        <li class="nav-item"><a class="nav-link link-primary" href="#">Terms &amp; Conditions</a></li>
        <li class="nav-item"><a class="nav-link link-primary" href="#">Privacy Policy</a></li>
        <li class="nav-item"><a class="nav-link link-primary" href="#">Help</a></li>
      </ul>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function togglePassword(id) {
    const input = document.getElementById(id);
    if (input.type === "password") {
      input.type = "text";
    } else {
      input.type = "password";
    }
  }
</script>
@endpush
