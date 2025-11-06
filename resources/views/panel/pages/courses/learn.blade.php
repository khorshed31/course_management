@extends('panel.layouts.app')

@section('title', 'Learn: '.$course->title)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/plyr@3/dist/plyr.css"/>

<style>
/* ===== SCOPED TO PANEL LEARN UI ===== */
#learn-ui-panel{ --sidebarW: 320px; --radius:16px; --pad:1rem; --cardShadow:0 10px 30px rgba(2,8,23,.06); }
#learn-ui-panel .layout{display:grid;grid-template-columns:1fr;gap:1rem}
@media (min-width:992px){
  #learn-ui-panel .layout{grid-template-columns:minmax(0,1fr) var(--sidebarW)}
}
.plyr__tooltip { font-weight:700 }
/* Lesson info mini-table (2x2) */
.lesson-mini {
  --bd:#e5e7eb; --pad:.75rem; --radius:10px;
  border:1px solid var(--bd); border-radius:var(--radius); overflow:hidden;
  display:grid; grid-template-columns:1fr minmax(90px,180px);
  margin-bottom:1rem; background:#fff;
}
.lesson-mini > div{ padding:var(--pad); border-right:1px solid var(--bd); border-bottom:1px solid var(--bd); }
.lesson-mini > div:nth-child(2n){ border-right:none; text-align:center; font-weight:700; }
.lesson-mini > div:nth-last-child(-n+2){ border-bottom:none; }
.lesson-mini .muted{ color:#475569; font-weight:700; }
.lesson-mini.rtl{ direction:rtl; }
@media (max-width:575.98px){
  .lesson-mini{ grid-template-columns:1fr minmax(70px,120px); }
}

/* Card/Panes */
#learn-ui-panel .pane{background:#fff;border:1px solid #e5e7eb;border-radius:var(--radius);box-shadow:var(--cardShadow)}
#learn-ui-panel .pane .pad{padding:var(--pad)}
@media (min-width:992px){ #learn-ui-panel .pane .pad{padding:1.25rem} }

/* Media wrapper */
#learn-ui-panel .media{
  aspect-ratio:16/9;background:linear-gradient(145deg,#0b1226,#111827);
  border-radius:14px;overflow:hidden;position:relative;box-shadow:0 12px 40px rgba(2,8,23,.25)
}
#learn-ui-panel .media iframe, #learn-ui-panel .media video{width:100%;height:100%;display:block}

/* Title & text */
#learn-ui-panel .lesson-title{font-weight:800;font-size:1.1rem;margin:0}
#learn-ui-panel .desc{color:#334155;line-height:1.75;font-size:1rem}
#learn-ui-panel .lesson-meta{color:#64748b;font-size:.9rem}

/* File box */
#learn-ui-panel .file-box{border:1px dashed #cbd5e1;padding:1rem;border-radius:12px;background:#f8fafc}

/* Curriculum list */
#learn-ui-panel .chapter{margin-bottom:.75rem}
#learn-ui-panel .chapter h6{margin:0 0 .35rem 0;font-weight:800;font-size:.95rem}
#learn-ui-panel .lesson-item{display:flex;align-items:center;gap:.6rem;padding:.65rem .7rem;border-radius:10px;transition:background .2s, transform .05s}
#learn-ui-panel .lesson-item:hover{background:#f1f5f9}
#learn-ui-panel .lesson-item.active{background:#eaf3ff;border:1px solid #cfe2ff}
#learn-ui-panel .lesson-item .ok{color:#22c55e}
#curriculumCloneTarget .lesson-item .ok,
.offcanvas.curriculum #curriculumCloneTarget .lesson-item .ok { color: #22c55e !important; }
#learn-ui-panel .lesson-item .icon{font-size:1.05rem}

/* Header progress chip */
#learn-ui-panel .progress-pill{
  font-weight:700;color:#1e90ff;background:#eef6ff;border:1px solid #cfe2ff;border-radius:999px;padding:.25rem .6rem;
}

/* Sticky bottom action bar (mobile) */
.mob-actions{
  position:sticky;bottom:0;left:0;right:0;background:#0b1226;
  border-top:1px solid #0f172a;padding:.6rem;z-index:30;
  display:flex;gap:.6rem;justify-content:space-between;align-items:center;
}
.mob-actions .btn{flex:1;border-radius:12px;font-weight:700}
.mob-actions .btn:disabled{opacity:.6}

/* Float FAB for content (mobile) */
#contentFab{
  position:fixed;right:16px;bottom:78px;z-index:40;
  border-radius:999px;box-shadow:0 14px 30px rgba(2,8,23,.25);
  padding:.8rem 1rem;font-weight:800
}

/* Offcanvas (curriculum) */
.offcanvas.curriculum{max-width:92vw;width:380px}
.offcanvas.curriculum .offcanvas-header{border-bottom:1px solid #e5e7eb}

/* Desktop actions row spacing */
.actions .btn{border-radius:10px}

/* Responsive tweaks */
@media (max-width:991.98px){
  /* Hide desktop sidebar, use offcanvas instead */
  #learn-ui-panel aside.pane{display:none}
  /* Add subtle padding around content card */
  #learn-ui-panel .pane .pad{padding:.9rem}
  /* Bump tap targets */
  #learn-ui-panel .lesson-item{padding:.8rem .85rem}
  .desc{font-size:1rem}
}
@media (max-width:575.98px){
  #contentFab{bottom:76px;right:14px}
}
</style>
@endpush

@section('content')
<div id="learn-ui-panel">

  {{-- Page header --}}
  <div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
      <div class="nk-block-head-content">
        <h3 class="nk-block-title page-title">Learning: {{ $course->title }}</h3>
        <div class="nk-block-des text-soft">
          <p>Continue your lesson below. Use the content panel to navigate chapters.</p>
        </div>
      </div>
      <div class="nk-block-head-content">
        <div class="progress-pill">Progress: {{ (int)($enrollment->progress_percent ?? 0) }}%</div>
      </div>
    </div>
  </div>

  <div class="nk-block">
    <div class="layout">
      {{-- Content Pane --}}
      <div class="pane">
        <div class="pad">

          {{-- Player wrapper --}}
          <div id="playerWrap"
               data-course="{{ $course->id }}"
               data-lesson="{{ $lesson->id }}"
               data-seek="{{ (int)($seekSeconds ?? 0) }}"
               data-provider="{{ strtolower($lesson->video_provider ?? 'html5') }}"
               data-type="{{ strtolower($lesson->type ?? '') }}"
               data-gate="0"
               data-completed="{{ $completed ? '1' : '0' }}">

            {{-- Media / Content --}}
            @if($lesson->type === 'video')
              <div class="media mb-3">
                @if($lesson->video_provider === 'youtube' && $lesson->video_url)
                  @php
                    $yt = \Illuminate\Support\Str::contains($lesson->video_url, 'http') ? $lesson->video_url : 'https://www.youtube.com/watch?v='.$lesson->video_url;
                    $ytId = preg_replace('/.*(?:v=|be\/)([^&?]+).*/', '$1', $yt);
                  @endphp
                  <iframe id="ytPlayer"
                          src="https://www.youtube.com/embed/{{ $ytId }}?enablejsapi=1"
                          title="YouTube video" frameborder="0" allowfullscreen loading="lazy"></iframe>

                @elseif($lesson->video_provider === 'vimeo' && $lesson->video_url)
                  @php $vmId = preg_replace('/.*vimeo\.com\/(\d+).*/', '$1', $lesson->video_url); @endphp
                  <iframe id="vimeoPlayer"
                          src="https://player.vimeo.com/video/{{ $vmId }}"
                          frameborder="0" allowfullscreen loading="lazy"></iframe>
                  <script src="https://player.vimeo.com/api/player.js"></script>

                @elseif($lesson->video_provider === 'local' && $lesson->video_file_path)
                  <video id="html5Video"
                        controls
                        preload="metadata"
                        src="{{ asset($lesson->video_file_path) }}"
                        type="{{ $lesson->video_mime ?? 'video/mp4' }}">
                  </video>

                @elseif($lesson->video_provider === 'external' && $lesson->video_url)
                  <iframe id="extPlayer"
                          src="{{ $lesson->video_url }}"
                          frameborder="0" allowfullscreen loading="lazy"></iframe>
                @else
                  <div class="file-box">No video attached.</div>
                @endif
              </div>
            @endif
          </div>

          {{-- FILE content --}}
          @if($lesson->type === 'file')
            @php
              $path = $lesson->file_path ? asset($lesson->file_path) : null;
              $mime = strtolower($lesson->mime_type ?? '');
              $sizeKb = $lesson->file_size ? number_format($lesson->file_size/1024, 0) : null;

              $isPdf   = str_starts_with($mime, 'application/pdf');
              $isImg   = str_starts_with($mime, 'image/');
              $isAudio = str_starts_with($mime, 'audio/');
              $isText  = str_starts_with($mime, 'text/') || in_array($mime, ['application/json','application/xml']);
            @endphp

            <div class="file-box mb-3">
              @if($path)
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
                  <div>
                    <strong>File:</strong> {{ basename($lesson->file_path) }}
                    <div class="lesson-meta">
                      Type: {{ $mime ?: 'unknown' }} @if($sizeKb) • Size: {{ $sizeKb }} KB @endif
                    </div>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button"
                            class="btn btn-outline-primary btn-sm"
                            id="viewLargeBtn"
                            data-file-src="{{ $path }}"
                            data-mime="{{ $mime }}">
                      <em class="icon ni ni-maximize"></em> View Larger
                    </button>
                    <a class="btn btn-sm btn-primary" href="{{ $path }}" download>
                      <em class="icon ni ni-download"></em> Download
                    </a>
                  </div>
                </div>

                {{-- Inline preview --}}
                @if($isPdf)
                  <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;height:70vh;background:#f8fafc">
                    <iframe src="{{ $path }}" style="width:100%;height:100%;border:0" title="PDF preview"></iframe>
                  </div>
                @elseif($isImg)
                  <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;background:#0f172a;display:flex;justify-content:center">
                    <img src="{{ $path }}" alt="{{ e($lesson->title) }}" style="max-width:100%;height:auto;display:block">
                  </div>
                @elseif($isAudio)
                  <audio controls style="width:100%">
                    <source src="{{ $path }}" type="{{ $mime }}">
                    Your browser does not support the audio element.
                  </audio>
                @elseif($isText)
                  @php
                    $abs = public_path($lesson->file_path);
                    $preview = (is_file($abs) && filesize($abs) <= 102400)
                      ? e(\Illuminate\Support\Str::of(file_get_contents($abs))->limit(5000, ' …'))
                      : null;
                  @endphp
                  @if($preview)
                    <pre style="white-space:pre-wrap;border:1px solid #e5e7eb;border-radius:12px;padding:1rem;background:#f8fafc;max-height:60vh;overflow:auto">{{ $preview }}</pre>
                  @else
                    <div class="text-soft small">Large text file — use Download to view the full content.</div>
                  @endif
                @else
                  <div class="d-flex align-items-center gap-3">
                    <div class="btn btn-dim btn-secondary">
                      <em class="icon ni ni-clip-v"></em>
                    </div>
                    <div>
                      <div>Preview not available for this file type.</div>
                      <div class="text-soft small">Use the button above to download and open locally.</div>
                    </div>
                  </div>
                @endif
              @else
                No file attached.
              @endif
            </div>
          @endif

          @if($lesson->type === 'text')
            <div class="desc mb-3">
              {!! $lesson->content_text ?: '<p>No content.</p>' !!}
            </div>
          @endif

          <h5 class="lesson-title mb-2">{{ $lesson->title }}</h5>
          <div class="lesson-meta mb-3">
            @if($lesson->duration_seconds) <em class="icon ni ni-clock"></em> ~{{ ceil($lesson->duration_seconds/60) }} min @endif
          </div>

          @php
            // Labels (fallback EN; switch to Arabic if your app locale is 'ar')
            $isAr = app()->getLocale() === 'ar';
            $labelToils  = 'العدات';
            $labelRounds = 'الجولات';
            $order  = $lesson->sort_order ?? null;   // "Order" reuses sort_order
            $toils  = $lesson->toils ?? null;        // integer (optional)
            $rounds = trim($lesson->rounds ?? '');   // plain string e.g. "10, 12, 14, 16"
          @endphp

          <div class="lesson-mini">
            {{-- Row 1: Title | Order --}}
            <div class="text-truncate" title="{{ $lesson->title }}"><b>{{ $lesson->title }}</b></div>
            <div>{{ $order ?: '-' }}</div>

            {{-- Row 2: Toils | Rounds --}}
            <div class="muted">{{ $labelToils }} {{ $toils !== null ? $toils : '-' }}</div>
            <div>{{ $rounds !== '' ? $rounds : '-' }}</div>

            {{-- Row 3: Notes  --}}
            <div class="muted"><strong>{{ app()->getLocale()==='ar' ? 'ملاحظات' : 'Notes' }}:</strong></div>
            <div>{{ $lesson->notes }}</div>

            {{-- Row 4: Tours  --}}
            <div class="muted"><strong>{{ app()->getLocale()==='ar' ? 'الجولات' : 'Tours' }}:</strong></div>
            <div>{{ $lesson->others }}</div>

          </div>

          {{-- @if(!empty($lesson->notes) || !empty($lesson->others))
            <div class="mt-2" style="border:1px solid #e5e7eb;border-radius:10px;padding:.75rem;background:#fff">
              @if(!empty($lesson->notes))
                <div class="mb-1"><strong>{{ app()->getLocale()==='ar' ? 'ملاحظات' : 'Notes' }}:</strong>
                  <div class="text-muted" style="white-space:pre-wrap">{{ $lesson->notes }}</div>
                </div>
              @endif
              @if(!empty($lesson->others))
                <div><strong>{{ app()->getLocale()==='ar' ? 'أخرى' : 'Others' }}:</strong> <span class="text-muted">{{ $lesson->others }}</span></div>
              @endif
            </div>
          @endif
          <br> --}}

          {{-- Actions (desktop/tablet) --}}
          <div class="actions d-flex flex-wrap gap-2 d-none d-lg-flex">
            @if($prev)
              <a class="btn btn-outline-primary" href="{{ route('learn.lesson', [$course->id, $prev->id]) }}">
                <em class="icon ni ni-arrow-left"></em> Prev
              </a>
            @else
              <button class="btn btn-outline-primary" disabled>
                <em class="icon ni ni-arrow-left"></em> Prev
              </button>
            @endif

            @if($next)
              <button id="nextBtn"
                      class="btn btn-primary"
                      data-href="{{ route('learn.lesson', [$course->id, $next->id]) }}">
                Next <em class="icon ni ni-arrow-right"></em>
              </button>
            @else
              {{-- Last lesson: only show Complete if not already done --}}
              @unless($completed)
                <form method="POST" action="{{ route('learn.lesson.complete', $lesson->id) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-success">
                    <em class="icon ni ni-check-thick"></em> Complete
                  </button>
                </form>
              @else
                <button class="btn btn-success" disabled>
                  <em class="icon ni ni-check-thick"></em> Completed
                </button>
              @endunless
            @endif
          </div>

          {{-- Mobile sticky action bar --}}
          <div class="mob-actions d-lg-none">
            @if($prev)
              <a class="btn btn-outline-light" href="{{ route('learn.lesson', [$course->id, $prev->id]) }}">
                <em class="icon ni ni-arrow-left"></em> Prev
              </a>
            @else
              <button class="btn btn-outline-light" disabled><em class="icon ni ni-arrow-left"></em> Prev</button>
            @endif

            @if($next)
              <button id="nextBtnMobile" class="btn btn-primary" data-href="{{ route('learn.lesson', [$course->id, $next->id]) }}">
                Next <em class="icon ni ni-arrow-right"></em>
              </button>
            @else
              @unless($completed)
                <form method="POST" action="{{ route('learn.lesson.complete', $lesson->id) }}" class="w-100 d-inline">
                  @csrf
                  <button class="btn btn-success w-100"><em class="icon ni ni-check-thick"></em> Complete</button>
                </form>
              @else
                <button class="btn btn-success w-100" disabled><em class="icon ni ni-check-thick"></em> Completed</button>
              @endunless
            @endif
          </div>

        </div>
      </div>

      {{-- Curriculum Sidebar (desktop/tablet) --}}
      <aside class="pane">
        <div class="pad">
          <h6 class="mb-3">Course Content</h6>
          @forelse($chapters as $ch)
            <div class="chapter">
              <h6 class="mb-2">{{ $ch->title }}</h6>
              <div class="gy-2">
                @forelse($ch->lessons as $ls)
                  @php
                    $isActive = $ls->id === $lesson->id;
                    $isDone   = \App\Models\LessonCompletion::where('lesson_id',$ls->id)->where('user_id',auth()->id())->exists();
                    $rowUrl   = route('learn.lesson', [$course->id, $ls->id]);
                  @endphp

                  <a class="lesson-item {{ $isActive ? 'active' : '' }} d-block" href="{{ $rowUrl }}">
                    @if($isDone)
                      <em class="icon ni ni-check-circle ok"></em>
                    @else
                      <em class="icon ni ni-play-circle text-primary"></em>
                    @endif
                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="text-dark">{{ $ls->title }}</span>
                        <small class="lesson-meta">
                          @if($ls->duration_seconds) {{ ceil($ls->duration_seconds/60) }}m @endif
                        </small>
                      </div>
                    </div>
                  </a>
                @empty
                  <div class="text-soft small">No lessons.</div>
                @endforelse
              </div>
            </div>
          @empty
            <div class="text-soft">No chapters yet.</div>
          @endforelse

          <div class="mt-2">
            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-outline-secondary w-100">
              <em class="icon ni ni-book-read"></em> Course Details
            </a>
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>

{{-- Floating "Course Content" button (mobile only) --}}
<button id="contentFab" class="btn btn-primary d-lg-none">
  <em class="icon ni ni-menu"></em> Content
</button>

<!-- Offcanvas: Curriculum (mobile) -->
<div class="offcanvas offcanvas-end curriculum" tabindex="-1" id="curriculumCanvas" aria-labelledby="curriculumCanvasLabel">
  <div class="offcanvas-header">
    <h5 id="curriculumCanvasLabel" class="mb-0">Course Content</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div id="curriculumCloneTarget"></div>
  </div>
</div>

{{-- Big file modal --}}
<div id="fileModal" class="file-modal" style="display:none;position:fixed;inset:0;z-index:1050;background:rgba(15,23,42,.75)">
  <div class="file-modal-body" style="position:absolute;inset:4%;background:#fff;border-radius:14px;box-shadow:0 12px 40px rgba(0,0,0,.25);overflow:hidden;display:flex;flex-direction:column">
    <div style="padding:.6rem .9rem;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between">
      <strong>Preview</strong>
      <button type="button" id="fileModalClose" class="btn btn-sm btn-outline-secondary">
        <em class="icon ni ni-cross"></em> Close
      </button>
    </div>
    <div id="fileModalContent" style="flex:1;min-height:0;background:#0f172a;display:flex;align-items:center;justify-content:center"></div>
  </div>
</div>

{{-- Congrats Modal --}}
<div id="congratsModal" style="display:none;position:fixed;inset:0;z-index:1100;background:rgba(15,23,42,.75)">
  <div style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);
              width:min(560px,92vw);background:#fff;border-radius:16px;box-shadow:0 16px 60px rgba(0,0,0,.3);
              overflow:hidden">
    <div style="padding:1rem 1.25rem;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between">
      <strong>Congratulations! 🎉</strong>
      <button type="button" id="congratsClose" class="btn btn-sm btn-outline-secondary">
        <em class="icon ni ni-cross"></em> Close
      </button>
    </div>
    <div style="padding:1.25rem">
      <h4 style="margin:0 0 .5rem 0">You’ve completed <span class="text-primary">{{ $course->title }}</span></h4>
      <p class="text-soft mb-3">Great job finishing all lessons. Your progress is now 100%.</p>

      <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('home') }}" class="btn btn-primary">
          <em class="icon ni ni-book-read"></em> Back to Course
        </a>
        {{-- <a href="{{ route('courses.certificate', $course->id) }}" class="btn btn-success">
          <em class="icon ni ni-award"></em> Download Certificate
        </a> --}}
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/plyr@3/dist/plyr.polyfilled.min.js"></script>

<script>
/* -------------------------
   1) Prevent double-submit & reflect UI immediately on manual complete
   ------------------------- */
document.addEventListener('DOMContentLoaded', () => {
  // Prevent double submit on "Complete"
  document.querySelectorAll('form[action*="learn/lesson/"][action*="/complete"]').forEach(f=>{
    const btn = f.querySelector('button[type="submit"], button');
    if (!btn) return;
    f.addEventListener('submit', ()=>{
      btn.disabled = true;
      btn.innerHTML = '<em class="icon ni ni-check-thick"></em> Completing...';
    });
  });

  // Also reflect Completed UI immediately when the manual complete form is submitted
  document.querySelectorAll('form[action*="learn/lesson/"][action*="/complete"]').forEach(form=>{
    form.addEventListener('submit', function(e){
      try { window.__markLessonCompleteInUI?.(+(document.getElementById('playerWrap')?.dataset.lesson || 0)); } catch(_) {}
    });
  });
});

/* -------------------------
   2) Congrats modal (session flag)
   ------------------------- */
(function(){
  const showCongrats = @json(session('course_completed', false));
  if (!showCongrats) return;

  const modal = document.getElementById('congratsModal');
  const btnX  = document.getElementById('congratsClose');

  function confettiBurst() {
    const end = Date.now() + 600;
    const colors = ['#16a34a','#2563eb','#f59e0b','#ef4444','#8b5cf6','#06b6d4'];
    function spawn() {
      for (let i = 0; i < 12; i++) {
        const el = document.createElement('i');
        el.style.position = 'fixed';
        el.style.left = Math.random() * 100 + 'vw';
        el.style.top = '-12px';
        el.style.width = '8px';
        el.style.height = '12px';
        el.style.background = colors[Math.floor(Math.random()*colors.length)];
        el.style.opacity = '0.95';
        el.style.borderRadius = '2px';
        el.style.transform = `rotate(${Math.random()*360}deg)`;
        el.style.zIndex = 1200;
        document.body.appendChild(el);

        const dx = (Math.random() - .5) * 100;
        const duration = 1000 + Math.random()*800;

        el.animate([
          { transform:`translate(0,0) rotate(0deg)`, opacity: 1 },
          { transform:`translate(${dx}px, 100vh) rotate(${360+Math.random()*360}deg)`, opacity: .9 }
        ], { duration, easing: 'cubic-bezier(.2,.7,.2,1)' }).onfinish = ()=> el.remove();
      }
      if (Date.now() < end) requestAnimationFrame(spawn);
    }
    spawn();
  }

  modal.style.display = 'block';
  document.body.style.overflow = 'hidden';
  confettiBurst();

  function close() {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
  btnX?.addEventListener('click', close);
  modal.addEventListener('click', (e)=>{ if (e.target === modal) close(); });
  window.addEventListener('keydown', (e)=>{ if (e.key === 'Escape') close(); });
})();

/* -------------------------
   3) File "View Larger" modal
   ------------------------- */
(function(){
  const btn  = document.getElementById('viewLargeBtn');
  const wrap = document.getElementById('fileModal');
  const box  = document.getElementById('fileModalContent');
  const xBtn = document.getElementById('fileModalClose');

  if(!btn || !wrap || !box || !xBtn) return;

  const open = (src, mime) => {
    box.innerHTML = '';
    const isPdf   = (mime || '').startsWith('application/pdf');
    const isImg   = (mime || '').startsWith('image/');
    const isAudio = (mime || '').startsWith('audio/');
    const isText  = (mime || '').startsWith('text/') || ['application/json','application/xml'].includes(mime);

    if (isPdf) {
      const iframe = document.createElement('iframe');
      iframe.src = src;
      iframe.style.cssText = 'border:0;width:100%;height:100%';
      iframe.title = 'PDF preview';
      box.appendChild(iframe);
    } else if (isImg) {
      const img = document.createElement('img');
      img.src = src;
      img.alt = 'Image preview';
      img.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain;display:block';
      box.appendChild(img);
    } else if (isAudio) {
      const audio = document.createElement('audio');
      audio.controls = true;
      audio.style.width = '100%';
      audio.innerHTML = `<source src="${src}" type="${mime}">`;
      box.appendChild(audio);
    } else if (isText) {
      const iframe = document.createElement('iframe');
      iframe.src = src;
      iframe.style.cssText = 'border:0;width:100%;height:100%;background:#0f172a;color:#e5e7eb';
      box.appendChild(iframe);
    } else {
      const div = document.createElement('div');
      div.style.cssText = 'color:#e5e7eb;text-align:center;padding:1rem';
      div.innerHTML = `
        <div style="margin-bottom:.5rem;font-weight:700">Preview not available</div>
        <div class="text-soft">Please use the Download button to open this file.</div>
      `;
      box.appendChild(div);
    }

    wrap.style.display = 'block';
    document.body.style.overflow = 'hidden';
  };

  const close = () => {
    wrap.style.display = 'none';
    box.innerHTML = '';
    document.body.style.overflow = '';
  };

  btn.addEventListener('click', () => open(btn.dataset.fileSrc, btn.dataset.mime));
  xBtn.addEventListener('click', close);
  wrap.addEventListener('click', (e)=>{ if(e.target === wrap) close(); });
  window.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') close(); });
})();

/* -------------------------
   4) Player + Progress (NO GATING)
   ------------------------- */

// ----- Config from DOM -----
const wrap        = document.getElementById('playerWrap');
const COURSE_ID   = +(wrap?.dataset.course || 0);
const LESSON_ID   = +(wrap?.dataset.lesson || 0);
const SEEK_AT     = +(wrap?.dataset.seek || 0);
const TYPE        = (wrap?.dataset.type || '').toLowerCase();
const PROVIDER    = (wrap?.dataset.provider || '').toLowerCase();
const COMPLETED   = (wrap?.dataset.completed || '0') === '1';
const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content;

// Save throttle
let lastSent = 0;
let timer = null;

// Save playback position (enrollment.last_position_seconds, last_lesson_id)
function savePosition(pos, duration){
  pos = Math.floor(pos || 0);
  if (Math.abs(pos - lastSent) < 3) return; // throttle ~3s
  lastSent = pos;

  fetch(@json(route('learn.progress.save')), {
    method: 'POST',
    headers: {
      'Content-Type':'application/json',
      'X-CSRF-TOKEN': CSRF,
      'Accept':'application/json'
    },
    body: JSON.stringify({
      course_id: COURSE_ID,
      lesson_id: LESSON_ID,
      position:  pos,
      duration:  Math.floor(duration || 0)
    })
  }).catch(()=>{});
}

// Utility: enable/disable Next (desktop + mobile)
const NEXT_BTN       = document.getElementById('nextBtn');
const NEXT_BTN_MOB   = document.getElementById('nextBtnMobile');
const NEXT_HREF      = NEXT_BTN?.dataset.href || NEXT_BTN_MOB?.dataset.href || null;
const COMPLETE_URL   = @json(route('learn.lesson.complete', $lesson->id));

function setNextEnabled(on){
  if (NEXT_BTN)     on ? NEXT_BTN.removeAttribute('disabled')     : NEXT_BTN.setAttribute('disabled','disabled');
  if (NEXT_BTN_MOB) on ? NEXT_BTN_MOB.removeAttribute('disabled') : NEXT_BTN_MOB.setAttribute('disabled','disabled');
}

// Auto-complete then navigate (kept for progress consistency)
async function completeThenGoNext(){
  if (!NEXT_HREF) return;
  try {
    await fetch(COMPLETE_URL, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept':'text/html' },
    });
  } catch(e) { /* ignore */ }
  window.location = NEXT_HREF;
}

/* -------------------------
   UI helper: mark lesson item(s) as done
   ------------------------- */
function updateLessonUI(lessonId){
  if (!lessonId) return;
  const sel = `.lesson-item[href*="/learn/lesson/${COURSE_ID}/${lessonId}"], .lesson-item[href$="/${lessonId}"]`;

  document.querySelectorAll(sel).forEach(a=>{
    if (!a) return;
    const icon = a.querySelector('.icon');
    if (icon) {
      icon.className = 'icon ni ni-check-circle ok';
    } else {
      const em = document.createElement('em'); em.className = 'icon ni ni-check-circle ok';
      a.insertBefore(em, a.firstChild);
    }
  });

  // replace any Complete form button with disabled "Completed" state
  document.querySelectorAll('form[action*="/learn/lesson/"][action*="/complete"]').forEach(f=>{
    const btn = f.querySelector('button[type="submit"], button');
    if (!btn) return;
    const done = document.createElement('button');
    done.className = btn.className || 'btn btn-success';
    done.disabled = true;
    done.innerHTML = '<em class="icon ni ni-check-thick"></em> Completed';
    btn.replaceWith(done);
  });
}
window.__markLessonCompleteInUI = updateLessonUI;

// ============================
// Local HTML5 video (no gating)
// ============================
function setupHTML5(){
  const v = document.getElementById('html5Video');
  if (!v) return;

  // Your existing behavior
  setNextEnabled(true);
  if (COMPLETED) { try { updateLessonUI(LESSON_ID); } catch(_) {} }

  // Resume
  v.addEventListener('loadedmetadata', ()=>{
    if (SEEK_AT > 0 && SEEK_AT < (v.duration || Infinity)) {
      try { v.currentTime = SEEK_AT; } catch(e){}
    }
  }, { once:true });

  // Progress save (unchanged)
  v.addEventListener('timeupdate', ()=> savePosition(v.currentTime, v.duration||0));
  window.addEventListener('beforeunload', ()=> savePosition(v.currentTime, v.duration||0));

  // ⬇️ Soft-enhance with Plyr only if it’s available; otherwise keep native controls
  if (window.Plyr) {
    try {
      new Plyr(v, {
        ratio: '16:9',
        controls: [
          'play-large','play','progress','current-time','duration',
          'mute','volume','pip','airplay','settings','fullscreen'
        ],
        settings: ['captions','speed'],
        speed: { selected: 1, options: [0.5,0.75,1,1.25,1.5,1.75,2] },
        tooltips: { controls: true, seek: true }
      });
    } catch (e) {
      console.warn('Plyr failed, using native controls', e);
      // native continues to work
    }
  }
}

// ============================
// YouTube: save progress
// ============================
let ytPlayerObj = null;
function onYouTubeIframeAPIReady(){
  ytPlayerObj = new YT.Player('ytPlayer', {
    events:{
      'onReady': (e)=>{
        if (SEEK_AT > 0) {
          try { e.target.seekTo(SEEK_AT, true); } catch(_){}
        }
      },
      'onStateChange': (e)=>{
        if (e.data === YT.PlayerState.PLAYING) {
          if (timer) clearInterval(timer);
          timer = setInterval(()=>{
            const t = ytPlayerObj?.getCurrentTime?.() || 0;
            const d = ytPlayerObj?.getDuration?.() || 0;
            savePosition(t, d);
          }, 2000);
        } else {
          if (timer) clearInterval(timer);
          const t = ytPlayerObj?.getCurrentTime?.() || 0;
          const d = ytPlayerObj?.getDuration?.() || 0;
          savePosition(t, d);
        }
      }
    }
  });
}

// ============================
// Vimeo: save progress
// ============================
function setupVimeo(){
  const iframe = document.getElementById('vimeoPlayer');
  if (!iframe || !window.Vimeo) return;
  const player = new Vimeo.Player(iframe);

  player.ready().then(()=>{
    if (SEEK_AT > 0) player.setCurrentTime(SEEK_AT).catch(()=>{});
  });

  timer = setInterval(async ()=>{
    try{
      const t = await player.getCurrentTime();
      const d = await player.getDuration();
      savePosition(t||0, d||0);
    }catch(_){}
  }, 2000);

  window.addEventListener('beforeunload', async ()=>{
    try{
      const t = await player.getCurrentTime();
      const d = await player.getDuration();
      savePosition(t||0, d||0);
    }catch(_){}
  });
}

// ============================
// Offcanvas curriculum (mobile)
// ============================
(function(){
  const fab = document.getElementById('contentFab');
  const canvasEl = document.getElementById('curriculumCanvas');
  const cloneTarget = document.getElementById('curriculumCloneTarget');

  if (fab && canvasEl && cloneTarget) {
    const off = new bootstrap.Offcanvas(canvasEl);
    let cloned = false;

    fab.addEventListener('click', () => {
      if (!cloned) {
        const sidebar = document.querySelector('#learn-ui-panel aside.pane .pad');
        if (sidebar) {
          const copy = sidebar.cloneNode(true);
          // Ensure completed icons persist in clone (best effort)
          try {
            sidebar.querySelectorAll('.lesson-item').forEach((origItem, idx) => {
              const cloneItem = copy.querySelectorAll('.lesson-item')[idx];
              if (!cloneItem) return;
              const origOk = origItem.querySelector('.ok');
              if (origOk) {
                let icon = cloneItem.querySelector('.icon');
                if (icon) icon.className = 'icon ni ni-check-circle ok';
                else {
                  const em = document.createElement('em');
                  em.className = 'icon ni ni-check-circle ok';
                  cloneItem.insertBefore(em, cloneItem.firstChild);
                }
              }
            });
          } catch(_) {}

          cloneTarget.innerHTML = '';
          cloneTarget.appendChild(copy);
          cloned = true;
        }
      }
      off.show();
    });

    window.openCurriculum = () => off.show();
  }
})();

// ============================
// Bootstrap init: wire players + Next buttons
// ============================
document.addEventListener('DOMContentLoaded', ()=>{
  // Always allow Next from the start
  setNextEnabled(true);

  if (TYPE === 'video') {
    if (PROVIDER === 'local' || PROVIDER === 'html5') {
      setupHTML5();
    } else if (PROVIDER === 'youtube') {
      const tag = document.createElement('script');
      tag.src = "https://www.youtube.com/iframe_api";
      document.head.appendChild(tag);
      window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady;
    } else if (PROVIDER === 'vimeo') {
      setupVimeo(); // player.js already included in Blade
    }
  }

  // Wire Next buttons (desktop & mobile)
  function wireNext(btn){
    if (!btn) return;
    btn.addEventListener('click', (e)=>{
      e.preventDefault();
      completeThenGoNext(); // auto-complete then navigate
    });
  }
  wireNext(NEXT_BTN);
  wireNext(NEXT_BTN_MOB);
});
</script>

@endpush
