<style>
    .book-card .card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.book-card .card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.book-card img {
  border-bottom: 1px solid #f0f0f0;
}
</style>

@foreach($books as $book)
  <div class="col-6 col-md-4 col-lg-3 book-card mb-4">
    <div class="card h-100 shadow-sm border-0 rounded-3">
      
      {{-- Cover --}}
      @if($book->cover_path)
        <img src="{{ asset($book->cover_path) }}"
             alt="{{ $book->title }}"
             class="card-img-top rounded-top-3"
             style="height:200px; object-fit:cover;">
      @endif

      {{-- Content --}}
      <div class="card-body d-flex flex-column p-3">
        <h6 class="fw-semibold mb-1 text-truncate" title="{{ $book->title }}">
          {{ $book->title }}
        </h6>
        @if($book->author)
          <small class="text-muted d-block mb-1">{{ $book->author }}</small>
        @endif

        <p class="small text-muted mb-2" style="font-size: 0.82rem;">
          {{ \Illuminate\Support\Str::limit($book->description, 60) }}
        </p>

        <span class="badge bg-primary align-self-start mb-2">
          ${{ number_format($book->price, 2) }}
        </span>

        {{-- Actions --}}
        <div class="mt-auto">
          @guest
            <a href="{{ route('login') }}" class="btn btn-sm btn-primary w-100 rounded-pill mt-2">
              Sign in to Buy
            </a>
          @else
            @php
              $has = $book->purchases()
                    ->where('user_id', auth()->id())
                    ->where('status','paid')
                    ->exists();
            @endphp

            @unless($has)
              <form action="{{ route('books.buy', $book->slug) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary w-100 rounded-pill mt-2">
                  <i class="fa fa-credit-card"></i> Buy
                </button>
              </form>
            @endunless

            @if($has)
              <button
                class="btn btn-sm btn-primary w-100 rounded-pill mt-2 preview-pdf-btn"
                data-pdf="{{ route('books.preview', $book->slug) }}"
                data-title="{{ $book->title }}"
                data-bs-toggle="modal"
                data-bs-target="#pdfPreviewModal">
                Preview
              </button>

              <a href="{{ route('books.download', $book->slug) }}"
                 class="btn btn-sm btn-outline-secondary w-100 rounded-pill mt-2">
                Download
              </a>
            @endif
          @endguest
        </div>
      </div>
    </div>
  </div>
@endforeach
