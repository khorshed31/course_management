@extends('panel.layouts.app')

@section('title', 'My Profile')

@push('styles')
<style>
  /* Avatar circle */
  .avatar {
    width: 96px; height: 96px; border-radius: 50%;
    object-fit: cover; background: #f1f3f5; display: block;
    border: 1px solid #e9ecef;
  }
  .shadow-soft { box-shadow: 0 10px 30px rgba(0,0,0,.06); }
  .card-rounded { border-radius: 18px; }
</style>
@endpush

@section('content')
<div class="container py-3">
  <div class="row g-3">
    {{-- LEFT: Profile summary --}}
    <div class="col-12 col-lg-4">
      <div class="card card-rounded shadow-soft">
        <div class="card-body text-center">
          <div class="mb-3">
            @if($user->image)
              <img class="avatar" src="{{ asset($user->image) }}" alt="{{ $user->name }}">
            @else
              <img class="avatar" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=192&background=E9ECEF&color=495057" alt="{{ $user->name }}">
            @endif
          </div>
          <h5 class="mb-1">{{ $user->name }}</h5>
          <div class="small text-muted mb-1">{{ $user->email }}</div>
          {{-- @if($user->email_verified_at)
            <span class="badge text-bg-success">Verified</span>
          @else
            <span class="badge text-bg-secondary">Not Verified</span>
          @endif --}}

          <hr class="my-4">
          <div class="text-start small">
            <div class="d-flex justify-content-between mb-1"><span>Role</span><span class="fw-semibold">{{ ucfirst($user->role) }}</span></div>
            <div class="d-flex justify-content-between mb-1"><span>Phone</span><span class="fw-semibold">{{ $user->phone ?: '—' }}</span></div>
            <div class="d-flex justify-content-between"><span>Joined</span><span class="fw-semibold">{{ $user->created_at?->format('d M Y') }}</span></div>
          </div>
        </div>
      </div>
    </div>

    {{-- RIGHT: Forms --}}
    <div class="col-12 col-lg-8">
      {{-- Flash messages --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if(session('success_password'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success_password') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      {{-- Update Profile --}}
      <div class="card card-rounded shadow-soft mb-3">
        <div class="card-body">
          <h5 class="mb-3">Edit Profile</h5>

          <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-md-6">
              <label class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name', $user->name) }}" required>
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email', $user->email) }}" required>
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                     value="{{ old('phone', $user->phone) }}">
              @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label d-flex justify-content-between">
                <span>Profile Image</span>
                <small class="text-muted">jpg, jpeg, png, webp • max 2MB</small>
              </label>
              <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
              @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

              <div class="mt-2 d-flex align-items-center gap-2">
                <img id="preview" src="{{ $user->image ? asset($user->image) : '' }}" alt="" style="height:56px;width:56px;border-radius:10px;object-fit:cover;display:{{ $user->image ? 'block' : 'none' }};">
                <small class="text-muted" id="previewLabel" style="display:{{ $user->image ? 'none':'block' }}">No image selected</small>
              </div>
            </div>

            <div class="col-12">
              <button class="btn btn-primary"><i class="bi bi-check2-circle"></i> Save Changes</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Change Password --}}
      <div class="card card-rounded shadow-soft">
        <div class="card-body">
          <h5 class="mb-3">Change Password</h5>
          <form action="{{ route('profile.password') }}" method="post" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-12">
              <label class="form-label">Current Password <span class="text-danger">*</span></label>
              <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
              @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">New Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="col-12">
              <button class="btn btn-outline-primary"><i class="bi bi-shield-lock"></i> Update Password</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Show preview when a new image is selected
  document.getElementById('image')?.addEventListener('change', function (e) {
    const file = e.target.files?.[0];
    const preview = document.getElementById('preview');
    const label = document.getElementById('previewLabel');
    if (file) {
      const reader = new FileReader();
      reader.onload = ev => {
        preview.src = ev.target.result;
        preview.style.display = 'block';
        if (label) label.style.display = 'none';
      }
      reader.readAsDataURL(file);
    } else {
      preview.src = '';
      preview.style.display = 'none';
      if (label) label.style.display = 'block';
    }
  });
</script>
@endpush
