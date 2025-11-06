@extends('panel.layouts.app')
@section('title', 'My Library')

@push('styles')
<style>
  .library-card .card { transition: transform .2s ease, box-shadow .2s ease; }
  .library-card .card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,.08); }
  .library-card img { height: 160px; object-fit: cover; border-bottom: 1px solid #f1f1f1; }
  .badge-price { font-weight:700; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">📚 My Purchased Books</h4>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($purchases->count())
    <div class="row g-3">
      @foreach($purchases as $purchase)
        @php $book = $purchase->book; @endphp
        @if($book)
        <div class="col-6 col-md-4 col-lg-3 library-card">
          <div class="card h-100 border-0 rounded-3 shadow-sm">
            @if($book->cover_path)
              <img src="{{ asset($book->cover_path) }}" alt="{{ $book->title }}" class="card-img-top rounded-top-3">
            @endif

            <div class="card-body d-flex flex-column p-3">
              <h6 class="fw-semibold mb-1 text-truncate" title="{{ $book->title }}">{{ $book->title }}</h6>
              @if($book->author)
                <small class="text-muted d-block mb-1">{{ $book->author }}</small>
              @endif

              <div class="mb-2">
                <span class="badge bg-primary badge-price">${{ number_format($book->price, 2) }}</span>
              </div>

              <div class="mt-auto">
                {{-- Preview & Download use your existing secure routes (auth + purchased required) --}}
                <button
                  class="btn btn-sm btn-primary w-100 rounded-pill mb-2 preview-pdf-btn"
                  data-pdf="{{ route('books.preview', $book->slug) }}"
                  data-title="{{ $book->title }}"
                  data-bs-toggle="modal"
                  data-bs-target="#pdfPreviewModal">
                  Preview
                </button>

                <a href="{{ route('books.download', $book->slug) }}"
                   class="btn btn-sm btn-outline-secondary w-100 rounded-pill">
                  Download
                </a>

                <small class="text-muted d-block mt-2">
                  Purchased on {{ $purchase->created_at->format('M d, Y') }}
                </small>
              </div>
            </div>
          </div>
        </div>
        @endif
      @endforeach
    </div>

    <div class="mt-4">
      {{ $purchases->links() }}
    </div>
  @else
    <div class="text-center py-5">
      <h5 class="mb-2">No purchases yet</h5>
      <p class="text-muted">When you buy books, they’ll appear here.</p>
      <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4">Browse Books</a>
    </div>
  @endif
</div>

{{-- PDF Preview Modal (shared) --}}
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pdfPreviewLabel">Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0" style="min-height:75vh">
        <embed id="nativePdfEmbed" src="" type="application/pdf" style="width:100%;height:75vh;border:0">
      </div>
      <div class="modal-footer">
        <a id="downloadPdfLink" href="#" class="btn btn-outline-secondary" target="_blank" rel="noopener">Open in new tab</a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Handle preview clicks (delegated)
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.preview-pdf-btn');
    if (!btn) return;
    const url   = btn.getAttribute('data-pdf');
    const title = btn.getAttribute('data-title') || 'Preview';
    document.getElementById('pdfPreviewLabel').textContent = title;
    document.getElementById('nativePdfEmbed').src = url + '#toolbar=0';
    document.getElementById('downloadPdfLink').href = url;
  });

  // Cleanup embed on modal close
  const modalEl = document.getElementById('pdfPreviewModal');
  modalEl.addEventListener('hidden.bs.modal', function () {
    document.getElementById('nativePdfEmbed').src = '';
  });
});
</script>
@endpush
