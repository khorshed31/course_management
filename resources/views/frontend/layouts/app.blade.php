<!DOCTYPE html>
<html lang="{{ $htmlLang ?? app()->getLocale() }}" dir="{{ $htmlDir ?? session('dir','ltr') }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>

    {{-- Dynamic Bootstrap CSS --}}
    @if(($htmlDir ?? 'ltr') === 'rtl')
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">
      <style>
        body { text-align: right; direction: rtl; }
        .text-start { text-align: right !important; }
        .text-end { text-align: left !important; }
      </style>
    @else
      <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">

    @include('frontend.layouts._head')
    @stack('styles')
  </head>
  <body class="{{ ($htmlDir ?? 'ltr') }} {{ ($promoBanner ?? null) ? 'has-promo' : '' }}">
    @include('frontend.layouts._navbar')

    <main>
      @yield('content')
    </main>

    @include('frontend.layouts._footer')

    <script src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>
    @include('frontend.layouts._scripts')
    @stack('scripts')
  </body>
</html>
