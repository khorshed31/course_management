@extends('panel.layouts.app')
@section('title', $page->exists ? 'Edit Page' : 'New Page')

@section('content')
<div class="nk-block">
  <h4 class="mb-3">{{ $page->exists ? 'Edit Page' : 'New Page' }}</h4>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="POST" action="{{ $page->exists ? route('admin.custom-pages.update',$page) : route('admin.custom-pages.store') }}" enctype="multipart/form-data">
    @csrf
    @if($page->exists) @method('PUT') @endif

    <div class="card">
      <div class="card-inner">
        <div class="row g-4">
          <div class="col-md-6">
            <label class="form-label">Title *</label>
            <input type="text" class="form-control" name="title" value="{{ old('title',$page->title) }}" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Icon (class)</label>
            <input type="text" class="form-control" name="icon" placeholder="ni ni-info / bi bi-info-circle" value="{{ old('icon',$page->icon) }}">
          </div>

          <div class="col-md-2">
            <label class="form-label">Position</label>
            <input type="number" class="form-control" name="position" value="{{ old('position',$page->position ?? 1) }}">
          </div>

          <div class="col-md-1 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published',$page->is_published) ? 'checked':'' }}>
              <label class="form-check-label" for="is_published">Publish</label>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Content</label>
            {{-- ✅ CKEditor target --}}
            <textarea id="editor" class="form-control" name="content" rows="8" placeholder="Write your page content (HTML or plain text)">{{ old('content',$page->content) }}</textarea>
            <small class="text-muted">You can paste HTML (e.g., from a WYSIWYG) or plain text.</small>
          </div>

          <div class="col-12">
            <label class="form-label">Attachment (image / PDF)</label>
            <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf">
            @if($page->attachment)
                <div class="mt-2 border rounded p-2 d-flex align-items-center justify-content-between">
                <a href="{{ asset($page->attachment) }}" target="_blank" class="me-2 text-truncate" style="max-width:70%">
                    {{ basename($page->attachment) }}
                </a>
                <label class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remove_attachment" value="1">
                    <span class="small">Remove</span>
                </label>
                </div>
            @endif
        </div>

        </div>
      </div>
    </div>

    <div class="mt-3">
      <button class="btn btn-primary">{{ $page->exists ? 'Update Page' : 'Create Page' }}</button>
      <a href="{{ route('admin.custom-pages.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
  {{-- CKEditor 5 Classic --}}
  <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: {
          items: [
            'heading', '|',
            'bold', 'italic', 'underline', 'link',
            'bulletedList', 'numberedList', 'blockQuote', '|',
            'insertTable', 'mediaEmbed', '|',
            'undo', 'redo'
          ]
        },
        link: { decorators: { addTargetToExternalLinks: true } },
        mediaEmbed: { previewsInData: true }
      }).catch(console.error);
    });
  </script>
@endpush
