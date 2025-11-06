@extends('panel.layouts.app')

@section('title', 'Students & Activity')

@push('styles')
<style>
  .rotate-90 { transform: rotate(90deg); }
  .detail-cell { background: #fbfcfe; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h4 class="mb-0 fw-bold text-primary">
        <i class="bi bi-people me-2"></i> Students & Activity
      </h4>
      <div class="small text-muted mt-1">Monitor student progress and activity</div>
    </div>
    <form method="GET" class="d-flex align-items-center gap-2">
      <div class="input-group">
        <input type="text" class="form-control" name="q" placeholder="Search name or email…" value="{{ $q ?? '' }}">
        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
      </div>
      @if(!empty($q))
        <a class="btn btn-outline-secondary" href="{{ route('admin.students.page') }}"><i class="bi bi-x-lg"></i> Reset</a>
      @endif
    </form>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="students-table">
          <thead class="table-light">
            <tr>
              <th style="width:56px"></th>
              <th>Student</th>
              <th>Email</th>
              <th class="text-center" style="width:140px">Enrollments</th>
              <th class="text-center" style="width:160px">Avg Progress</th>
              <th style="width:160px">Actions</th>
            </tr>
          </thead>
          <tbody>
          @forelse($students as $st)
            {{-- HEAD ROW --}}
            <tr>
              <td>
                <button
                  class="btn btn-sm btn-outline-secondary"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#st-{{ $st->id }}"
                  aria-expanded="false"
                  aria-controls="st-{{ $st->id }}"
                >
                  <i class="bi bi-chevron-right"></i>
                </button>
              </td>
              <td class="fw-semibold">{{ $st->name }}</td>
              <td class="text-muted">{{ $st->email }}</td>
              <td class="text-center">
                <span class="badge bg-light text-dark">{{ $st->enrollments_count }}</span>
              </td>
              <td class="text-center">
                @php $avg = $st->avg_progress_percent ? round($st->avg_progress_percent,1) : 0; @endphp
                <div class="small fw-semibold mb-1">{{ $avg }}%</div>
                <div class="progress" style="height:6px">
                  <div class="progress-bar" style="width: {{ $avg }}%"></div>
                </div>
              </td>
              <td>
                <button
                  class="btn btn-sm btn-primary"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#st-{{ $st->id }}"
                  aria-expanded="false"
                  aria-controls="st-{{ $st->id }}"
                >
                  View Activity
                </button>
              </td>
            </tr>

            {{-- COLLAPSE ROW (AJAX target) --}}
            <tr class="collapse" id="st-{{ $st->id }}" data-student-id="{{ $st->id }}">
              <td></td>
              <td colspan="5" class="detail-cell">
                <div class="py-3" data-detail-body>
                  <div class="text-center text-muted small">Loading…</div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-inboxes me-1"></i> No students found.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($students->hasPages())
      <div class="card-footer">
        {{ $students->links() }}
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  console.log("✅ Activity script loaded");

  const table = document.getElementById('students-table');
  if (!table) return;

  // Build absolute URL safely (avoids route-name issues on some setups)
  const urlBase = "{{ url('admin/students/activity') }}";

  // Rotate chevron on show/hide
  table.addEventListener('show.bs.collapse', (ev) => {
    const tr = ev.target.closest('tr');
    const head = tr?.previousElementSibling;
    head?.querySelectorAll('i.bi-chevron-right').forEach(i => i.classList.add('rotate-90'));
  });
  table.addEventListener('hide.bs.collapse', (ev) => {
    const tr = ev.target.closest('tr');
    const head = tr?.previousElementSibling;
    head?.querySelectorAll('i.bi-chevron-right').forEach(i => i.classList.remove('rotate-90'));
  });

  // Lazy-load content once, when the row is first expanded
  table.addEventListener('show.bs.collapse', async (ev) => {
    const tr = ev.target.closest('tr');                  // the collapse row
    if (!tr || tr.dataset.loaded) return;                // already loaded

    const userId = tr.getAttribute('data-student-id');
    const body   = tr.querySelector('[data-detail-body]');
    if (!userId || !body) return;

    try {
      const res = await fetch(`${urlBase}/${encodeURIComponent(userId)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const html = await res.text();
      body.innerHTML = html;
      tr.dataset.loaded = '1';
    } catch (err) {
      body.innerHTML = `<div class="alert alert-danger mb-0">Failed to load activity. ${err?.message ?? ''}</div>`;
    }
  });
})();
</script>
@endpush
