@extends('panel.layouts.app')

@section('title','Custom Pages')

@section('content')
<div class="nk-block">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Custom Pages</h4>
    <a href="{{ route('admin.custom-pages.create') }}" class="btn btn-primary">New Page</a>
  </div>

  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

  <div class="card">
    <div class="card-inner">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr><th>#</th><th>Title</th><th>Slug</th><th>Icon</th><th>Published</th><th>Position</th><th></th></tr>
          </thead>
          <tbody>
          @forelse($pages as $i => $p)
            <tr>
              <td>{{ $i+1 }}</td>
              <td><a href="{{ $p->url() }}" target="_blank">{{ $p->title }}</a></td>
              <td>{{ $p->slug }}</td>
              <td><code>{{ $p->icon }}</code></td>
              <td>{!! $p->is_published ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
              <td>{{ $p->position }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.custom-pages.edit',$p) }}">Edit</a>
                <form action="{{ route('admin.custom-pages.destroy',$p) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete page?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No pages yet.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
    {{-- Pagination --}}
      @if($pages->hasPages())
        <div class="card-footer">
          {{ $pages->links() }}
        </div>
      @endif
  </div>
</div>
@endsection