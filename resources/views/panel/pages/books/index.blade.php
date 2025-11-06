@extends('panel.layouts.app')

@section('title','Books')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Books</h4>
  <a href="{{ route('admin.books.create') }}" class="btn btn-primary">Add Book</a>
</div>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>SL</th>
        <th>Cover</th>
        <th>Title</th>
        <th>Price (KWD)</th>
        <th>Author</th>
        <th>Status</th>
        <th>Downloads</th>
        {{-- <th>Uploader</th> --}}
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($books as $book)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>@if($book->cover_path)<img src="{{ asset($book->cover_path) }}" style="height:44px;border-radius:6px">@endif</td>
        <td>{{ $book->title }}</td>
        <td>{{ number_format($book->price, 2) }}</td>
        <td>{{ $book->author }}</td>
        <td>
          <span class="badge {{ $book->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
            {{ ucfirst($book->status) }}
          </span>
          @if($book->published_at)
            <small class="text-muted d-block">{{ $book->published_at->format('Y-m-d') }}</small>
          @endif
        </td>
        <td>{{ $book->downloads_count }}</td>
        {{-- <td>{{ optional($book->uploader)->name }}</td> --}}
        <td class="text-end">
            <a href="{{ route('admin.books.edit',$book) }}" class="btn btn-sm btn-warning">
                <em class="bi bi-pencil"></em>
            </a>

            @if($book->file_path)
                <button
                    type="button"
                    class="btn btn-sm btn-dark preview-pdf-btn"
                    data-pdf="{{ route('admin.books.preview', $book) }}"
                    data-title="{{ $book->title }}"
                    data-bs-toggle="modal"
                    data-bs-target="#pdfPreviewModal">
                    <em class="bi bi-eye"></em>
                </button>
            @endif

            <form action="{{ route('admin.books.destroy',$book) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Delete this book?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">
                    <em class="bi bi-trash"></em>
                </button>
            </form>
            </td>
      </tr>
      @empty
      <tr><td colspan="8" class="text-center text-muted">No books yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- PDF Preview Modal --}}
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pdfPreviewLabel">Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-0" style="min-height:75vh">
        {{-- Prefer PDF.js viewer if available --}}
        <iframe id="pdfJsFrame" src="" style="display:none;width:100%;height:75vh;border:0"></iframe>

        {{-- Fallback native preview --}}
        <embed id="nativePdfEmbed" src="" type="application/pdf" style="display:none;width:100%;height:75vh;border:0">
      </div>

      <div class="modal-footer">
        {{-- Optional: show a subtle link if someone still wants to download --}}
        <a id="downloadPdfLink" href="#" class="btn btn-outline-secondary" target="_blank" rel="noopener">Open in new tab</a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{ $books->links() }}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const btns   = document.querySelectorAll('.preview-pdf-btn');
  const title  = document.getElementById('pdfPreviewLabel');
  const iframe = document.getElementById('pdfJsFrame');
  const embed  = document.getElementById('nativePdfEmbed');
  const dlLink = document.getElementById('downloadPdfLink');

  // Optional: if you installed PDF.js to /public/pdfjs/web/viewer.html
  const pdfJsViewerUrl = "{{ asset('pdfjs/web/viewer.html') }}";
  const usePdfJs = false; // set true only if viewer exists

  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      const pdfUrl = btn.getAttribute('data-pdf'); // now the preview route
      title.textContent = btn.getAttribute('data-title') || 'Preview';
      dlLink.href = pdfUrl;

      // Choose viewer
      if (usePdfJs) {
        iframe.src = pdfJsViewerUrl + '?file=' + encodeURIComponent(pdfUrl) + '#toolbar=0';
        iframe.style.display = 'block';
        embed.style.display  = 'none';
      } else {
        embed.src = pdfUrl + '#toolbar=0';
        embed.style.display  = 'block';
        iframe.style.display = 'none';
      }

      const modalEl = document.getElementById('pdfPreviewModal');
      modalEl.addEventListener('hidden.bs.modal', function () {
        iframe.src = '';
        embed.src  = '';
      }, { once: true });
    });
  });
});
</script>
@endpush


