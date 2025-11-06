@extends('panel.layouts.app')

@section('title', 'Courses')

@section('content')
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Courses</h4>
    <button class="btn btn-primary" id="btnAdd">
      <i class="bi bi-plus-circle"></i> Add Course
    </button>
  </div>

  {{-- Filters --}}
  <div class="card mb-3 shadow-sm border-0">
    <div class="card-body">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
          <label class="form-label fw-semibold text-secondary">Search</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Course title...">
          </div>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label fw-semibold text-secondary">Status</label>
          <select name="status" class="form-select">
            <option value="">Any</option>
            <option value="1" @selected(($status ?? '')==='1')>Active</option>
            <option value="0" @selected(($status ?? '')==='0')>Inactive</option>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label fw-semibold text-secondary">From</label>
          <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? '' }}">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label fw-semibold text-secondary">To</label>
          <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? '' }}">
        </div>
        <div class="col-12 col-md-1">
          <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i> Filter</button>
        </div>
        <div class="col-12 col-md-1">
          <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  {{-- Table --}}
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:70px">#</th>
              <th style="width:80px">Image</th>
              <th>Title</th>
              <th style="width:120px">Price</th>
              <th style="width:100px">Program</th>
              <th style="width:120px">Status</th>
              <th style="width:160px">Created</th>
              <th style="width:240px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($courses as $i => $c)
              <tr>
                <td>{{ $courses->firstItem() + $i }}</td>
                <td>
                  @if($c->image)
                    <img src="{{ asset($c->image) }}" class="img-thumbnail" style="height:50px;width:70px;object-fit:cover">
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="fw-semibold">{{ $c->title }}</td>
                <td>{{ number_format((float)$c->price, 2) }} &#x062F;&#x002E;&#x0643;</td>
                <td>
                  @if($c->program == 1)
                    First Program
                  @elseif($c->program == 2)
                    Second Program
                  @elseif($c->program == 3)
                    Third Program
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  @if($c->status)
                    <span class="badge bg-success">Active</span>
                  @else
                    <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
                <td>
                  <div class="small text-muted">{{ $c->created_at->format('Y-m-d H:i') }}</div>
                </td>
                <td>
                  <a href="{{ url('admin/courses/'.$c->id.'/chapters') }}" class="btn btn-sm btn-outline-secondary me-1">
                    <i class="bi bi-diagram-3"></i> Manage
                  </a>
                  <button
                    class="btn btn-sm btn-outline-primary me-1 btn-edit"
                    data-id="{{ $c->id }}"
                    data-title="{{ e($c->title) }}"
                    data-price="{{ (float)$c->price }}"
                    data-status="{{ (int)$c->status }}"
                    data-program="{{ $c->program }}"
                    data-description="{{ e($c->description) }}"
                    data-image="{{ $c->image ? asset($c->image) : '' }}"
                  >
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger btn-delete"
                    data-id="{{ $c->id }}"
                    data-title="{{ e($c->title) }}"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  <i class="bi bi-inboxes me-1"></i> No courses found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pagination --}}
    @if($courses->hasPages())
      <div class="card-footer">
        {{ $courses->links() }}
      </div>
    @endif
  </div>
</div>

{{-- Add/Edit Modal (same as yours) --}}
<div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="courseForm">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" name="course_id" id="course_id">

        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add Course</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label required">Title</label>
              <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label required">Price (KWD)</label>
              <input type="number" step="0.01" min="0" name="price" id="price" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label required">Status</label>
              <select name="status" id="status" class="form-select" required>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label required">Program</label>
              <select name="program" id="program" class="form-select">
                <option value="">Select Program</option>
                <option value="1" @selected(old('program', $course->program) == 1)>First Program</option>
                <option value="2" @selected(old('program', $course->program) == 2)>Second Program</option>
                <option value="3" @selected(old('program', $course->program) == 3)>Third Program</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea name="description" id="description" rows="4" class="form-control" placeholder="About this course..."></textarea>
            </div>

            {{-- Dropzone --}}
            <div class="col-12">
              <label class="form-label">Image (jpg, jpeg, png, webp; max 2MB)</label>
              <div class="border rounded p-3">
                <div id="imageDropzone" class="dropzone">
                  <div class="dz-message">Drop image here or click to upload</div>
                </div>
                <small class="text-muted">Leave empty to keep existing image.</small>
              </div>
            </div>

            {{-- Current image preview --}}
            <div class="col-12 d-none" id="currentImageWrap">
              <label class="form-label">Current Image</label><br>
              <img id="currentImage" src="#" alt="Current" class="img-thumbnail" style="max-height:140px">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
          <button class="btn btn-primary" id="btnSave" type="submit">
            <i class="bi bi-save"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Delete Confirm --}}
<div class="modal fade" id="confirmDelete" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Delete Course</h6>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">Delete <strong id="delTitle"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" id="btnConfirmDelete"><i class="bi bi-trash"></i> Delete</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .table td, .table th { vertical-align: middle; }
</style>
@endpush

@push('scripts')
<script>
(() => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  const modalEl = document.getElementById('courseModal');
  const delModalEl = document.getElementById('confirmDelete');
  let dz, editingId = null, deletingId = null;

  // Dropzone (no auto upload; we pack into FormData)
  Dropzone.autoDiscover = false;
  function buildDropzone() {
    if (dz) dz.destroy();
    dz = new Dropzone("#imageDropzone", {
      url: "#",
      autoProcessQueue: false,
      maxFiles: 1,
      acceptedFiles: ".jpg,.jpeg,.png,.webp",
      addRemoveLinks: true,
      dictRemoveFile: "Remove",
      paramName: "image"
    });
    dz.on('maxfilesexceeded', function(file) {
      this.removeAllFiles();
      this.addFile(file);
    });
  }
  buildDropzone();

  // Open Add
  document.getElementById('btnAdd').addEventListener('click', () => {
    editingId = null;
    document.getElementById('courseForm').reset();
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('modalTitle').innerText = 'Add Course';
    document.getElementById('currentImageWrap').classList.add('d-none');
    dz.removeAllFiles(true);
    new bootstrap.Modal(modalEl).show();
  });

  // Edit (read data-* from button)
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {
      editingId = btn.dataset.id;
      document.getElementById('course_id').value = editingId;
      document.getElementById('title').value = btn.dataset.title || '';
      document.getElementById('price').value = (parseFloat(btn.dataset.price || '0')).toFixed(2);
      document.getElementById('status').value = (btn.dataset.status === '1') ? '1' : '0';
      document.getElementById('description').value = btn.dataset.description || '';
      document.getElementById('program').value = btn.dataset.program || '';
      document.getElementById('formMethod').value = 'PUT';
      document.getElementById('modalTitle').innerText = 'Edit Course';
      dz.removeAllFiles(true);

      const img = btn.dataset.image || '';
      if (img) {
        document.getElementById('currentImage').src = img;
        document.getElementById('currentImageWrap').classList.remove('d-none');
      } else {
        document.getElementById('currentImageWrap').classList.add('d-none');
      }

      new bootstrap.Modal(modalEl).show();
    });
  });

  // Delete open
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
      deletingId = btn.dataset.id;
      document.getElementById('delTitle').innerText = btn.dataset.title || '';
      new bootstrap.Modal(delModalEl).show();
    });
  });

  // Confirm Delete
  document.getElementById('btnConfirmDelete').addEventListener('click', async () => {
    if (!deletingId) return;
    try {
      const res = await fetch(`{{ url('admin/courses') }}/${deletingId}`, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {
          'X-CSRF-TOKEN': token,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });
      if (!res.ok) throw new Error('Failed');
      bootstrap.Modal.getInstance(delModalEl).hide();
      // Simple refresh to show updated list
      location.reload();
    } catch (err) {
      alert('Delete failed');
    } finally {
      deletingId = null;
    }
  });

  // Save (Add/Update)
  document.getElementById('courseForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const fd = new FormData();
    fd.append('title', document.getElementById('title').value.trim());
    fd.append('price', document.getElementById('price').value || '0');
    fd.append('status', document.getElementById('status').value);
    fd.append('description', document.getElementById('description').value.trim());
    fd.append('program', document.getElementById('program').value);

    if (dz.getAcceptedFiles().length) {
      fd.append('image', dz.getAcceptedFiles()[0], dz.getAcceptedFiles()[0].name);
    }

    const method = document.getElementById('formMethod').value;
    const url = method === 'PUT'
      ? `{{ url('admin/courses') }}/${document.getElementById('course_id').value}`
      : `{{ route('admin.courses.store') }}`;

    try {
      const res = await fetch(url, {
        method: 'POST', // use _method for PUT
        credentials: 'same-origin',
        headers: {
          'X-CSRF-TOKEN': token,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: (() => { if (method==='PUT') fd.append('_method','PUT'); return fd; })()
      });

      if (!res.ok) {
        const data = await res.json().catch(()=>({}));
        let msg = 'Save failed';
        if (data?.errors) msg = Object.values(data.errors).flat().join('\n');
        throw new Error(msg);
      }

      bootstrap.Modal.getInstance(modalEl).hide();
      // Reload to reflect filters + new data
      location.reload();

    } catch (err) {
      alert(err.message || 'Save failed');
    }
  });

})();
</script>
@endpush
