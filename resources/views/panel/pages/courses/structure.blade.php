@extends('panel.layouts.app')

@section('title', 'Manage Course Structure')

@section('content')

<div class="container-fluid py-3" data-course-id="{{ $course->id }}">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Course: {{ $course->title }}</h4>
    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to Courses
    </a>
  </div>

  {{-- Always stack vertically --}}
  <div class="row gy-3">
    {{-- CHAPTERS --}}
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header position-sticky top-0 bg-white d-flex justify-content-between align-items-center" style="z-index:1;">
          <strong>Chapters</strong>
          <button class="btn btn-sm btn-primary" id="btnAddChapter">
            <i class="bi bi-plus-circle"></i> Add Chapter
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped align-middle" id="tblChapters" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Order</th>
                  <th>Status</th>
                  <th>Lessons</th>
                  <th>Actions</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- LESSONS --}}
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header position-sticky top-0 bg-white d-flex justify-content-between align-items-center" style="z-index:1;">
          <strong>Lessons</strong>
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted">Chapter:</span>
            <strong id="activeChapterTitle">None</strong>
            <button class="btn btn-sm btn-primary ms-2" id="btnAddLesson" disabled>
              <i class="bi bi-plus-circle"></i> Add Lesson
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped align-middle" id="tblLessons" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Type</th>
                  <th>Duration</th>
                  <th>Order</th>
                  <th>Toils</th>
                  <th>Rounds</th>
                  <th>Notes</th>
                  <th>Others</th>
                  <th>Status</th>
                  {{-- <th>Preview</th> --}}
                  <th>Actions</th>
                </tr>
              </thead>
            </table>
          </div>
          <div class="text-muted small mt-2">Select a chapter to view/manage lessons.</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Chapter Modal --}}
<div class="modal fade" id="chapterModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="chapterForm">
        @csrf
        <input type="hidden" id="chapter_id">
        <input type="hidden" id="chapter_method" value="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="chapterModalTitle">Add Chapter</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Title</label>
            <input type="text" class="form-control" id="chapter_title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Order</label>
            <input type="number" min="1" class="form-control" id="chapter_sort_order" value="1">
          </div>
          <div class="mb-3">
            <label class="form-label required">Status</label>
            <select id="chapter_status" class="form-select" required>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
          <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Lesson Modal --}}
<div class="modal fade" id="lessonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="lessonForm">
        @csrf
        <input type="hidden" id="lesson_id">
        <input type="hidden" id="lesson_method" value="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="lessonModalTitle">Add Lesson</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Title</label>
              <input type="text" id="lesson_title" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label required">Type</label>
              <select id="lesson_type" class="form-select" required>
                <option value="text">Text</option>
                <option value="video">Video</option>
                <option value="file">File</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Sort Order</label>
              <input type="number" min="1" id="lesson_sort_order" class="form-control" value="1">
            </div>

            {{-- VIDEO FIELDS (URL or upload) --}}
            <div class="col-12 d-none" id="videoFields">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label me-3">Video Source</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="video_source" id="videoSrcUrl" value="url" checked>
                    <label class="form-check-label" for="videoSrcUrl">URL</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="video_source" id="videoSrcUpload" value="upload">
                    <label class="form-check-label" for="videoSrcUpload">Upload</label>
                  </div>
                </div>

                {{-- URL mode --}}
                <div class="col-12" id="videoUrlRow">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Provider</label>
                      <select id="lesson_video_provider" class="form-select">
                        <option value="">Select</option>
                        <option value="youtube">YouTube</option>
                        <option value="vimeo">Vimeo</option>
                        <option value="local">Local</option>
                      </select>
                    </div>
                    <div class="col-md-8">
                      <label class="form-label">Video URL</label>
                      <input type="url" id="lesson_video_url" class="form-control" placeholder="https://...">
                    </div>
                  </div>
                </div>

                {{-- Upload mode --}}
                <div class="col-12 d-none" id="videoUploadRow">
                  <label class="form-label">Upload Video (mp4/webm/ogg/mov/mkv/avi)</label>
                  <div class="border rounded p-3">
                    <div id="lessonVideoDropzone" class="dropzone">
                      <div class="dz-message">Drop video here or click to upload</div>
                    </div>
                    <small class="text-muted">Max 500MB</small>
                  </div>
                </div>
              </div>
            </div>

            {{-- FILE FIELDS (for type=file) --}}
            <div class="col-12 d-none" id="fileFields">
              <label class="form-label">Upload File (pdf/image/zip etc.)</label>
              <div class="border rounded p-3">
                <div id="lessonFileDropzone" class="dropzone">
                  <div class="dz-message">Drop file here or click to upload</div>
                </div>
                <small class="text-muted">Max 10MB</small>
              </div>
            </div>

            {{-- TEXT FIELDS --}}
            <div class="col-12" id="textFields">
              <label class="form-label">Content</label>
              <textarea id="lesson_content_text" rows="5" class="form-control" placeholder="Write lesson content..."></textarea>
            </div>

            <div class="col-md-3">
              <label class="form-label">Duration (seconds)</label>
              <input type="number" min="0" id="lesson_duration" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label required">Status</label>
              <select id="lesson_status" class="form-select" required>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
            {{-- <div class="col-md-3">
              <label class="form-label required">Free Preview</label>
              <select id="lesson_preview" class="form-select" required>
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
            </div> --}}

            <div class="col-md-3">
              <label class="form-label">Toils</label>
              <input type="number" min="0" id="lesson_toils" class="form-control" placeholder="e.g., 3">
            </div>

            <div class="col-md-6">
              <label class="form-label">Rounds</label>
              <input type="text" id="lesson_rounds" class="form-control" placeholder="e.g., 12,13,14">
            </div>

            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea id="lesson_notes" rows="3" class="form-control" placeholder="Any notes for this lesson"></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Others</label>
              <input type="text" id="lesson_others" class="form-control" placeholder="Extra info">
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
          <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Preview Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewTitle">Preview</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="previewContainer" class="ratio ratio-16x9">
          {{-- content injected by JS --}}
        </div>
        <div id="previewMeta" class="small text-muted mt-2"></div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
  .card .table-responsive { overflow-x: auto; }
  #tblChapters, #tblLessons { width: 100% !important; }
</style>
@endpush

@push('scripts')
    @include('panel.pages.courses._script')
@endpush
