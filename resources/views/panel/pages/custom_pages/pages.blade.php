@extends('panel.layouts.app')

@section('title', $page->title)

@section('content')
<section class="section">
  <div class="container">
    <h1 class="mb-3">{{ $page->title }}</h1>

    @if($page->content)
      <div class="content mb-4">
        {!! $page->content !!}
      </div>
    @endif

    @if($page->attachment)
        @php
            $url = asset($page->attachment);
            $filename = basename($page->attachment);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $imageExts = ['jpg','jpeg','png','gif','webp','bmp','svg','avif'];
            $isImage = in_array($ext, $imageExts, true);
            $isPdf = $ext === 'pdf';
        @endphp

        <div class="card mt-3">
            <div class="card-body">
            <h5 class="card-title mb-2">Attachment</h5>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <a class="text-truncate" style="max-width: 320px" href="{{ $url }}" target="_blank" title="{{ $filename }}">
                <i class="bi bi-paperclip me-1"></i> {{ $filename }}
                </a>

                {{-- Preview button (shown for images & PDF) --}}
                @if($isImage || $isPdf)
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#attachmentPreviewModal">
                    Preview
                </button>
                @endif

                {{-- Open in new tab (always available) --}}
                <a class="btn btn-sm btn-outline-secondary" href="{{ $url }}" target="_blank" rel="noopener">
                Open in new tab
                </a>

                {{-- Download (hint browser to download) --}}
                <a class="btn btn-sm btn-success" href="{{ $url }}" download="{{ $filename }}">
                Download
                </a>
            </div>

            {{-- Tiny inline thumbnail for images (optional) --}}
            @if($isImage)
                <div class="mt-3">
                <img src="{{ $url }}" alt="{{ $filename }}" class="img-fluid rounded border" style="max-height:220px;object-fit:contain">
                </div>
            @endif
            </div>
        </div>

        {{-- PREVIEW MODAL --}}
        @if($isImage || $isPdf)
            <div class="modal fade" id="attachmentPreviewModal" tabindex="-1" aria-labelledby="attachmentPreviewLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="attachmentPreviewLabel">Preview — {{ $filename }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @if($isImage)
                    <div class="ratio ratio-16x9 bg-light">
                        <img src="{{ $url }}" alt="{{ $filename }}" class="w-100 h-100" style="object-fit:contain">
                    </div>
                    @elseif($isPdf)
                    {{-- Use <iframe> for PDF preview. If the browser can't embed, user can open in new tab. --}}
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $url }}" title="PDF Preview" frameborder="0"></iframe>
                    </div>
                    <div class="p-3 text-center">
                        <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-secondary">Open full PDF</a>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success" href="{{ $url }}" download="{{ $filename }}">Download</a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
            </div>
        @endif
    @endif

  </div>
</section>
@endsection