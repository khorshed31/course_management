@extends('frontend.layouts.app')
@section('title','Verify Email')

@section('content')
<div class="container py-4" style="max-width:520px">
  <h4 class="mb-3">Verify your email</h4>
  <p class="text-muted">
    We’ve sent a verification link to your email address.
    If you didn’t receive the email, click below to request another.
  </p>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('verification.send') }}" class="d-grid gap-2">
    @csrf
    <button class="btn btn-primary" type="submit">Resend Verification Email</button>
  </form>

  <form method="POST" action="{{ route('logout') }}" class="mt-3">
    @csrf
    <button class="btn btn-link p-0">Log out</button>
  </form>
</div>
@endsection