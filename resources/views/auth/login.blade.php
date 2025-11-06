@extends('frontend.layouts.app')

@section('title', 'Login & Register')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">
<style>
  body {
    background: linear-gradient(135deg, #4f8cff 0%, #6dd5ed 100%);
    min-height: 100vh;
    font-family: 'Arial', sans-serif;
    color: #333;
  }
  .auth-container {
    max-width: 900px;
  }
  .auth-card {
    border-radius: 1.5rem;
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
    background: rgba(255, 255, 255, 0.95);
    padding: 3rem 2rem;
  }
  .auth-card-body {
    padding: 1.5rem;
  }
  .auth-tabs .nav-link {
    border: none;
    font-weight: 600;
    color: #4f8cff;
    transition: 0.3s;
  }
  .auth-tabs .nav-item.show .nav-link, .auth-tabs .nav-link.active {
    color: #fff;
    background-color: #4f8cff;
    border-radius: 0.5rem 0.5rem 0 0;
  }
  .tab-content {
    padding-top: 30px;
  }
  .auth-label {
    font-weight: bold;
    color: #555;
  }
  .auth-form-control {
    border-radius: 1rem;
    padding: 1.25rem;
  }
  .auth-btn-primary {
    background: linear-gradient(90deg, #4f8cff 0%, #6dd5ed 100%);
    border: none;
    border-radius: 1rem;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(79, 140, 255, 0.2);
    transition: background 0.3s ease;
  }
  .auth-btn-primary:hover {
    background: linear-gradient(90deg, #6dd5ed 0%, #4f8cff 100%);
  }
  .auth-form-note a {
    color: #4f8cff;
    font-weight: 600;
    text-decoration: underline;
  }
  .auth-form-control:focus {
    border-color: #4f8cff;
    box-shadow: 0 0 0 0.25rem rgba(79, 140, 255, 0.25);
  }
  .nav-item {
    margin-right: 10px;
  }
  .eye-btn {
    cursor: pointer;
  }
  @media (max-width: 576px) {
    .auth-card {
      padding: 2rem 1rem;
    }
    .brand-logo img {
      max-width: 120px;
    }
  }
  .invalid-feedback {
    color: #dc3545;
  }
</style>
@endpush

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="w-100 auth-container" style="max-width: 800px;margin-top: 90px;">
      <div class="card auth-card">
        <ul class="nav nav-tabs auth-tabs" id="authTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="login-tab" data-bs-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">Sign In</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="register-tab" data-bs-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">Create Account</a>
          </li>
        </ul>
        <div class="tab-content" id="authTabsContent">
          <!-- Login Form -->
          <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
            <div class="card-body auth-card-body">
              <h4 class="text-center mb-4" style="font-weight:700; color:#1e3c72;">Sign In</h4>
              <p class="text-center text-muted mb-4">Access your account using your email and password.</p>
              <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                  <label for="email" class="auth-label">Email</label>
                  <input type="email" class="form-control auth-form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required autofocus>
                  @error('email') <span class="invalid-feedback d-block" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                  <label for="password" class="auth-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control auth-form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="btn btn-outline-secondary eye-btn" id="toggleLoginPassword">
                      <i class="fa fa-eye"></i>
                    </button>
                  </div>
                  @error('password') <span class="invalid-feedback d-block" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="d-grid mb-2">
                  <button class="btn btn-lg auth-btn-primary" type="submit">Sign in</button>
                </div>
              </form>
            </div>
          </div>

          <!-- Register Form -->
          <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
            <div class="card-body auth-card-body">
              <h4 class="text-center mb-4" style="font-weight:700; color:#1e3c72;">Create Account</h4>
              <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                  <label for="name" class="auth-label">Full Name</label>
                  <input type="text" class="form-control auth-form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Your name" required autofocus>
                  @error('name') <span class="invalid-feedback d-block" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                  <label for="email" class="auth-label">Email Address</label>
                  <input type="email" class="form-control auth-form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                  @error('email') <span class="invalid-feedback d-block" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                  <label for="phone" class="auth-label">Phone Number</label>
                  <input type="text" class="form-control auth-form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Your phone number" required>
                  @error('phone') <span class="invalid-feedback d-block" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                  <label for="password" class="auth-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control auth-form-control @error('password') is-invalid @enderror" id="registerPassword" name="password" placeholder="Create a password" required>
                    <button type="button" class="btn btn-outline-secondary eye-btn" id="toggleRegisterPassword">
                      <i class="fa fa-eye"></i>
                    </button>
                  </div>
                  @error('password') <span class="invalid-feedback d-block" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                  <label for="password_confirmation" class="auth-label">Confirm Password</label>
                  <input type="password" class="form-control auth-form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-type password" required>
                </div>
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="agree" required>
                  <label class="form-check-label" for="agree">I agree to the <a href="#" class="link-primary">Terms & Conditions</a>.</label>
                </div>
                <div class="d-grid mb-3">
                  <button class="btn btn-lg auth-btn-primary" id="submitBtn" type="submit" disabled>Create Account</button>
                </div>
              </form>
            </div>
          </div>
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
        <li class="nav-item"><a class="nav-link link-primary" href="#">Terms & Conditions</a></li>
        <li class="nav-item"><a class="nav-link link-primary" href="#">Privacy Policy</a></li>
        <li class="nav-item"><a class="nav-link link-primary" href="#">Help</a></li>
      </ul>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
<script>
  // Show SweetAlert if there is an error in the session
  @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: '{{ session('error') }}',
      confirmButtonText: 'OK'
    });
  @endif

  // Function to toggle password visibility for login form
  function togglePassword() {
    const passwordField = document.getElementById('password');
    const icon = document.getElementById('toggleLoginPassword').querySelector('i');
    if (passwordField.type === "password") {
      passwordField.type = "text";
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordField.type = "password";
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }

  // Function to toggle password visibility for register form
  function toggleRegisterPassword() {
    const passwordField = document.getElementById('registerPassword');
    const icon = document.getElementById('toggleRegisterPassword').querySelector('i');
    if (passwordField.type === "password") {
      passwordField.type = "text";
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordField.type = "password";
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }

  // Add event listeners to run toggle password visibility
  document.getElementById('toggleLoginPassword').addEventListener('click', togglePassword);
  document.getElementById('toggleRegisterPassword').addEventListener('click', toggleRegisterPassword);

  // Function to check if passwords match for registration
  function checkPasswordsMatch() {
    const password = document.getElementById('registerPassword');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const submitButton = document.getElementById('submitBtn');
  
    if (password.value === passwordConfirmation.value) {
      submitButton.disabled = false; // Enable the submit button if passwords match
    } else {
      submitButton.disabled = true; // Disable the submit button if passwords do not match
    }
  }

  // Add event listeners to check password match
  document.getElementById('registerPassword').addEventListener('input', checkPasswordsMatch);
  document.getElementById('password_confirmation').addEventListener('input', checkPasswordsMatch);
</script>
@endpush
