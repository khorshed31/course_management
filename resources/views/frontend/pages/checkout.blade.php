@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="checkout-hero" style="margin-top:70px">
  <div class="overlay"></div>
  <div class="container py-5 position-relative">
    <div class="row align-items-center text-center text-lg-start">
      <div class="col-lg-8 mx-auto">
        <h1 class="display-5 fw-bold text-white mb-3">
          Secure Checkout
        </h1>
        <p class="lead text-white-50 mb-0">
          Review your cart and complete your purchase. Encrypted • Fast • Hassle-free
        </p>
      </div>
    </div>
  </div>
  <svg class="wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120"><path fill="#fff" d="M0,96L80,80C160,64,320,32,480,21.3C640,11,800,21,960,37.3C1120,53,1280,75,1360,85.3L1440,96L1440,0L1360,0C1280,0,1120,0,960,0C800,0,640,0,480,0C320,0,160,0,80,0L0,0Z"/></svg>
</div>

<div class="container pb-5" style="margin-top:-40px">
  <div class="row g-4">
    {{-- LEFT: Cart items --}}
    <div class="col-lg-7">
      <div class="card glass shadow-xl border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">

          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="seal">
              <i class="fa fa-shopping-cart"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">Your Cart</h3>
              <div class="text-muted small">Manage items, quantities, and discounts</div>
            </div>
          </div>

          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          @if(empty($items))
            <div class="p-4 text-center border rounded-4 bg-white">
              <h5 class="fw-bold mb-1">Your cart is empty</h5>
              <p class="text-muted mb-3">Add a course or book to continue.</p>
              <a href="{{ route('courses.list') }}" class="btn btn-gradient">
                <i class="fa fa-arrow-left me-2"></i> Continue Shopping
              </a>
            </div>
          @else
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th class="text-center">Type</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                @foreach($items as $key => $ci)
                  @php
                    $img = !empty($ci['image_path']) ? asset($ci['image_path']) : asset('frontend/assets/images/placeholder-rect.jpg');
                    $isCourse = ($ci['item_type'] ?? '') === 'course';
                    $hasDiscount = (float)($ci['final_price'] ?? 0) < (float)($ci['unit_price'] ?? 0);
                    $lineTotal = (float)$ci['final_price'];
                  @endphp
                  <tr>
                    <td class="text-center text-capitalize">{{ $ci['title'] }}</td>
                    <td class="text-center text-capitalize">{{ $ci['item_type'] }}</td>
                    <td class="text-end">
                      @if($hasDiscount)
                        <div class="text-muted small" style="text-decoration:line-through;">
                          {{ number_format($ci['unit_price'], 2) }} د.ك
                        </div>
                        <div class="fw-bold">{{ number_format($ci['final_price'], 2) }} د.ك</div>
                      @else
                        <div class="fw-bold">{{ number_format($ci['final_price'], 2) }} د.ك</div>
                      @endif
                    </td>

                    <td class="text-end fw-bold">
                      {{ number_format($lineTotal, 2) }} د.ك
                    </td>

                    <td class="text-end">
                      <form method="POST" action="{{ route('cart.remove') }}">
                        @csrf
                        <input type="hidden" name="key" value="{{ $key }}">
                        <button class="btn btn-sm btn-outline-danger" title="Remove">
                          <i class="fa fa-times"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>

            <hr class="soft my-4">
            <div class="row g-3">
              <div class="col-6 col-md-4">
                <div class="feature-tile">
                  <i class="fa fa-lock"></i>
                  <div class="label">Secure Payment</div>
                </div>
              </div>
              <div class="col-6 col-md-4">
                <div class="feature-tile">
                  <i class="fa fa-bolt"></i>
                  <div class="label">Instant Access</div>
                </div>
              </div>
              <div class="col-6 col-md-4">
                <div class="feature-tile">
                  <i class="fa fa-sync"></i>
                  <div class="label">Free Updates</div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
              <form method="POST" action="{{ route('cart.clear') }}">
                @csrf
                <button class="btn btn-outline-secondary">Clear Cart</button>
              </form>
              <a href="{{ route('courses.list') }}" class="btn btn-outline-primary">
                <i class="fa fa-arrow-left me-2"></i> Continue Shopping
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- RIGHT: Order summary / CTA --}}
    <div class="col-lg-5">
      <div class="card glass shadow-xl border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex justify-content-between align-items-end mb-2">
            <span class="text-muted">Subtotal</span>
            <span class="fw-semibold">{{ number_format($subtotal, 2) }} د.ك</span>
          </div>

          <hr class="soft">

          <div class="d-flex justify-content-between align-items-center">
            <span class="h5 mb-0 fw-bold">Total</span>
            <span class="display-6 price mb-0">
              {{ number_format($finalPrice, 2) }} <small class="text-muted">د.ك</small>
            </span>
          </div>

          @auth
            <form method="POST" action="{{ route('checkout.submit') }}" class="mt-4">
              @csrf
              <button type="submit" class="btn btn-gradient w-100 btn-lg lift" {{ empty($items) ? 'disabled' : '' }}>
                <i class="fa fa-credit-card me-2"></i> Pay &amp; Access Now
              </button>
            </form>
          @else
            <button type="button" class="btn btn-gradient w-100 btn-lg lift mt-4"
                    data-bs-toggle="modal" data-bs-target="#loginModal">
              <i class="fa fa-sign-in-alt me-2"></i> Login to Continue
            </button>
          @endauth
        </div>
      </div>
    </div>
  </div>
</div>

@include('frontend.pages.auth_modal', ['redirect' => url()->current()])

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
<style>
  :root{
    --g1:#4f8cff; --g2:#6dd5ed;
    --glass-bg:rgba(255,255,255,.6);
    --glass-brd:rgba(255,255,255,.35);
    --soft-brd:#e9eef8;
  }

  .checkout-hero{
    position:relative; overflow:hidden;
    background: radial-gradient(80% 80% at 20% 10%, #6dd5ed 0%, #4f8cff 50%, #2a3d7a 100%);
    min-height: 280px;
  }
  .checkout-hero .overlay{ position:absolute; inset:0; background: linear-gradient(120deg, rgba(0,0,0,.15), rgba(0,0,0,.35)); }
  .checkout-hero .wave{ display:block; margin-top:-2px; }

  .glass{
    background: var(--glass-bg) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--glass-brd) !important;
  }

  .soft{ border-color: var(--soft-brd); opacity:.7; }
  .thumb{ box-shadow: 0 8px 24px rgba(0,0,0,.08); }

  .feature-tile{
    background:#fff;border:1px solid var(--soft-brd);
    padding:14px;border-radius:14px;text-align:center;
    box-shadow:0 6px 16px rgba(21,26,48,.06);
  }
  .feature-tile i{ font-size:22px; margin-bottom:6px; color:#1f64ff; display:block; }
  .feature-tile .label{ font-weight:600; font-size:.95rem; }

  .seal{
    width:64px;height:64px;border-radius:50%;
    background:linear-gradient(135deg, var(--g1), var(--g2));
    display:grid;place-items:center;color:#fff;font-size:28px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
  }

  .btn-gradient{
    background: linear-gradient(135deg, var(--g2) 0%, var(--g1) 100%);
    border:0; color:#fff; padding:.9rem 1.1rem; border-radius:14px;
    box-shadow: 0 10px 26px rgba(79,140,255,.35);
    transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
  }
  .btn-gradient:hover{ transform: translateY(-2px); filter:brightness(.98); box-shadow:0 14px 30px rgba(79,140,255,.45); }
  .lift{ transition: transform .2s ease; }
  .lift:hover{ transform: translateY(-2px); }

  .price{ font-weight:800; letter-spacing:-.5px; }

  /* Skeleton */
  .skeleton{ position:relative; overflow:hidden; }
  .skeleton::after{
    content:""; position:absolute; inset:0;
    background:linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.35) 50%, rgba(255,255,255,0) 100%);
    transform:translateX(-100%); animation:sheen 1.8s infinite;
  }
  @keyframes sheen{ to{ transform:translateX(100%);} }

  .qty-input{ width:84px; }
  [dir="rtl"] .me-1{ margin-left:.25rem !important; margin-right:0 !important; }
  [dir="rtl"] .me-2{ margin-left:.5rem !important; margin-right:0 !important; }
</style>
@endpush
