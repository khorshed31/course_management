
@extends('panel.layouts.app')

@section('title','Create Book')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Book Create</h4>
  <a href="{{ route('admin.books.index') }}" class="btn btn-primary">Back</a>
</div>
@if($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
<form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
  @include('panel.pages.books._form')
</form>
@endsection
