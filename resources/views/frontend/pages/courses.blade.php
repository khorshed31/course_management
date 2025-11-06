@extends('frontend.layouts.app')

@section('title', 'All Courses')

@push('styles')
<style>
  body {
    background: linear-gradient(135deg, #4f8cff 0%, #6dd5ed 100%);
    min-height: 100vh;
  }

/* ====== SCOPED: Courses List Page Only ====== */
#courses-page .course-card{
  border-radius:18px;border:1px solid #e3e6f0;
  background:linear-gradient(135deg,#f8fbff 0%,#eaf6ff 100%);
  overflow:hidden;display:flex;flex-direction:column;height:100%;
  box-shadow:0 4px 24px rgba(30,144,255,.12);
  transition:box-shadow .2s,border-color .2s,transform .2s;position:relative
}
#courses-page .course-card:hover{box-shadow:0 8px 32px rgba(30,144,255,.22);border-color:#1e90ff;transform:translateY(-4px) scale(1.02)}
#courses-page .course-card .image-thumb{aspect-ratio:16/9;overflow:hidden;background:linear-gradient(90deg,#e3f0ff 0%,#f8fbff 100%);position:relative}
#courses-page .course-card .image-thumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s,filter .3s;filter:brightness(.97) saturate(1.1)}
#courses-page .course-card:hover .image-thumb img{transform:scale(1.07) rotate(-1deg);filter:brightness(1) saturate(1.2)}
#courses-page .course-card .down-content{flex:1 1 auto;display:flex;flex-direction:column;padding:1.25rem 1rem 1rem;background:rgba(255,255,255,.95)}
#courses-page .course-card h4 a{color:#212529;text-decoration:none;transition:color .2s;font-size:1.18rem;font-weight:700;letter-spacing:.5px}
#courses-page .course-card h4 a:hover{color:#1e90ff;text-decoration:underline}
#courses-page .status{font-size:.85rem;font-weight:600;color:#28a745;margin-bottom:.5rem;letter-spacing:.5px;text-transform:uppercase}
#courses-page .status.inactive{color:#dc3545}
#courses-page .price{font-weight:700;font-size:1.1rem;color:#1e90ff;background:rgba(30,144,255,.07);padding:.3rem .7rem;border-radius:6px;box-shadow:0 1px 4px rgba(30,144,255,.07)}
#courses-page .badge-free{background:linear-gradient(90deg,#1e90ff 60%,#1565c0 100%);color:#fff;padding:.25rem .7rem;border-radius:6px;font-size:.9rem;font-weight:700;letter-spacing:.5px;box-shadow:0 1px 4px rgba(30,144,255,.12)}
#courses-page .progress-text{font-size:.85rem;color:#888;margin-bottom:.35rem;font-weight:500;letter-spacing:.5px}

/* Buttons mapped to theme's main-button */
#courses-page .main-button a,#courses-page .main-button button.btn{
  border-radius:10px;font-weight:700;padding:.7rem 1.1rem;
  background:linear-gradient(90deg,#1e90ff 60%,#1565c0 100%);color:#fff!important;border:none;
  transition:background .2s,box-shadow .2s;box-shadow:0 2px 8px rgba(30,144,255,.14);
  font-size:1rem;letter-spacing:.5px;text-shadow:0 1px 2px rgba(30,144,255,.08);display:inline-flex;align-items:center;gap:.5em
}
#courses-page .main-button a:hover,#courses-page .main-button button.btn:hover{background:linear-gradient(90deg,#1565c0 60%,#1e90ff 100%);color:#0d0d0d!important;box-shadow:0 4px 16px rgba(30,144,255,.18);text-decoration:none}

/* Filters */
#courses-page .filters-wrap{background:linear-gradient(90deg,#f8fbff 0%,#eaf6ff 100%);border-radius:14px;box-shadow:0 2px 12px rgba(30,144,255,.08);padding:1rem 1.25rem;margin-bottom:.5rem}
#courses-page .filter-grid{display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:1rem;align-items:start}
#courses-page .filter-col{margin-bottom:0}
#courses-page .filter-row{display:flex;align-items:center;gap:.75rem;padding:.55rem 0;border-bottom:1px dashed rgba(0,0,0,.07);background:transparent}
#courses-page .filter-row:last-child{border-bottom:none}
#courses-page .filter-icon{width:38px;min-width:38px;height:38px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#eef6ff 0%,#dbefff 100%);color:#1e90ff;font-size:18px;box-shadow:0 1px 4px rgba(30,144,255,.07)}
#courses-page .filter-actions{display:flex;gap:.5rem}
#courses-page .filter-actions .btn{padding:.7rem 1.1rem;font-weight:700;border-radius:10px;font-size:1rem}

/* Section heading (scoped) */
#courses-page .section-heading h2{
  font-size:2.2rem;font-weight:800;letter-spacing:1px;margin-bottom:.5rem;
  background:linear-gradient(90deg,#ed563b 60%,#ed563b 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-fill-color:transparent
}
#courses-page .section-heading em{font-style:normal;color:#212529;font-weight:700;background:none;-webkit-text-fill-color:initial;text-fill-color:initial}

/* Empty state */
#courses-page .empty-state{background:linear-gradient(135deg,#f8fbff 0%,#eaf6ff 100%);border-radius:18px;box-shadow:0 2px 16px rgba(30,144,255,.10);padding:3rem 2rem}

/* === Discount badges on card === */
#courses-page .badge-discounted{
  background: rgba(30,144,255,.09);
  color:#0b63b6;font-weight:800;padding:.25rem .5rem;border-radius:6px;
  box-shadow:0 1px 3px rgba(30,144,255,.08);
}
#courses-page .badge-save{
  background: linear-gradient(90deg,#ff7f50 0%,#ed563b 100%);
  color:#fff;font-weight:800;padding:.25rem .5rem;border-radius:6px;
  box-shadow:0 1px 4px rgba(237,86,59,.18);letter-spacing:.3px;font-size:.9rem;
}

/* === Promo chip: title + timer under price === */
#courses-page .promo-chip{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-top:.35rem}
#courses-page .promo-chip__label{
  background:linear-gradient(90deg,#ff7f50 0%,#ed563b 100%);
  color:#fff;font-weight:800;padding:.2rem .5rem;border-radius:6px;
  letter-spacing:.3px;font-size:.85rem;box-shadow:0 1px 4px rgba(237,86,59,.18);
}
#courses-page .promo-chip__countdown{
  display:inline-flex;align-items:center;gap:.35rem;
  background:rgba(30,144,255,.08);color:#0b63b6;
  padding:.2rem .5rem;border-radius:6px;font-weight:700;font-size:.85rem;
}
#courses-page .promo-chip__countdown i{opacity:.8}

/* Mobile */
@media (max-width:575.98px){
  #courses-page .section-heading h2{font-size:1.5rem}
  #courses-page .course-card .down-content{padding:.85rem}
  #courses-page .filter-grid{display:block}
  #courses-page .filter-col{width:100%;margin-bottom:.7rem}
  #courses-page .filter-actions{flex-direction:column}
  #courses-page .filter-actions .btn{width:100%}
  #courses-page .filters-wrap{padding:.85rem .7rem}
  #courses-page .row.g-4>[class*="col-"]{padding-left:.3rem;padding-right:.3rem}
  #courses-page .row.g-4{margin-left:-.3rem;margin-right:-.3rem}
  #courses-page .course-card{border-radius:14px}
  #courses-page .empty-state{padding:2rem 1rem}
}

/* Tablet */
@media (min-width:576px) and (max-width:991.98px){
  #courses-page .filter-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem 1rem;align-items:start}
  #courses-page .filter-col{margin-bottom:0}
}
</style>
@endpush

@section('content')
<section class="section py-4" id="courses-page">
  <div class="container">

    {{-- Title --}}
    <div class="row mb-3">
      <div class="col-lg-8 offset-lg-2">
        <div class="section-heading text-center">
          <h2 class="fw-bold mb-2">
            <span>All</span> <em>Courses</em>
          </h2>
        </div>
      </div>
    </div>

    {{-- Empty state / Grid --}}
    @if($courses->count() === 0)
      <div class="row">
        <div class="col-lg-8 offset-lg-2">
          <div class="text-center empty-state">
            <img src="{{ asset('training-studio/assets/images/line-dec.png') }}" class="mb-3" alt="">
            <h4 class="fw-bold mb-2">No courses found</h4>
            <p class="mb-3 text-muted">Try changing your search or filter options.</p>
            <div class="main-button">
              <a href="{{ route('courses.list') }}">Clear Filters</a>
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="row g-4">
        @foreach($courses as $course)
          @php
            $img      = $course->image ? asset($course->image) : asset('training-studio/assets/images/first-trainer.jpg');
            $active   = (bool) ($course->status ?? 1);
            $price    = is_numeric($course->price ?? null) ? (float)$course->price : 0.0;
            $enr      = $enrolledMap[$course->id] ?? null;
            $progress = $enr['progress_percent'] ?? null;

            /** Promo logic per course (course-specific > site-wide) */
            $service  = app(\App\Services\PromotionService::class);
            $promo    = $service->bestForCourse($course->id);
            $final    = ($promo && $price > 0) ? $service->applyDiscount($price, $promo) : $price;
            $endsAt   = $promo ? $service->endsAt($promo) : null;
            $isInCart = auth()->check() && auth()->user()->carts()->where('course_id', $course->id)->exists();
          @endphp

          <div class="col-12 col-sm-6 col-lg-4 d-flex">
            <div class="course-card flex-grow-1 d-flex flex-column">
              <div class="image-thumb">
                <a href="{{ route('courses.show', $course->slug) }}">
                  <img src="{{ $img }}" alt="{{ e($course->title) }}">
                </a>
              </div>

              <div class="down-content">
                <span class="status {{ $active ? '' : 'inactive' }}">{{ $active ? 'Available' : 'Inactive' }}</span>

                <h4 class="mb-2">
                  <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
                </h4>

                <p class="mb-3 text-muted" style="min-height:48px;">
                  {{ \Illuminate\Support\Str::limit(strip_tags($course->description), 120) }}
                </p>

                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 w-100">
                  <div class="price d-flex align-items-center gap-2">
                    @if($price > 0)
                      @if($promo && $final < $price)
                        <span class="text-muted" style="text-decoration:line-through;opacity:.8;">
                          {{ number_format($price, 2) }} &#x062F;&#x002E;&#x0643;
                        </span>
                        <span class="badge-discounted">
                          {{ number_format($final, 2) }} &#x062F;&#x002E;&#x0643;
                        </span>
                        <span class="badge-save">
                          {{ $service->shortSaveText($promo, 'د.ك') }}
                        </span>
                      @else
                        {{ number_format($price, 2) }} &#x062F;&#x002E;&#x0643;
                      @endif
                    @else
                      <span class="badge-free">Free</span>
                    @endif
                  </div>

                  <div>
                    @if($enr)
                        <div class="progress-text">Progress: {{ $progress }}%</div>
                        <div class="main-button" style="display:inline-block;">
                            <a href="{{ route('learn.course', $course->id) }}"><i class="fa fa-play-circle"></i> Continue</a>
                        </div>
                    @else
                    <form method="GET" action="{{ route('checkout.page', ['type' => 'course', 'slug' => $course->slug]) }}">
                      @csrf
                      <button class="btn btn-primary" type="submit"><i class="fa fa-credit-card"></i> Buy Now</button>
                    </form>
                    @endif
                </div>
            </div>

                {{-- Promo title + countdown (timer/special_day) --}}
                @if($promo)
                  <div class="promo-chip">
                    <span class="promo-chip__label">
                      {{ $promo->discount_type === 'special_day' && $promo->day_title ? $promo->day_title : $promo->label() }}
                    </span>

                    @if($endsAt && in_array($promo->discount_type, ['timer','special_day']))
                      <span class="promo-chip__countdown"
                            data-end="{{ $endsAt->copy()->timezone(config('app.timezone'))->toIso8601String() }}">
                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                        <b class="cd-d">00</b>d
                        <b class="cd-h">00</b>h
                        <b class="cd-m">00</b>m
                        <b class="cd-s">00</b>s
                      </span>
                    @endif
                  </div>
                @endif

              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="row mt-5">
        <div class="col-12 d-flex justify-content-center">
          {{ $courses->onEachSide(1)->links() }}
        </div>
      </div>
    @endif

  </div>
</section>
@endsection

@push('scripts')
<script>
/* Lightweight per-card countdown for all visible promos on this page */
(function(){
  var nodes = document.querySelectorAll('#courses-page .promo-chip__countdown[data-end]');
  if (!nodes.length) return;

  function pad(n){ return String(n).padStart(2,'0'); }

  function tickOne(node, end) {
    var now  = new Date();
    var diff = Math.max(0, end - now);
    var sec  = Math.floor(diff / 1000);

    var d = Math.floor(sec / 86400);
    var h = Math.floor((sec % 86400) / 3600);
    var m = Math.floor((sec % 3600) / 60);
    var s = sec % 60;

    var dEl = node.querySelector('.cd-d'), hEl = node.querySelector('.cd-h'),
        mEl = node.querySelector('.cd-m'), sEl = node.querySelector('.cd-s');

    if (dEl) dEl.textContent = pad(d);
    if (hEl) hEl.textContent = pad(h);
    if (mEl) mEl.textContent = pad(m);
    if (sEl) sEl.textContent = pad(s);

    if (diff === 0) {
      // Remove the chip when expired (optional)
      var chip = node.closest('.promo-chip');
      if (chip) chip.remove();
    }
  }

  var items = [];
  nodes.forEach(function(node){
    var endISO = node.getAttribute('data-end');
    var end = endISO ? new Date(endISO) : null;
    if (!end) return;
    items.push({ node: node, end: end });
    tickOne(node, end);
  });

  if (!items.length) return;
  setInterval(function(){ items.forEach(function(it){ tickOne(it.node, it.end); }); }, 1000);
})();
</script>
@endpush
