@extends('panel.layouts.app')

@section('title','Settings')

@push('styles')
<style>
  .thumb { display:inline-block; padding:6px; border:1px solid #e5e7eb; border-radius:8px; background:#f8fafc }
  .thumb img { max-height:48px; display:block }
</style>
@endpush

@section('content')
<div class="nk-block nk-block-lg">
  <div class="nk-block-head">
    <div class="nk-block-head-content">
      <h4 class="nk-block-title">Site Settings</h4>
      @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger mt-2">
          <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif
    </div>
  </div>

  <div class="card card-bordered">
    <div class="card-inner">
      <form method="POST" action="{{ route('admin.settings.save') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
          <div class="col-lg-6">
            <div class="form-group">
              <label class="form-label">Site Name</label>
              <input type="text" class="form-control" name="kv[site_name]" value="{{ old('kv.site_name',$kv['site_name']) }}">
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group">
              <label class="form-label">Contact Email</label>
              <input type="email" class="form-control" name="kv[contact_email]" value="{{ old('kv.contact_email',$kv['contact_email']) }}">
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <label class="form-label">Footer Text</label>
              <input type="text" class="form-control" name="kv[footer_text]" value="{{ old('kv.footer_text',$kv['footer_text']) }}">
            </div>
          </div>
        </div>

        {{-- Language & RTL section --}}
        <div class="row g-4 mt-1">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Default Language</label>
              <select name="kv[default_locale]" class="form-control">
                <option value="ar" {{ old('kv.default_locale',$kv['default_locale'])=='ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                <option value="en" {{ old('kv.default_locale',$kv['default_locale'])=='en' ? 'selected' : '' }}>English</option>
              </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label d-flex align-items-center justify-content-between">
                <span>Enable RTL by default</span>
              </label>
              <input type="hidden" name="kv[rtl_enabled]" value="0">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rtl_enabled"
                      name="kv[rtl_enabled]" value="1"
                      {{ old('kv.rtl_enabled',$kv['rtl_enabled']) ? 'checked' : '' }}>
                <label class="form-check-label" for="rtl_enabled">
                  When enabled, site opens in Arabic (RTL).
                </label>
              </div>
            </div>
          </div>
        </div>


        <div class="row g-4 mt-1">
          {{-- Logo --}}
          <div class="col-md-6">
            <label class="form-label d-flex align-items-center justify-content-between">
              <span>Logo</span>
              @if($kv['logo'])
                <span class="thumb"><img src="{{ asset($kv['logo']) }}" alt="logo"></span>
              @endif
            </label>
            <input type="file" name="logo" accept=".png,.jpg,.jpeg,.svg,.webp" class="form-control">
            @if($kv['logo'])
              <div class="form-check mt-1">
                <input class="form-check-input" type="checkbox" name="remove_logo" id="remove_logo" value="1">
                <label class="form-check-label" for="remove_logo">Remove current logo</label>
              </div>
            @endif
          </div>

          {{-- Favicon --}}
          <div class="col-md-6">
            <label class="form-label d-flex align-items-center justify-content-between">
              <span>Favicon</span>
              @if($kv['favicon'])
                <span class="thumb"><img src="{{ asset($kv['favicon']) }}" alt="favicon"></span>
              @endif
            </label>
            <input type="file" name="favicon" accept=".ico,.png,.jpg,.jpeg,.webp" class="form-control">
            @if($kv['favicon'])
              <div class="form-check mt-1">
                <input class="form-check-input" type="checkbox" name="remove_favicon" id="remove_favicon" value="1">
                <label class="form-check-label" for="remove_favicon">Remove current favicon</label>
              </div>
            @endif
          </div>
        </div>

        <div class="mt-4">
          <button class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
