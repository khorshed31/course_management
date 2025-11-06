@extends('frontend.layouts.app')

@section('title', 'Home — 3Shwe')

{{-- Optional: page-specific CSS --}}
@push('styles')
    {{--
  <link rel="stylesheet" href="{{ asset('frontend/assets/css/home.css') }}"> --}}
    <style>
        #coaching-apps {
            background: #fff;
        }
        
        .auto-video { width: 100%; height: 100%; object-fit: cover; border-radius:16px; display:block; }
        .video-wrap { position: relative; }
.video-wrap .play-overlay {
  position:absolute; inset:0; display:grid; place-items:center;
  background: rgba(0,0,0,.15); border-radius:16px; cursor:pointer;
  opacity:0; transition:opacity .2s;
}
.video-wrap:hover .play-overlay { opacity:1; }

        #coaching-apps .accent {
            color: #d09b43;
            /* gold accent */
        }

        #coaching-apps img {
            object-fit: cover;
            border-radius: 16px;
        }

        #coaching-apps .btn-outline-primary {
            border-color: #0d6efd;
            color: #0d6efd;
            transition: all 0.3s ease;
        }

        #coaching-apps .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }

        @media (max-width: 991px) {
            #coaching-apps .row.align-items-center {
                text-align: center;
            }
        }

        #contact-section .card-demo {
            background: #f3f6fb;
            box-shadow: 0 10px 30px rgba(21, 26, 48, 0.06);
            border: 1px solid #e9eef8;
        }

        #contact-section .accent {
            color: #1f64ff;
        }

        #contact-section .note-box {
            background: #ffffff;
            border: 1px solid #e9eef8;
            box-shadow: 0 6px 16px rgba(21, 26, 48, .05);
        }

        #contact-section .form-card {
            border: 1px solid #edf1fb;
            box-shadow: 0 12px 28px rgba(21, 26, 48, 0.06);
        }

        #contact-section .soft-input {
            background: #f7f9ff;
            border: 1px solid #e4e9f7;
            border-radius: 12px;
            padding: .7rem .9rem;
            transition: box-shadow .2s, border-color .2s;
        }

        #contact-section .soft-input::placeholder {
            color: #aab3c4;
        }

        #contact-section .soft-input:focus {
            border-color: #1f64ff;
            box-shadow: 0 0 0 .25rem rgba(31, 100, 255, .12);
        }

        #contact-section .btn-primary {
            background: #1f64ff;
            border-color: #1f64ff;
        }

        #contact-section .btn-primary:hover {
            filter: brightness(0.95);
        }
        .py-5 {
       
       padding-bottom: 0rem !important;
        }
        .h5header{
            color:white;
        }
        .social-links a {
    transition: transform 0.3s;
}

.social-links a:hover {
    transform: scale(1.2);
}

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
               <h5> الموقع مبني على مبدأ واحد: التغيير. فيه 3 جداول تمرين، كل جدول غير عن الثاني تماماً. ما في تكرار، ما في روتين. كل ما خلصت جدول، تدخل بجدول ثاني يعطي عضلاتك تحدي جديد ويخلي جسمك يواصل نموه.
 </h5>
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
              {{-- lazy-load via data-src --}}
              <source data-src="{{ asset('frontend/assets/images/table-1.mp4') }}" type="video/mp4">
            </video>
            <div class="play-overlay">
              <button class="btn btn-light btn-sm rounded-pill px-3">Tap to play</button>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <h3 class="fw-bold mb-3">الجدول الأول <span class="accent">– Standard</span></h3>
          <h5 class="text-muted mb-4">جدول ممتاز نشتغل فيه على عضلتين باليوم...</h5>
          <form method="GET" action="{{ route('checkout.page', ['type' => 'course', 'slug' => $firstCourse->slug]) }}">
            <button class="btn btn-outline-primary rounded-pill px-4" type="submit">
              <i class="fa fa-credit-card"></i> اشتري الآن
            </button>
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
          <h5 class="text-muted mb-4">نرفع فيه مستوى الجهد...</h5>
          <form method="GET" action="{{ route('checkout.page', ['type' => 'course', 'slug' => $secondCourse->slug]) }}">
            <button class="btn btn-outline-primary rounded-pill px-4" type="submit">
              <i class="fa fa-credit-card"></i> اشتري الآن
            </button>
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
          <h5 class="text-muted mb-4">أقوى جدول...</h5>
          <form method="GET" action="{{ route('checkout.page', ['type' => 'course', 'slug' => $thirdCourse->slug]) }}">
            <button class="btn btn-outline-primary rounded-pill px-4" type="submit">
              <i class="fa fa-credit-card"></i> اشتري الآن
            </button>
          </form>
        </div>
      </div>

    </div>
  </section>

  {{-- ===== Gallery (unchanged) ===== --}}
  <div class="container text-center my-4">
    <h3 class="fw-bold mb-4">نتائج التزام الابطال بالجداول</h3>
    <div class="col-lg-12 order-lg-2">
      <div class="row">
        @foreach (['001','002','003','004','005','006','007','008'] as $img)
          <div class="col-6 col-lg-3 mb-3">
            <img src="{{ asset("frontend/assets/images/$img.jpeg") }}" class="img-fluid rounded-3 shadow-sm" alt="Result {{$img}}">
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ===== Featured Book (optional) ===== --}}
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
            @unless ($has)
              <form method="GET" action="{{ route('checkout.page', ['type'=>'book','slug'=>$featuredBook->slug]) }}" class="d-inline">
                <button class="btn btn-outline-primary rounded-pill px-4" type="submit">
                  <i class="fa fa-credit-card"></i>  اشتري الآن
                </button>
              </form>
            @endunless
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const videos = Array.from(document.querySelectorAll('video'));

  // Respect "reduce motion"
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    videos.forEach(v => { try { v.pause(); } catch(e){} });
    return;
  }

  // Ensure autoplay flags (attributes + properties)
  const ensureFlags = (v) => {
    v.setAttribute('autoplay', '');
    v.setAttribute('muted', '');
    v.setAttribute('playsinline', '');
    v.setAttribute('webkit-playsinline', '');
    v.muted = true;
    v.defaultMuted = true;     // critical for iOS
    v.playsInline = true;      // property mirror
  };

  // Lazy-assign real src when visible (for section videos only)
  const lazyLoadVideo = (v) => {
    const s = v.querySelector('source');
    if (s && s.dataset.src && !s.src) {
      s.src = s.dataset.src;
      v.load();
    }
  };

  // Attempt to play with retries
  const attemptPlay = (v, tries = 6) => {
    if (!v) return;
    ensureFlags(v);
    if (!v.paused && !v.ended) return;
    const p = v.play?.();
    if (p && typeof p.then === 'function') {
      p.then(() => {}).catch(() => {
        if (tries > 0) setTimeout(() => attemptPlay(v, tries - 1), 250);
      });
    }
  };

  // Overlay wiring (tap to play fallback)
  document.querySelectorAll('.video-wrap').forEach(wrap => {
    const v = wrap.querySelector('video');
    const overlay = wrap.querySelector('.play-overlay');
    if (!v || !overlay) return;

    const syncOverlay = () => { overlay.style.display = (!v.paused && !v.ended) ? 'none' : ''; };
    v.addEventListener('playing', syncOverlay);
    v.addEventListener('pause',   syncOverlay);
    overlay.addEventListener('click', () => {
      ensureFlags(v);
      lazyLoadVideo(v); // harmless for hero (already has src)
      v.play().catch(() => {});
    });
    requestAnimationFrame(syncOverlay);
  });

  // Observe visibility for lazy load + play/pause
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      const v = entry.target;
      if (entry.isIntersecting && entry.intersectionRatio > 0.2) {
        lazyLoadVideo(v);
        attemptPlay(v);
      } else {
        try { v.pause(); } catch(e){}
      }
    });
  }, { threshold: [0, 0.2, 0.5, 1] });

  videos.forEach(v => {
    ensureFlags(v);
    requestAnimationFrame(() => attemptPlay(v)); // immediate try if visible

    // Extra retries when ready
    v.addEventListener('loadedmetadata', () => attemptPlay(v), { once: true });
    v.addEventListener('canplay',        () => attemptPlay(v), { once: true });
    v.addEventListener('canplaythrough', () => attemptPlay(v), { once: true });

    io.observe(v);
  });

  // User gesture fallback (covers strict autoplay / Low Power Mode)
  const resumeVisible = () => {
    videos.forEach(v => {
      const r = v.getBoundingClientRect();
      const inView = r.top < window.innerHeight && r.bottom > 0;
      if (inView) attemptPlay(v, 3);
    });
  };
  ['pointerdown','touchstart','keydown'].forEach(evt => {
    window.addEventListener(evt, resumeVisible, { once:true, passive: (evt==='touchstart') });
  });

  // Return from background
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') resumeVisible();
  });

  // PDF modal
  document.querySelectorAll('.preview-pdf-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const url = btn.getAttribute('data-pdf');
      const title = btn.getAttribute('data-title') || 'Preview';
      document.getElementById('pdfPreviewLabel').textContent = title;
      document.getElementById('nativePdfEmbed').src = url + '#toolbar=0';
      document.getElementById('downloadPdfLink').href = url;
    });
  });
  const modalEl = document.getElementById('pdfPreviewModal');
  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', function() {
      document.getElementById('nativePdfEmbed').src = '';
    });
  }
});
</script>

{{-- Optional: register a service worker if you already have /sw.js --}}
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/sw.js', { scope: '/' })
      .then(reg => {
        if ('requestIdleCallback' in window) {
          requestIdleCallback(() => navigator.serviceWorker.controller && reg.update());
        } else {
          setTimeout(() => navigator.serviceWorker.controller && reg.update(), 2000);
        }
      })
      .catch(console.error);
  });
}
</script>
@endpush