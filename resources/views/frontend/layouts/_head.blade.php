<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>@yield('title', setting('site_name'))</title>

<link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

{{-- Shared CSS from public/frontend --}}
<link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/assets/css/font-awesome.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/assets/css/templatemo-training-studio.css') }}">

{{-- ✅ Add favicon from settings, fallback to a default --}}
@if(setting('favicon'))
  <link rel="icon" type="image/png" href="{{ asset(setting('favicon')) }}">
@else
  <link rel="icon" type="image/png" href="{{ asset('frontend/assets/images/default-favicon.png') }}">
@endif
