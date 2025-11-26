@extends('frontend.layouts.app')

@section('title', 'Home — 3Shwe')

@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('frontend/assets/css/home.css') }}"> --}}
    <style>
        #coaching-apps { background: #fff; }
        .auto-video { width: 100%; height: 100%; object-fit: cover; border-radius:16px; display:block; }
        .video-wrap { position: relative; }
        .video-wrap .play-overlay {
          position:absolute; inset:0; display:grid; place-items:center;
          background: rgba(0,0,0,.15); border-radius:16px; cursor:pointer;
          opacity:0; transition:opacity .2s;
        }
        .video-wrap:hover .play-overlay { opacity:1; }

        #coaching-apps .accent { color: #d09b43; }
        #coaching-apps img { object-fit: cover; border-radius: 16px; }
        #coaching-apps .btn-outline-primary { border-color: #0d6efd; color: #0d6efd; transition: all 0.3s ease; }
        #coaching-apps .btn-outline-primary:hover { background-color: #0d6efd; color: #fff; }

        @media (max-width: 991px) {
            #coaching-apps .row.align-items-center { text-align: center; }
        }

        /* Toast (lightweight) */
        .toast-stack {
          position: fixed;
          right: 16px;
          bottom: 16px;
          z-index: 1080;
          display: grid;
          gap: 10px;
        }
        .h5header{
            color:#fff;
        }

        /* Sticky Review Order bar */
.order-review-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #111827; /* dark */
    padding: .55rem 0;
    z-index: 1070;
    box-shadow: 0 -4px 18px rgba(0,0,0,.35);
    font-size: 0.95rem;
    cursor: pointer;
}

.order-review-bar .quantity-pill {
    background: #030712;
    min-width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #fff;
}

.order-review-bar .review-label {
    color: #f9fafb;
    font-weight: 500;
}

.order-review-bar .old-price {
    font-size: 0.75rem;
    text-decoration: line-through;
    color: #9ca3af;
}

.order-review-bar .current-price {
    font-size: 0.95rem;
    font-weight: 600;
    color: #f9fafb;
}

        .toast-item {
          background: #111827;
          color: #fff;
          border-radius: 10px;
          padding: .75rem .9rem;
          box-shadow: 0 12px 28px rgba(0,0,0,.22);
          display: flex;
          align-items: center;
          gap: .55rem;
          min-width: 240px;
          max-width: min(92vw, 420px);
          animation: fadeIn .15s ease-out;
        }
        .toast-item.success { background: #065f46; }   /* green */
        .toast-item.error { background: #7f1d1d; }     /* red */
        .toast-item i { opacity: .9; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px);} to { opacity: 1; transform: translateY(0);} }
    </style>
@endpush

@section('content')
    {{-- Main Banner --}}
    <div class="main-banner" id="top">
        <video id="bg-video"
               autoplay
               muted
               playsinline
               webkit-playsinline
               loop
               preload="metadata"
               poster="{{ asset('frontend/assets/images/fallback-poster.jpg') }}">
          <source src="{{ asset('frontend/assets/images/gymm.mp4') }}" type="video/mp4" />
        </video>
        <div class="video-overlay header-text">
            <div class="caption">
                <h2>اصنع اقوى نسخه من نفسك</h2>
                <div class="h5header">
                  <h5> الموقع مبني على مبدأ واحد التغيير. فيه 3 جداول تمرين، كل جدول غير عن الثاني تماما ما في تكرار ما في روتين. كل ما خلصت جدول، تدخل بجدول ثاني يعطي عضلاتك تحدي جديد ويخلي جسمك يواصل نموه </h5>
                </div>
                <br>
                <div class="main-button scroll-to-section">
                    <a href="{{ route('login') }}">Book a call learn more</a>
                </div>
                <br>
                <div class="social-links text-center mt-3">
                    <a href="https://www.youtube.com/@v3shwe" target="_blank" class="me-3">
                        <img src="{{ asset('frontend/assets/images/youtube.png') }}" alt="" width="40">
                    </a>
                    <a href="https://www.snapchat.com/@ashwe9" target="_blank" class="me-3">
                        <img src="{{ asset('frontend/assets/images/snapchat.png') }}" alt="" width="30">
                    </a>
                    <a href="https://www.tiktok.com/@3shwe" target="_blank" class="me-3">
                        <img src="{{ asset('frontend/assets/images/tiktok.png') }}" alt="" width="40">
                    </a>
                    <a href="https://www.instagram.com/3shwe/" target="_blank" class="me-3">
                        <img src="{{ asset('frontend/assets/images/instagram.png') }}" alt="" width="30">
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Coaching & Apps Section ===== --}}
    <section class="section py-5" id="coaching-apps">
      <div class="container">
        {{-- Block 1 --}}
        <div class="row align-items-center g-5 mb-5">
          <div class="col-lg-6">
            <div class="video-wrap">
              <video class="auto-video"
                     autoplay muted playsinline webkit-playsinline loop
                     preload="metadata"
                     poster="{{ asset('frontend/assets/images/fallback-poster.jpg') }}">
                <source data-src="{{ asset('frontend/assets/images/table-1.mp4') }}" type="video/mp4">
              </video>
              <div class="play-overlay">
                <button class="btn btn-light btn-sm rounded-pill px-3">Tap to play</button>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <h3 class="fw-bold mb-3">الجدول الأول <span class="accent">– Standard</span></h3>
            <h5 class="text-muted mb-4"> جدول ممتاز نشتغل فيه على عضلتين باليوم، ومثالي لأي شخص أول مرة يبدأ
معاي متوازن، فعال، ويجهز جسمك للمرحلة اللي بعدها  </h5>
            <form method="POST" action="{{ route('cart.add') }}" class="d-inline ajax-add-to-cart"
                data-success-text="تمت الإضافة ✓" data-view-cart="{{ route('checkout.page') }}">
            @csrf
            <input type="hidden" name="type" value="course">
            <input type="hidden" name="slug" value="{{ $firstCourse->slug }}">

            @if(in_array($firstCourse->slug, array_column($cartItems, 'slug')))
                <a href="{{ route('checkout.page') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fa fa-shopping-cart"></i> عرض السلة
                </a>
            @else
                <button class="btn btn-outline-primary rounded-pill px-4" type="submit">
                    <i class="fa fa-shopping-cart"></i> أضف إلى السلة
                </button>
            @endif
          </form>
          </div>
        </div>

        {{-- Block 2 --}}
        <div class="row align-items-center g-5 mb-5">
          <div class="col-lg-6">
            <div class="video-wrap">
              <video class="auto-video"
                     autoplay muted playsinline webkit-playsinline loop
                     preload="metadata"
                     poster="{{ asset('frontend/assets/images/fallback-poster.jpg') }}">
                <source data-src="{{ asset('frontend/assets/images/table-2.mp4') }}" type="video/mp4">
              </video>
              <div class="play-overlay">
                <button class="btn btn-light btn-sm rounded-pill px-3">Tap to play</button>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <h3 class="fw-bold mb-3">الجدول الثاني <span class="accent">– Push Pull Legs</span></h3>
<h5 class="text-muted mb-4"> نرفع فيه مستوى الجهد ونركز أكثر على استهداف العضلات. مناسب للي يبي ينتقل من مرحلة التأسيس إلى تطوير فعلي في القوة والشكل 
</h5>            <form method="POST" action="{{ route('cart.add') }}" class="d-inline ajax-add-to-cart"
                  data-success-text="تمت الإضافة ✓" data-view-cart="{{ route('checkout.page') }}">
              @csrf
              <input type="hidden" name="type" value="course">
              <input type="hidden" name="slug" value="{{ $secondCourse->slug }}">

              @if(in_array($secondCourse->slug, array_column($cartItems, 'slug')))
                  <a href="{{ route('checkout.page') }}" class="btn btn-primary rounded-pill px-4">
                      <i class="fa fa-shopping-cart"></i> عرض السلة
                  </a>
              @else
                  <button class="btn btn-outline-primary rounded-pill px-4" type="submit">
                      <i class="fa fa-shopping-cart"></i> أضف إلى السلة
                  </button>
              @endif
            </form>
          </div>
        </div>

        {{-- Block 3 --}}
        <div class="row align-items-center g-5 mb-5">
          <div class="col-lg-6">
            <div class="video-wrap">
              <video class="auto-video"
                     autoplay muted playsinline webkit-playsinline loop
                     preload="metadata"
                     poster="{{ asset('frontend/assets/images/fallback-poster.jpg') }}">
                <source data-src="{{ asset('frontend/assets/images/table-3.mp4') }}" type="video/mp4">
              </video>
              <div class="play-overlay">
                <button class="btn btn-light btn-sm rounded-pill px-3">Tap to play</button>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <h3 class="fw-bold mb-3">الجدول الثالث <span class="accent">– 3shwe Style</span></h3>
            <h5 class="text-muted mb-4">  أقوى جدول كل عضلة نلعبها بروحها ، نركز فيها على الجهد العالي والتمارين
المختلفة بأسلوبي الخاص لتحقيق أقصى تفصيل ونتيجة  </h5>
            <form method="POST" action="{{ route('cart.add') }}" class="d-inline ajax-add-to-cart"
                  data-success-text="تمت الإضافة ✓" data-view-cart="{{ route('checkout.page') }}">
              @csrf
              <input type="hidden" name="type" value="course">
              <input type="hidden" name="slug" value="{{ $thirdCourse->slug }}">

              @if(in_array($thirdCourse->slug, array_column($cartItems, 'slug')))
                  <a href="{{ route('checkout.page') }}" class="btn btn-primary rounded-pill px-4">
                      <i class="fa fa-shopping-cart"></i> عرض السلة
                  </a>
              @else
                  <button class="btn btn-outline-primary rounded-pill px-4" type="submit">
                      <i class="fa fa-shopping-cart"></i> أضف إلى السلة
                  </button>
              @endif
            </form>
          </div>
        </div>
      </div>
    </section>

    {{-- ===== Gallery Section ===== --}}
    <div class="container text-center my-4">
      <h3 class="fw-bold mb-4">نتائج التزام الابطال بالجداول</h3>
      <div class="col-lg-12 order-lg-2">
        <div class="row">
          @foreach (['001','002','003','004','005','006','007','008','35','36','37','38'] as $img)
            <div class="col-6 col-lg-3 mb-3">
              <img src="{{ asset("frontend/assets/images/$img.jpeg") }}" class="img-fluid rounded-3 shadow-sm" alt="Result {{$img}}">
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ===== Featured Book Section ===== --}}
    @if (isset($featuredBook) && $featuredBook)
  <section class="section py-5" id="coaching-apps">
    <div class="container">
      <h3 class="fw-bold mb-4 text-center">اصنع أفضل نسخة من نفسك</h3>
      <h6 class="fw-bold mb-4 text-center">هذا مو كتاب… هذا مرآة...</h6>
      <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
          <img src="{{ asset($featuredBook->cover_path) }}" class="img-fluid rounded-4 shadow-sm" alt="{{ $featuredBook->title }}">
        </div>
        <div class="col-lg-6">
          <h3 class="fw-bold mb-3">{{ $featuredBook->title }}</h3>
          @if ($featuredBook->author)
            <p class="text-muted mb-1"><span class="accent">by </span> {{ $featuredBook->author }}</p>
          @endif
          <h3 class="mb-2"><span class="badge bg-primary price-badge">Price: د.ك{{ number_format($featuredBook->price, 2) }}</span></h3>
          <p class="text-muted mb-4">{{ \Illuminate\Support\Str::limit($featuredBook->description, 220) }}</p>

          @php
            $has = $featuredBook->purchases()->where('user_id', auth()->id())->where('status','paid')->exists();
          @endphp

          @if (in_array($featuredBook->slug, array_column($cartItems, 'slug')))
            <a href="{{ route('checkout.page') }}" class="btn btn-primary rounded-pill px-4">
              <i class="fa fa-shopping-cart"></i> عرض السلة
            </a>
          @else
            @unless ($has)
              <form method="POST" action="{{ route('cart.add') }}" class="d-inline ajax-add-to-cart"
                    data-success-text="تمت الإضافة ✓" data-view-cart="{{ route('checkout.page') }}">
                @csrf
                <input type="hidden" name="type" value="book">
                <input type="hidden" name="slug" value="{{ $featuredBook->slug }}">
                <button class="btn btn-outline-primary rounded-pill px-4" type="submit">
                  <i class="fa fa-shopping-cart"></i>  أضف إلى السلة
                </button>
              </form>
            @endunless
          @endif

          @if ($has)
            <button class="btn btn-primary rounded-pill px-4 preview-pdf-btn"
                    data-pdf="{{ route('books.preview', $featuredBook->slug) }}"
                    data-title="{{ $featuredBook->title }}"
                    data-bs-toggle="modal" data-bs-target="#pdfPreviewModal">Preview in Modal</button>
            <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('books.download', $featuredBook->slug) }}">Download PDF</a>
          @endif
        </div>
      </div>
    </div>
  </section>
@endif

    {{-- PDF Preview Modal --}}
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="pdfPreviewLabel">Preview</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-0" style="min-height:75vh">
            <embed id="nativePdfEmbed" src="" type="application/pdf" style="width:100%;height:75vh;border:0">
          </div>
          <div class="modal-footer">
            <a id="downloadPdfLink" href="#" class="btn btn-outline-secondary" target="_blank" rel="noopener">Open in new tab</a>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    {{-- Toast stack --}}
    <div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div>
    {{-- Sticky Review Order bar --}}
<div id="orderReviewBar" class="order-review-bar d-none">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="badge rounded-pill quantity-pill">
            <span id="orderReviewCount">0</span>
        </div>
        <div class="flex-grow-1 text-center">
            <span class="review-label">Review Order</span>
        </div>
        <div class="text-end">
            {{-- Optional old price if you ever need it --}}
            <div class="old-price" id="orderReviewOldTotal"></div>
            <div class="current-price" id="orderReviewTotal">0.000 د.ك</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const toastStack = document.getElementById('toastStack');

    const reviewBar   = document.getElementById('orderReviewBar');
    const reviewCount = document.getElementById('orderReviewCount');
    const reviewTotal = document.getElementById('orderReviewTotal');
    const reviewOld   = document.getElementById('orderReviewOldTotal');

    if (reviewBar) {
      reviewBar.addEventListener('click', function () {
          window.location.href = "{{ route('checkout.page') }}";
      });
  }

    function showToast(message, type = 'success') {
        if (!toastStack) return;
        const node = document.createElement('div');
        node.className = `toast-item ${type}`;
        node.innerHTML = `<i class="fa ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
        toastStack.appendChild(node);
        setTimeout(() => { node.remove(); }, 2800);
    }

    function swapToViewCart(form) {
        const viewUrl = form.getAttribute('data-view-cart');
        const successText = form.getAttribute('data-success-text') || 'تمت الإضافة ✓';
        const btn = form.querySelector('button[type="submit"]');

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fa fa-check"></i> ${successText}`;
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');
        }

        const link = document.createElement('a');
        link.href = viewUrl || '{{ route('checkout.page') }}';
        link.className = btn?.className || 'btn btn-primary rounded-pill px-4';
        link.innerHTML = `<i class="fa fa-shopping-cart"></i> عرض السلة`;

        setTimeout(() => {
            form.replaceWith(link);
        }, 400);
    }

    // NEW: update sticky review bar
    function updateReviewBar(json) {
        if (!reviewBar || !reviewCount || !reviewTotal) return;

        const count    = json.cart_count ?? 0;
        const total    = Number(json.cart_total ?? 0);
        const currency = json.currency || 'د.ك';

        reviewCount.textContent = count;
        reviewTotal.textContent = `${total.toFixed(3)} ${currency}`;

        if (count > 0) {
            reviewBar.classList.remove('d-none');
        } else {
            reviewBar.classList.add('d-none');
        }
    }

    updateReviewBar({
        cart_count: {{ (int) $cartCount }},
        cart_total: {{ (float) $cartTotal }},
        currency: @json($currency),
    });

    document.querySelectorAll('form.ajax-add-to-cart').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const url = form.getAttribute('action');
            const method = (form.getAttribute('method') || 'POST').toUpperCase();

            const btn = form.querySelector('button[type="submit"]');
            const oldHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<i class="fa fa-spinner fa-spin"></i> ...`;
            }

            fetch(url, {
                method: method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf || ''
                },
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(json => {
                if (json?.ok) {
                    showToast(json.message || 'تمت إضافة المنتج إلى السلة', 'success');
                    swapToViewCart(form);

                    // navbar counter
                    const counter = document.querySelector('.cart-count');
                    if (counter && typeof json.cart_count !== 'undefined') {
                        counter.textContent = json.cart_count;
                    }

                    // NEW: sticky bar
                    updateReviewBar(json);

                } else {
                    showToast(json?.message || 'لم يتم إضافة المنتج', 'error');
                    if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
                }
            })
            .catch(() => {
                showToast('حصل خطأ غير متوقع', 'error');
                if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
            });
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('video.auto-video source[data-src]').forEach(srcEl => {
        srcEl.setAttribute('src', srcEl.getAttribute('data-src'));
        srcEl.removeAttribute('data-src');

        const video = srcEl.closest('video');
        if (video) {
            video.load();
        }
    });
});
</script>
@endpush
