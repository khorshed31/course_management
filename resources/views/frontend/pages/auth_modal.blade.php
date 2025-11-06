@php $redirect = $redirect ?? url()->current(); @endphp

<div class="modal fade auth-modal" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content border-0 rounded-4 overflow-hidden">
      <div class="modal-hero">
        <div class="layer"></div>
        <div class="container px-4 py-4 position-relative">
          <h5 class="text-white mb-0" id="loginModalLabel">
            <i class="fa fa-user-circle me-2"></i> Welcome back
          </h5>
          <div class="text-white-50 small">Sign in or create your account in seconds</div>
        </div>
      </div>
      <button type="button" class="btn-close modal-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>

      <div class="modal-body p-4">
        {{-- Tabs --}}
        <ul class="nav nav-pills mb-3 justify-content-center gap-2" id="authTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active pill" id="login-tab" data-bs-toggle="pill" data-bs-target="#login-pane" type="button" role="tab">
              <i class="fa fa-sign-in-alt me-1"></i> Login
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link pill" id="register-tab" data-bs-toggle="pill" data-bs-target="#register-pane" type="button" role="tab">
              <i class="fa fa-user-plus me-1"></i> Register
            </button>
          </li>
        </ul>

        <div class="tab-content" id="authTabContent">
          {{-- Login --}}
          <div class="tab-pane fade show active" id="login-pane" role="tabpanel">
            <form method="POST" action="{{ route('login') }}?redirect={{ urlencode($redirect) }}">
              @csrf
              <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control soft-input" name="email" required placeholder="you@example.com">
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control soft-input" name="password" required placeholder="••••••••">
              </div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="remember" name="remember">
                  <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="small link-primary">Forgot password?</a>
              </div>
              <button type="submit" class="btn btn-gradient w-100 btn-lg lift">
                <i class="fa fa-arrow-right me-2"></i> Login
              </button>
            </form>
          </div>

          {{-- Register --}}
          <div class="tab-pane fade" id="register-pane" role="tabpanel">
            <form method="POST" action="{{ route('register') }}?redirect={{ urlencode($redirect) }}">
              @csrf
              <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" class="form-control soft-input" name="name" required placeholder="Your full name">
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control soft-input" name="email" required placeholder="you@example.com">
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" class="form-control soft-input" name="phone" required placeholder="+965 5x xxx xxx">
              </div>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Password</label>
                  <input type="password" class="form-control soft-input" name="password" required placeholder="••••••••">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Confirm</label>
                  <input type="password" class="form-control soft-input" name="password_confirmation" required placeholder="••••••••">
                </div>
              </div>
              <button type="submit" class="btn btn-gradient w-100 btn-lg lift mt-3">
                <i class="fa fa-user-check me-2"></i> Create account
              </button>
            </form>
          </div>
        </div>
{{-- 
        <div class="text-center mt-3">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
            <i class="fa fa-times me-1"></i> Cancel
          </button>
        </div> --}}

        <div class="text-center mt-4">
          <div class="small text-muted">We respect your privacy. No spam ever.</div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  .auth-modal .modal-content{ box-shadow:0 24px 64px rgba(32,56,117,.25); }
  .modal-hero{
    position:relative; background: radial-gradient(120% 120% at 0% 0%, #6dd5ed 0%, #4f8cff 50%, #2a3d7a 100%);
  }
  .modal-hero .layer{ position:absolute; inset:0; background:linear-gradient(120deg, rgba(0,0,0,.15), rgba(0,0,0,.25)); }
  .pill{ border-radius:999px; padding:.5rem 1rem; }
  .soft-input{
    background:#f7f9ff; border:1px solid #e4e9f7; border-radius:12px; padding:.75rem .9rem;
  }
  .soft-input:focus{
    border-color:#1f64ff; box-shadow:0 0 0 .25rem rgba(31,100,255,.12);
  }
 .auth-modal .modal-close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  z-index: 10;
  filter: invert(1) brightness(200%);
  opacity: 0.9;
}

/* Flip for RTL layout */
html[dir="rtl"] .auth-modal .modal-close-btn {
  right: auto;
  left: 1rem;
}

.auth-modal .modal-close-btn:hover {
  opacity: 1;
}
</style>
@endpush
