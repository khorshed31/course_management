@extends('frontend.layouts.app')

@section('title', $course->title)

@push('styles')
<style>
  body { background: linear-gradient(135deg, #6dd5ed 0%, #4f8cff 100%); min-height: 100vh; }

/* ===== SCOPED TO COURSE PAGE ONLY ===== */
#course-show{ --navH: 64px; } /* change if your sticky navbar height differs */

/* layout helpers */
#course-show .mt-under-sticky{ margin-top: calc(var(--navH) * .4); }
#course-show [id]{ scroll-margin-top: calc(var(--navH) + 12px); }

/* HERO */
#course-show .hero{
  position:relative;border-radius:18px;overflow:hidden;
  background:linear-gradient(135deg,#eaf6ff 0%,#f8fbff 100%);
  box-shadow:0 6px 28px rgba(30,144,255,.12)
}
#course-show .hero-media{aspect-ratio:16/9;overflow:hidden;background:#f0f4f8}
#course-show .hero-media img{width:100%;height:100%;object-fit:cover;display:block;filter:brightness(.98)}

/* badges / price */
#course-show .hero-badges{position:absolute;left:14px;top:14px;display:flex;gap:.5rem;flex-wrap:wrap}
#course-show .badge-live,
#course-show .badge-inactive{color:#fff;border-radius:8px;padding:.35rem .6rem;font-weight:700;font-size:.8rem;letter-spacing:.4px}
#course-show .badge-live{ background:#28a745 } .badge-inactive{ background:#dc3545 }

/* Price tag with discount */
#course-show .price-tag{ position:absolute; right:14px; top:14px; border-radius:10px;
  background:#ffffffee; color:#0b63b6; font-weight:800; padding:.5rem .75rem;
  box-shadow:0 2px 12px rgba(30,144,255,.18); display:flex; gap:.5rem; align-items:center }
#course-show .price-tag .old{ text-decoration:line-through; opacity:.7; font-weight:700; color:#64748b }
#course-show .price-tag .new{ background:rgba(30,144,255,.1); color:#0b63b6; padding:.2rem .45rem; border-radius:8px; font-weight:900 }
#course-show .price-tag .save{ background:linear-gradient(90deg,#ff7f50 0%,#ed563b 100%); color:#fff; padding:.2rem .45rem; border-radius:8px; font-weight:900; }
#course-show .price-free{ background:linear-gradient(90deg,#22c55e 60%,#16a34a 100%); color:#fff; }

/* Promo ribbon under title */
#course-show .promo-ribbon{ display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; margin-top:.5rem }
#course-show .promo-ribbon .label{
  background:linear-gradient(90deg,#ff7f50 0%,#ed563b 100%); color:#fff; font-weight:800;
  padding:.25rem .55rem; border-radius:8px; letter-spacing:.3px; font-size:.9rem; box-shadow:0 1px 4px rgba(237,86,59,.18)
}
#course-show .promo-ribbon .countdown{
  display:inline-flex; align-items:center; gap:.4rem; background:rgb(255 255 255 / 60%);
  color:#0b63b6; padding:.25rem .55rem; border-radius:8px; font-weight:800; font-size:.9rem
}

/* TITLE + META */
#course-show .title-wrap h1{ font-size:clamp(1.25rem, 2vw + 1rem, 1.9rem); font-weight:800; margin:0; color:#0f172a; line-height:1.25 }
#course-show .meta{color:#eef4ff;font-size:.92rem;display:flex;gap:1rem;flex-wrap:wrap}
#course-show .meta i{margin-right:.25rem}

/* CARDS */
#course-show .card{ border-radius:16px;border:1px solid #e5e7eb;background:#fff; box-shadow:0 2px 14px rgba(30,144,255,.06) }
#course-show .card .card-body{padding:1rem} @media (min-width:992px){ #course-show .card .card-body{padding:1.25rem} }

/* BUTTONS */
#course-show .main-button a, #course-show .main-button button{
  border-radius:12px;font-weight:800;padding:.85rem 1.1rem;
  background:linear-gradient(90deg,#1e90ff 60%,#1565c0 100%); color:#fff!important;border:none;
  display:inline-flex;align-items:center;gap:.5rem; box-shadow:0 3px 12px rgba(30,144,255,.18)
}
#course-show .main-button a:hover, #course-show .main-button button:hover{ filter:brightness(1.05) }

/* DESCRIPTION */
#course-show .desc{font-size:1rem;line-height:1.75;color:#334155} #course-show .desc img{max-width:100%;height:auto;border-radius:8px}

/* INFO LIST */
#course-show .info-list{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:1fr;gap:.55rem}
#course-show .info-list li{display:flex;align-items:center;gap:.6rem} #course-show .info-list i{color:#1e90ff}

/* RELATED */
#course-show .related .course-card{ border-radius:14px;border:1px solid #e3e6f0;background:#fff;overflow:hidden;height:100%; box-shadow:0 2px 12px rgba(30,144,255,.08);transition:transform .2s,box-shadow .2s }
#course-show .related .course-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(30,144,255,.18)}
#course-show .related .thumb{aspect-ratio:16/9;overflow:hidden;background:#f8fafc}
#course-show .related .thumb img{width:100%;height:100%;object-fit:cover}
#course-show .related .body{padding:.85rem}
#course-show .related h5{font-size:1rem;margin:0 0 .35rem;font-weight:700}
#course-show .related .price{color:#1e90ff;font-weight:800}

/* MOBILE sticky enroll bar */
#course-show .enroll-bar{ position:fixed; left:0; right:0; bottom:0; z-index:1040; background:#ffffffee; border-top:1px solid #e5e7eb; backdrop-filter:saturate(120%) blur(6px) }
#course-show .enroll-bar .inner{ display:flex; gap:.75rem; align-items:center; justify-content:space-between; padding:.6rem .9rem }
#course-show .enroll-bar .price{ font-weight:800; color:#1e90ff }
@media (min-width:576px){ #course-show .enroll-bar{ display:none } }

/* MOBILE tweaks */
@media (max-width:575.98px){
  #course-show .meta{gap:.65rem}
  #course-show .title-actions{ row-gap:.75rem }
  #course-show .main-button a, #course-show .main-button button{ width:100% }
}
</style>
@endpush

@section('content')
@php
  /** Pricing + promo **/
  $service = app(\App\Services\PromotionService::class);
  $rawPrice = is_numeric($course->price ?? null) ? (float)$course->price : 0.0;
  $promo = $service->bestForCourse($course->id);              // course-specific > site-wide
  $finalPrice = ($promo && $rawPrice > 0) ? $service->applyDiscount($rawPrice, $promo) : $rawPrice;
  $endsAt = $promo ? $service->endsAt($promo) : null;         // Carbon|null
  $currency = 'د.ك'; // adjust if needed
  $isInCart = auth()->check() && auth()->user()->carts()->where('course_id', $course->id)->exists();
@endphp

<section id="course-show" class="section py-4 mt-under-sticky">
  <div class="container" style="margin-top: 100px">

    {{-- Back --}}
    <div class="mb-3 d-flex align-items-center gap-2">
      <a href="{{ route('courses.list') }}" class="btn btn-outline-primary rounded-pill px-3 py-1 shadow-sm">
        <i class="fa fa-angle-left"></i> Back to Courses
      </a>
    </div>

    {{-- HERO --}}
    <div class="hero mb-3">
      <div class="hero-media">
        <img
          src="{{ $course->image ? asset($course->image) : asset('training-studio/assets/images/first-trainer.jpg') }}"
          alt="{{ e($course->title) }}"
          decoding="async"
          loading="eager"
        >
      </div>

      {{-- Status badge (optional) --}}
      {{-- <div class="hero-badges">
        @if(($course->status ?? 1))
          <span class="badge-live">Active</span>
        @else
          <span class="badge-inactive">Inactive</span>
        @endif
      </div> --}}

      {{-- PRICE TAG (shows discount if promo found) --}}
      <div class="price-tag {{ $rawPrice > 0 ? '' : 'price-free' }}">
        @if($rawPrice > 0)
          @if($promo && $finalPrice < $rawPrice)
            <span class="old">{{ number_format($rawPrice, 2) }} {{ $currency }}</span>
            <span class="new">{{ number_format($finalPrice, 2) }} {{ $currency }}</span>
            <span class="save">{{ $service->shortSaveText($promo, $currency) }}</span>
          @else
            <span class="new">{{ number_format($rawPrice, 2) }} {{ $currency }}</span>
          @endif
        @else
          FREE
        @endif
      </div>
    </div>

    {{-- TITLE + ACTIONS --}}
    <div class="row g-3 align-items-start mb-3 title-actions">
      <div class="col-lg-8">
        <div class="title-wrap">
          <h1>{{ $course->title }}</h1>

          {{-- Promo ribbon with title + countdown --}}
          @if($promo)
            <div class="promo-ribbon">
              <span class="label">
                {{ $promo->discount_type === 'special_day' && $promo->day_title ? $promo->day_title : $promo->label() }}
              </span>

              @if($endsAt && in_array($promo->discount_type, ['timer','special_day']))
                <span class="countdown" data-end="{{ $endsAt->copy()->timezone(config('app.timezone'))->toIso8601String() }}">
                  <i class="fa fa-clock-o" aria-hidden="true"></i>
                  <b class="cd-d">00</b>d
                  <b class="cd-h">00</b>h
                  <b class="cd-m">00</b>m
                  <b class="cd-s">00</b>s
                </span>
              @endif
            </div>
          @endif

          <div class="meta mt-2">
            <span><i class="fa fa-calendar"></i> Created {{ optional($course->created_at)->format('d M, Y') }}</span>
            <span><i class="fa fa-refresh"></i> Updated {{ optional($course->updated_at)->format('d M, Y') }}</span>
            <span>
              @if(($course->status ?? 1))
                <i class="fa fa-check-circle"></i> Available
              @else
                <i class="fa fa-ban"></i> Unavailable
              @endif
            </span>
          </div>
        </div>
      </div>

      <div class="col-lg-4 text-lg-end">
        <div class="main-button">
            @php $enrolled = $enrolled ?? false; $progress = $progress ?? null; @endphp

            @if($enrolled)
              <a href="{{ route('learn.course', $course->id) }}"><i class="fa fa-play-circle"></i> Continue Learning</a>
            @else
              <form method="GET" action="{{ route('checkout.page', ['type' => 'course', 'slug' => $course->slug]) }}">
                  @csrf
                  <button class="btn btn-primary" type="submit"><i class="fa fa-credit-card"></i> Buy Now</button>
                </form>
            @endif
        </div>

        @if(isset($progress) && $progress !== null)
          <div class="mt-2 text-muted" style="font-weight:600;">
            <i class="fa fa-line-chart"></i> Progress: {{ $progress }}%
          </div>
        @endif
      </div>
    </div>

    <div class="row g-3">
      {{-- DESCRIPTION --}}
      <div class="col-lg-8">
        <div class="card mb-3">
          <div class="card-body">
            <h4 class="mb-3" style="font-weight:800;">About this course</h4>
            <div class="desc">
              {!! $course->description ? $course->description : '<p>No description available.</p>' !!}
            </div>
          </div>
        </div>
      </div>

      {{-- SIDEBAR --}}
      <div class="col-lg-4">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="mb-3" style="font-weight:800;">Course info</h5>
            <ul class="info-list">
              <li><i class="fa fa-money"></i> <strong>Price:</strong>
                @if($rawPrice > 0)
                  @if($promo && $finalPrice < $rawPrice)
                    <span class="old">{{ number_format($rawPrice, 2) }} {{ $currency }}</span>
                    <span class="new" style="margin-left:.4rem">{{ number_format($finalPrice, 2) }} {{ $currency }}</span>
                    <span class="save" style="margin-left:.4rem">{{ $service->shortSaveText($promo, $currency) }}</span>
                  @else
                    {{ number_format($rawPrice, 2) }} {{ $currency }}
                  @endif
                @else
                  Free
                @endif
              </li>
              <li><i class="fa fa-calendar-o"></i> <strong>Created:</strong> {{ optional($course->created_at)->format('d M, Y') }}</li>
              <li><i class="fa fa-history"></i> <strong>Updated:</strong> {{ optional($course->updated_at)->format('d M, Y') }}</li>
            </ul>

            <div class="mt-3 main-button">
              @if($enrolled)
              <a href="{{ route('learn.course', $course->id) }}"><i class="fa fa-play-circle"></i> Continue Learning</a>
            @else
              <form method="GET" action="{{ route('checkout.page', ['type' => 'course', 'slug' => $course->slug]) }}">
                  @csrf
                  <button class="btn btn-primary" type="submit"><i class="fa fa-credit-card"></i> Buy Now</button>
                </form>
            @endif
            </div>
          </div>
        </div>

        {{-- Share --}}
        <div class="card">
          <div class="card-body">
            <h6 class="mb-2" style="font-weight:800;">Share</h6>
            @php $shareUrl = request()->fullUrl(); @endphp
            <div class="d-flex gap-2 flex-wrap">
              <a class="btn btn-sm btn-light" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"><i class="fa fa-facebook"></i> Facebook</a>
              <a class="btn btn-sm btn-light" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($course->title) }}"><i class="fa fa-twitter"></i> Twitter/X</a>
              <a class="btn btn-sm btn-light" target="_blank" rel="noopener" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}"><i class="fa fa-linkedin"></i> LinkedIn</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- RELATED (optional; pass $relatedCourses) --}}
    @isset($relatedCourses)
      @if($relatedCourses->count())
        <div class="related mt-4">
          <h4 class="mb-3" style="font-weight:800;">You might also like</h4>
          <div class="row g-3">
            @foreach($relatedCourses as $rc)
              @php
                $rimg = $rc->image ? asset($rc->image) : asset('training-studio/assets/images/first-trainer.jpg');
                $rprice = is_numeric($rc->price ?? null) ? (float)$rc->price : 0;
              @endphp
              <div class="col-12 col-sm-6 col-lg-4 d-flex">
                <div class="course-card related flex-grow-1">
                  <div class="thumb">
                    <a href="{{ route('courses.show', $rc->slug) }}">
                      <img src="{{ $rimg }}" alt="{{ e($rc->title) }}" loading="lazy" decoding="async">
                    </a>
                  </div>
                  <div class="body">
                    <h5><a href="{{ route('courses.show', $rc->slug) }}" class="text-decoration-none text-dark">{{ $rc->title }}</a></h5>
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="price">
                        @if($rprice>0) {{ number_format($rprice,2) }} {{ $currency }} @else Free @endif
                      </div>
                      <a href="{{ route('courses.show', $rc->slug) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    @endisset

  </div>

  {{-- MOBILE STICKY ENROLL BAR --}}
  @php $userEnrolled = $enrolled ?? false; @endphp
  @auth
    @if(!$userEnrolled)
      <div class="enroll-bar">
        <div class="inner container">
          <div class="d-flex align-items-center gap-2">
            <strong>{{ $course->title }}</strong>
            <span class="price">
              @if($rawPrice > 0)
                @if($promo && $finalPrice < $rawPrice)
                  {{ number_format($finalPrice, 2) }} {{ $currency }}
                @else
                  {{ number_format($rawPrice, 2) }} {{ $currency }}
                @endif
              @else
                Free
              @endif
            </span>
          </div>
          @if($enrolled)
              <a href="{{ route('learn.course', $course->id) }}"><i class="fa fa-play-circle"></i> Continue Learning</a>
            @else
              <form method="GET" action="{{ route('checkout.page', ['type' => 'course', 'slug' => $course->slug]) }}">
                  @csrf
                  <button class="btn btn-primary" type="submit"><i class="fa fa-credit-card"></i> Buy Now</button>
                </form>
            @endif
        </div>
      </div>
    @endif
  @else
    <div class="enroll-bar">
      <div class="inner container">
        <div class="d-flex align-items-center gap-2">
          <strong>{{ $course->title }}</strong>
          <span class="price">
            @if($rawPrice > 0)
              @if($promo && $finalPrice < $rawPrice)
                {{ number_format($finalPrice, 0) }} {{ $currency }}
              @else
                {{ number_format($rawPrice, 0) }} {{ $currency }}
              @endif
            @else
              Free
            @endif
          </span>
        </div>
        @if($enrolled)
              <a href="{{ route('learn.course', $course->id) }}"><i class="fa fa-play-circle"></i> Continue Learning</a>
            @else
              <form method="GET" action="{{ route('checkout.page', ['type' => 'course', 'slug' => $course->slug]) }}">
                  @csrf
                  <button class="btn btn-primary" type="submit"><i class="fa fa-credit-card"></i> Buy Now</button>
                </form>
            @endif
      </div>
    </div>
  @endauth
</section>
@endsection

@push('scripts')
<script>
/* One countdown for course page (title ribbon). Works for timer/special_day. */
(function(){
  var node = document.querySelector('#course-show .promo-ribbon .countdown[data-end]');
  if (!node) return;

  function pad(n){ return String(n).padStart(2,'0'); }
  var endISO = node.getAttribute('data-end');
  var end = endISO ? new Date(endISO) : null;
  if (!end) return;

  function tick(){
    var now = new Date();
    var diff = Math.max(0, end - now);
    var sec  = Math.floor(diff / 1000);

    var d = Math.floor(sec / 86400);
    var h = Math.floor((sec % 86400) / 3600);
    var m = Math.floor((sec % 3600) / 60);
    var s = sec % 60;

    node.querySelector('.cd-d').textContent = pad(d);
    node.querySelector('.cd-h').textContent = pad(h);
    node.querySelector('.cd-m').textContent = pad(m);
    node.querySelector('.cd-s').textContent = pad(s);

    if (diff === 0) {
      // Hide the ribbon when expired (optional)
      var ribbon = node.closest('.promo-ribbon');
      if (ribbon) ribbon.remove();
      clearInterval(timer);
    }
  }

  tick();
  var timer = setInterval(tick, 1000);
})();
</script>
@endpush
