<style>
/* ===============================
   STICKY STACK (Promo + Navbar)
   =============================== */
.sticky-stack{
  position: sticky;
  top: 0;
  z-index: 1040;           /* above content, below modals */
  background: transparent; /* let child backgrounds show */
}

/* Kill any accidental spacing between promo and navbar */
.sticky-stack > *{
  margin-top: 0 !important;
}

/* Your theme often sets header fixed; we reset it so the wrapper handles stickiness */
.header-area.header-sticky{
  position: relative !important;
  top: auto !important;
  margin: 0 !important;
}

/* Some themes add margins/paddings on grid wrappers; flatten them */
.header-area,
.header-area .container,
.header-area .row,
.header-area .col-12,
.main-nav{
  margin-top: 0 !important;
  padding-top: 0 !important;
}

/* Optional: solid/semi-opaque bg for navbar so content behind doesn't peek through */
.header-area{
  background: rgba(25, 0, 47, 0.85); /* adjust or remove if you want transparent */
}

/* ===============================
   PROMO BAR STYLES
   =============================== */
.promo-bar{
  width: 100%;
  background: #49007a; /* deep purple */
  color: #fff;
  font-weight: 500;
  position: relative;   /* not fixed; wrapper is sticky */
  z-index: 1;
  padding: 14px 0;
  box-sizing: border-box;
  margin: 0;            /* ensure no gap above/below */
}

.promo-bar .promo-bar__inner{
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 18px;
  flex-wrap: wrap;
  text-align: center;
}

/* Badge on the left (Early Bird / Limited Time, etc.) */
.promo-badge{
  background: #ff5a66;
  color: #fff;
  font-weight: 700;
  padding: 8px 16px;
  border-radius: 10px;
}

/* “Discount SAVE … is expiring in:” text */
.promo-text{
  font-size: 16px;
  opacity: 0.95;
}

/* Highlighted SAVE chip */
.promo-save{
  background: #ff6f7a;
  color: #fff;
  font-weight: 800;
  padding: 8px 16px;
  border-radius: 10px;
  outline: 2px dashed #ffd24f;
  outline-offset: -4px;
  margin: 0 6px;
  display: inline-block;
}

/* Countdown boxes */
.promo-countdown{
  display: inline-flex;
  gap: 10px;
  align-items: center;
}
.promo-countdown .cd-box{
  background: #fff;
  color: #3a005f;
  border-radius: 10px;
  padding: 6px 10px;
  text-align: center;
  min-width: 54px;
}
.promo-countdown b{
  display: block;
  font-size: 18px;
  line-height: 1;
}
.promo-countdown small{
  font-size: 12px;
  opacity: 0.8;
}

/* CTA button */
.promo-cta{
  background: #ffbf00;
  color: #000;
  font-weight: 800;
  padding: 10px 18px;
  border-radius: 10px;
  text-decoration: none;
  transition: filter .2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.promo-cta:hover{ filter: brightness(.92); }

/* ===============================
   RESPONSIVE TWEAKS
   =============================== */
@media (max-width: 992px){
  .promo-text{ font-size: 15px; }
}
@media (max-width: 768px){
  .promo-text{ font-size: 14px; }
  .promo-countdown .cd-box{ padding: 4px 8px; min-width: 48px; }
}
@media (max-width: 480px){
  .promo-bar{ padding: 12px 0; }
  .promo-badge,
  .promo-save{ padding: 6px 12px; border-radius: 8px; }
  .promo-cta{ padding: 8px 14px; border-radius: 8px; }
}

/* ===============================
   RTL SAFETY (if needed)
   =============================== */
[dir="rtl"] .promo-bar .promo-bar__inner{
  direction: rtl;
}


</style>

@php
    /** @var \App\Models\Promotion|null $promoBanner */
    /** @var \Carbon\Carbon|null $promoEndsAt */
    $service = app(\App\Services\PromotionService::class);
    $currency = "د.ك" // set this in config if you like
@endphp
@if($promoBanner)
<div id="promoBar"
     class="promo-bar">
    <div class="container promo-bar__inner">
        <span class="promo-badge">{{ $promoBanner->label() }}</span>

        <span class="promo-text">
            Discount <span class="promo-save">{{ $service->saveText($promoBanner, $currency) }}</span>
            @if($promoEndsAt) <span>is expiring in:</span> @endif
        </span>

        @if($promoEndsAt)
            <div class="promo-countdown"
                 data-end="{{ $promoEndsAt->copy()->timezone(config('app.timezone'))->toIso8601String() }}">
                <span class="cd-box"><b class="cd-days">00</b> <small>Days</small></span>
                <span class="cd-box"><b class="cd-hours">00</b> <small>Hours</small></span>
                <span class="cd-box"><b class="cd-mins">00</b> <small>Minutes</small></span>
                <span class="cd-box"><b class="cd-secs">00</b> <small>Seconds</small></span>
            </div>
        @endif

        <a class="promo-cta" href="{{ url('/courses') }}">BUY NOW</a>
    </div>
</div>
@endif

<script>
(function () {
  // Positioning helper: set --promo-h to real height
  var bar = document.getElementById('promoBar');
  if (bar) {
    function setH(){ document.documentElement.style.setProperty('--promo-h', bar.offsetHeight + 'px'); }
    setH(); window.addEventListener('resize', setH);
  }

  // Countdown
  var root = document.querySelector('.promo-countdown');
  if (!root) return;

  var endISO = root.getAttribute('data-end');
  var end = endISO ? new Date(endISO) : null;
  if (!end) return;

  var els = {
    d: root.querySelector('.cd-days'),
    h: root.querySelector('.cd-hours'),
    m: root.querySelector('.cd-mins'),
    s: root.querySelector('.cd-secs'),
  };

  function pad(n){ return String(n).padStart(2,'0'); }

  function tick() {
    var now = new Date();
    var diff = Math.max(0, end - now);
    if (diff === 0) {
      // hide on expiry
      var barEl = document.getElementById('promoBar');
      if (barEl) barEl.style.display = 'none';
      clearInterval(timer);
      return;
    }
    var sec = Math.floor(diff / 1000);
    var days = Math.floor(sec / 86400);
    var hours = Math.floor((sec % 86400) / 3600);
    var mins = Math.floor((sec % 3600) / 60);
    var secs = sec % 60;

    els.d.textContent = pad(days);
    els.h.textContent = pad(hours);
    els.m.textContent = pad(mins);
    els.s.textContent = pad(secs);
  }

  tick();
  var timer = setInterval(tick, 1000);
})();


</script>
