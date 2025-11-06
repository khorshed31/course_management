
@csrf
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Title *</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $book->title ?? '') }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Author</label>
    <input type="text" name="author" class="form-control" value="{{ old('author', $book->author ?? '') }}">
  </div>
  <div class="col-12">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control" rows="4">{{ old('description', $book->description ?? '') }}</textarea>
  </div>
  <div class="col-md-3">
    <label class="form-label">Pages</label>
    <input type="number" name="pages" class="form-control" min="1" value="{{ old('pages', $book->pages ?? '') }}">
  </div>
  <div class="col-md-3">
  <label class="form-label">Price (KWD)</label>
  <input type="number" name="price" class="form-control"
         min="0" step="0.01"
         value="{{ old('price', $book->price ?? 0) }}">
</div>
  <div class="col-md-3">
    <label class="form-label">Status *</label>
    <select name="status" class="form-select" required>
      @php $st = old('status', $book->status ?? 'draft'); @endphp
      <option value="draft" {{ $st==='draft'?'selected':'' }}>Draft</option>
      <option value="published" {{ $st==='published'?'selected':'' }}>Published</option>
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Published At</label>
    <input type="datetime-local" name="published_at" class="form-control"
           value="{{ old('published_at', isset($book->published_at) ? $book->published_at->format('Y-m-d\TH:i') : '') }}">
    <small class="text-muted">If left empty and status is Published, it will be set to now.</small>
  </div>

  <div class="col-md-6">
    <label class="form-label">Cover Image</label>
    <input type="file" name="cover" class="form-control" accept=".jpg,.jpeg,.png,.webp">
    @isset($book->cover_path)
      <div class="mt-2"><img src="{{ asset($book->cover_path) }}" style="height:64px;border-radius:6px"></div>
    @endisset
  </div>

  <div class="col-md-6">
    <label class="form-label">PDF File {{ isset($book) ? '(leave empty to keep)' : '*' }}</label>
    <input type="file" name="pdf" class="form-control" accept="application/pdf">
    @isset($book->file_path)
      <div class="mt-2">
        <a href="{{ asset($book->file_path) }}" target="_blank" class="btn btn-sm btn-outline-dark">Open Current PDF</a>
      </div>
    @endisset
  </div>

  <div class="col-12">
    <button class="btn btn-primary">{{ isset($book) ? 'Update' : 'Create' }}</button>
  </div>
</div>
