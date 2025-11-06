<!DOCTYPE html>
<html lang="{{ $htmlLang ?? app()->getLocale() }}" dir="{{ $htmlDir ?? session('dir','ltr') }}" class="js">
  <head>
    @include('panel.layouts._head')
    @stack('styles')
  </head>

  @php $isRtl = (($htmlDir ?? 'ltr') === 'rtl'); @endphp
  <body class="nk-body bg-lighter npc-general has-sidebar {{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="nk-app-root">
      <div class="nk-main {{ $isRtl ? 'nk-main-rtl' : '' }}">

        {{-- Sidebar --}}
        @include('panel.layouts.sidebar')

        <div class="nk-wrap">
          {{-- Header --}}
          @include('panel.layouts.header')

          {{-- Optional language buttons (remove if not needed) --}}
          {{-- <div class="container-fluid py-2 d-flex justify-content-{{ $isRtl ? 'start' : 'end' }}">
            <div class="btn-group btn-group-sm">
              <a class="btn btn-outline-secondary" href="{{ route('lang.switch','ar') }}">العربية</a>
              <a class="btn btn-outline-secondary" href="{{ route('lang.switch','en') }}">English</a>
            </div>
          </div> --}}

          {{-- MAIN CONTENT — now full width (no side gaps) --}}
          <div class="nk-content nk-content-full">
            <div class="nk-content-inner">
              <div class="nk-content-body p-0 m-0">
                @yield('content')
              </div>
            </div>
          </div>

          {{-- Footer --}}
          @include('panel.layouts.footer')
        </div>
      </div>
    </div>

    @include('panel.layouts._scripts')
    @stack('scripts')
  </body>
</html>
