<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="DashLite Admin Panel">
<meta name="author" content="Softnio">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Favicon from settings --}}
@if(function_exists('setting') && setting('favicon'))
  <link rel="shortcut icon" href="{{ asset(setting('favicon')) }}">
@else
  <link rel="shortcut icon" href="{{ asset('panel/images/favicon.png') }}">
@endif

<title>@yield('title', (function_exists('setting') ? setting('site_name','Dashboard | LMS') : 'Dashboard | LMS'))</title>

{{-- DashLite core CSS (includes Bootstrap) --}}
<link rel="stylesheet" href="{{ asset('panel/assets/css/dashlite9b70.css') }}">
<link id="skin-default" rel="stylesheet" href="{{ asset('panel/assets/css/theme9b70.css') }}">

@php $isRtl = (($htmlDir ?? session('dir','ltr')) === 'rtl'); @endphp
{{-- @if($isRtl)
  <link rel="stylesheet" href="{{ asset('panel/assets/css/rtl-fixes.css') }}">
@endif --}}

{{-- Always include admin overrides (full-width & spacing) --}}
<link rel="stylesheet" href="{{ asset('panel/assets/css/admin-overrides.css') }}">

{{-- Optional vendor CSS --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.css" rel="stylesheet"/>
