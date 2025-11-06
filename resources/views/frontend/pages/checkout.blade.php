@extends('frontend.layouts.app')

@section('title', 'Checkout - ' . $item->title)

@section('content')
<div class="checkout-hero" style="margin-top:70px">
  <div class="overlay"></div>
  <div class="container py-5 position-relative">
    <div class="row align-items-center text-center text-lg-start">
      <div class="col-lg-8 mx-auto">
        <h1 class="display-5 fw-bold text-white mb-3">
          {{ $type === 'course' ? 'Secure Course Checkout' : 'Secure Book Checkout' }}
        </h1>
        <p class="lead text-white-50 mb-0">
          Complete your purchase in seconds. Encrypted • Fast • Hassle-free
        </p>
      </div>
    </div>
  </div>
  <svg class="wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120"><path fill="#fff" d="M0,96L80,80C160,64,320,32,480,21.3C640,11,800,21,960,37.3C1120,53,1280,75,1360,85.3L1440,96L1440,0L1360,0C1280,0,1120,0,960,0C800,0,640,0,480,0C320,0,160,0,80,0L0,0Z"/></svg>
</div>

<div class="container pb-5" style="margin-top:-40px">
  <div class="row g-4">
    {{-- LEFT: Item details --}}
    <div class="col-lg-7">
      <div class="card glass shadow-xl border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center gap-3 mb-4">
            @php
                // Decide which field to use based on type
                if ($type === 'course') {
                    // Try typical course image fields in order
                    $path = $item->thumbnail
                        ?? $item->image
                        ?? $item->image_path
                        ?? null;
                } else { // 'book'
                    // Prefer the book cover, then fall back to any thumbnail
                    $path = $item->cover_path
                        ?? $item->thumbnail
                        ?? null;
                }

                // Build a final URL with asset(), with a fallback placeholder
                $imageUrl = $path
                    ? asset($path)
                    : asset('frontend/assets/images/placeholder-rect.jpg');
            @endphp

            <div class="thumb skeleton"
                style="width:84px;height:84px;border-radius:16px;
                        background-image:url('{{ $imageUrl }}');
                        background-size:cover;background-position:center;">
            </div>
            <div>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary fw-semibold">
                  <i class="fa fa-shield-alt me-1"></i> Verified {{ ucfirst($type) }}
                </span>
                @if($type === 'course')
                  <span class="badge px-3 py-2 rounded-pill bg-success-subtle text-success fw-semibold">
                    <i class="fa fa-signal me-1"></i> Lifetime Access
                  </span>
                @else
                  <span class="badge px-3 py-2 rounded-pill bg-success-subtle text-success fw-semibold">
                    <i class="fa fa-file-pdf me-1"></i> PDF Included
                  </span>
                @endif
              </div>
              <h3 class="fw-bold mt-3 mb-1">{{ $item->title }}</h3>
              <div class="text-muted small">{{ \Illuminate\Support\Str::limit($item->description, 140) }}</div>
            </div>
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

          @if($type === 'course')
            <div class="mt-4">
              <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill {{ $item->status ? 'bg-success' : 'bg-danger' }}">
                  {{ $item->status ? 'Available' : 'Inactive' }}
                </span>
                <span class="text-muted">Status</span>
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- Guarantee/Trust --}}
      {{-- <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex flex-column flex-md-row align-items-center gap-4">
            <div class="seal">
              <i class="fa fa-check"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-1">30-Day Satisfaction Guarantee</h5>
              <p class="mb-0 text-muted">
                If you’re not happy, we’re not happy. Reach out within 30 days for an easy refund.
              </p>
            </div>
          </div>
        </div>
      </div> --}}

    </div>

    {{-- RIGHT: Order summary / CTA (sticky) --}}
    <div class="col-lg-5">
      <div class="card glass shadow-xl border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex justify-content-between align-items-end mb-2">
            <span class="text-muted">Subtotal</span>
            <span class="fw-semibold">{{ number_format($item->price, 2) }} د.ك</span>
          </div>

          @if($promotion && $type === 'course')
            <div class="promotion-box d-flex justify-content-between align-items-start gap-3 mb-3 p-3 rounded-3 shadow-sm bg-success-subtle border border-success-subtle">
                <div class="d-flex align-items-start gap-2">
                <div class="promo-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                    <i class="fa fa-tags"></i>
                </div>
                <div>
                    <h6 class="mb-1 text-success fw-bold">{{ $promotion->title ?? 'Limited Offer' }}</h6>
                    <p class="mb-0 text-muted small">
                    {{ $promotion->description ?? 'A special discount has been applied to this course.' }}
                    </p>

                    @if(isset($promotion->discount_type))
                    <p class="mb-0 text-success small mt-1">
                        {{ $promotion->discount_value_type === 'percentage'
                            ? "{$promotion->discount_value}% off"
                            : number_format($promotion->discount_value, 2) . ' د.ك off' }}
                        {{ isset($promotion->end_date) ? "until " . \Carbon\Carbon::parse($promotion->end_date)->format('M d, Y') : '' }}
                    </p>
                    @endif
                </div>
                </div>
                <div class="text-end">
                <span class="text-success fw-semibold">− {{ number_format($item->price - $finalPrice, 2) }} د.ك</span>
                </div>
            </div>
            @endif

          {{-- <div class="d-flex justify-content-between align-items-end mb-3">
            <span class="text-muted">Fees</span>
            <span class="fw-semibold">0.00 د.ك</span>
          </div> --}}

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
              <input type="hidden" name="type" value="{{ $type }}">
              <input type="hidden" name="slug" value="{{ $item->slug }}">
              <button type="submit" class="btn btn-gradient w-100 btn-lg lift">
                <i class="fa fa-credit-card me-2"></i> Pay &amp; Access Now
              </button>
            </form>
          @else
            <button type="button" class="btn btn-gradient w-100 btn-lg lift mt-4"
                    data-bs-toggle="modal" data-bs-target="#loginModal">
              <i class="fa fa-sign-in-alt me-2"></i> Login to Continue
            </button>
          @endauth

          {{-- Trust badges --}}
          {{-- <div class="trust mt-4">
            <div class="d-flex flex-wrap justify-content-center gap-3">
              <div class="trust-badge"><i class="fa fa-lock"></i> SSL 256-bit</div>
              <div class="trust-badge"><i class="fa fa-shield-alt"></i> Fraud Protection</div>
              <div class="trust-badge"><i class="fa fa-history"></i> Easy Refunds</div>
            </div>
          </div> --}}

          {{-- Payment icons (illustrative; replace with your gateway logos if needed) --}}
          {{-- <div class="payment-logos mt-4">
            <i class="fab fa-cc-visa"></i>
            <i class="fab fa-cc-mastercard"></i>
            <i class="fab fa-cc-amex"></i>
            <i class="fab fa-cc-apple-pay"></i>
            <i class="fab fa-cc-paypal"></i>
          </div> --}}

          {{-- <p class="small text-muted text-center mt-3 mb-0">
            By completing your purchase, you agree to our <a href="#" class="link-underline">Terms</a> and <a href="#" class="link-underline">Privacy Policy</a>.
          </p> --}}
        </div>
      </div>
    </div>
  </div>
</div>

@include('frontend.pages.auth_modal', ['redirect' => url()->current()])

@endsection

@push('styles')
{{-- Bootstrap Icons / Font Awesome (only if not already loaded) --}}
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
  .checkout-hero .overlay{
    position:absolute; inset:0;
    background: linear-gradient(120deg, rgba(0,0,0,.15), rgba(0,0,0,.35));
  }
  .checkout-hero .wave{ display:block; margin-top:-2px; }

  .glass{
    background: var(--glass-bg) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--glass-brd) !important;
  }

  .promotion-box {
    transition: all 0.3s ease;
    }
    .promotion-box:hover {
    background-color: #e7f7ee !important;
    border-color: #16a34a !important;
    transform: translateY(-2px);
    }
    .promo-icon {
        box-shadow: 0 4px 10px rgba(22,163,74,0.25);
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

  .trust-badge{
    font-size:.92rem; background:#fff; border:1px solid var(--soft-brd); border-radius:999px;
    padding:.45rem .9rem; color:#263238;
    box-shadow:0 6px 16px rgba(21,26,48,.06);
  }
  .trust-badge i{ color:#0d6efd; margin-right:.4rem; }

  .payment-logos{ text-align:center; font-size:28px; color:#98a0b3; }
  .payment-logos i{ margin:0 .25rem; }

  /* Skeleton (quick load polish) */
  .skeleton{ position:relative; overflow:hidden; }
  .skeleton::after{
    content:""; position:absolute; inset:0;
    background:linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.35) 50%, rgba(255,255,255,0) 100%);
    transform:translateX(-100%); animation:sheen 1.8s infinite;
  }
  @keyframes sheen{ to{ transform:translateX(100%);} }

  /* RTL friendly */
  [dir="rtl"] .me-1{ margin-left:.25rem !important; margin-right:0 !important; }
  [dir="rtl"] .me-2{ margin-left:.5rem !important; margin-right:0 !important; }
</style>
@endpush
