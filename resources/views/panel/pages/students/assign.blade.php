@extends('panel.layouts.app')

@section('title', 'Assign Students to Courses')

@push('styles')
<style>
  .card + .card { margin-top: 1rem; }
  #tblEnrollments tbody tr:hover { background: #fafafa; }
  .filters .form-select, .filters .form-control { min-width: 160px; }
  .filters .vr { width:1px; background:#e5e7eb; min-height:32px; }
  .searchable-select { position: relative; }
  .searchable-select .dropdown { position:absolute; z-index:10; left:0; right:0; max-height:220px; overflow:auto; border:1px solid #dee2e6; border-top:none; background:#fff; display:none; }
  .searchable-select .dropdown button { display:block; width:100%; text-align:left; padding:.5rem .75rem; border:0; background:#fff; }
  .searchable-select .dropdown button:hover { background:#f8f9fa; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Assign Students</h4>
    {{-- <div class="d-flex gap-2">
      <a class="btn btn-sm btn-outline-secondary"
         href="{{ route('admin.enrollments.export', request()->query()) }}">
        Export CSV
      </a>
    </div> --}}
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <div class="row g-3">
    {{-- Single assign (AJAX searchable selects) --}}
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header"><strong>Assign one student</strong></div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.enrollments.store') }}" id="singleAssignForm">
            @csrf

            <div class="mb-3">
              <label class="form-label">Course</label>
              <div class="searchable-select" data-endpoint="{{ route('admin.courses.ajaxSearch') }}">
                <input type="text" class="form-control" placeholder="Search course…" autocomplete="off">
                <div class="dropdown"></div>
                <input type="hidden" name="course_id">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Student</label>
              <div class="searchable-select" data-endpoint="{{ route('admin.students.ajaxSearch') }}">
                <input type="text" class="form-control" placeholder="Search student (name/email)…" autocomplete="off">
                <div class="dropdown"></div>
                <input type="hidden" name="user_id">
              </div>
            </div>

            <button class="btn btn-primary">Assign</button>
          </form>
        </div>
      </div>
    </div>

    {{-- Bulk assign --}}
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Bulk assign</strong>
          <small class="text-muted">Paste user IDs or emails</small>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.enrollments.bulkStore') }}" id="bulkAssignForm">
            @csrf

            <div class="mb-3">
              <label class="form-label">Course</label>
              <div class="searchable-select" data-endpoint="{{ route('admin.courses.ajaxSearch') }}">
                <input type="text" class="form-control" placeholder="Search course…" autocomplete="off">
                <div class="dropdown"></div>
                <input type="hidden" name="course_id">
              </div>
            </div>

            <div class="mb-2">
              <label class="form-label">Students (one per line)</label>
              <textarea class="form-control" rows="6" placeholder="e.g. 13&#10;jane@example.com&#10;42" id="bulkStudents"></textarea>
              <div class="form-text">You can mix user IDs and emails.</div>
            </div>

            <input type="hidden" name="user_ids[]" id="bulkUserIds"> {{-- will be filled via JS as multiple --}}

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-primary" id="bulkPreviewBtn">Preview</button>
              <button class="btn btn-primary d-none" id="bulkConfirmBtn">Confirm & Assign</button>
            </div>

            <div id="bulkPreview" class="mt-3 d-none">
              <h6 class="mb-2">Preview</h6>
              <ul class="list-group small" id="bulkPreviewList"></ul>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Filters + table --}}
  <div class="row g-3 mt-1">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center justify-content-between">
            <strong>Current Enrollments</strong>
          </div>

          <form method="GET" class="mt-3 filters">
            <div class="row g-2 align-items-end">
              <div class="col-auto">
                <label class="form-label mb-1">Course</label>
                <select name="course_id" class="form-select form-select-sm">
                  <option value="">All</option>
                  @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ (string)($filters['course_id'] ?? '') === (string)$c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-auto">
                <label class="form-label mb-1">Status</label>
                @php $status = $filters['status'] ?? 'all'; @endphp
                <select name="status" class="form-select form-select-sm">
                  <option value="all" {{ $status==='all' || $status==='' ? 'selected' : '' }}>All</option>
                  <option value="in-progress" {{ $status==='in-progress' ? 'selected' : '' }}>In Progress</option>
                  <option value="completed" {{ $status==='completed' ? 'selected' : '' }}>Completed</option>
                </select>
              </div>
              {{-- <div class="col-auto">
                <label class="form-label mb-1">Assigned by</label>
                @php $asb = $filters['assigned_by'] ?? 'any'; @endphp
                <select name="assigned_by" class="form-select form-select-sm">
                  <option value="any"  {{ $asb==='any' || $asb==='' ? 'selected' : '' }}>Anyone</option>
                  <option value="me"   {{ $asb==='me'   ? 'selected' : '' }}>Me</option>
                  <option value="none" {{ $asb==='none' ? 'selected' : '' }}>No one</option>
                  @foreach($admins as $a)
                    <option value="{{ $a->id }}" {{ (string)$asb === (string)$a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                  @endforeach
                </select>
              </div> --}}
              {{-- <div class="col-auto">
                <label class="form-label mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ optional($filters['from'] ?? null)->format('Y-m-d') }}">
              </div>
              <div class="col-auto">
                <label class="form-label mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ optional($filters['to'] ?? null)->format('Y-m-d') }}">
              </div> --}}
              <div class="col flex-grow-1">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Course, student, or admin…" value="{{ $filters['q'] ?? '' }}">
              </div>
              <div class="col-auto">
                <button class="btn btn-sm btn-primary w-100">Apply</button>
              </div>
              <div class="col-auto">
                <a class="btn btn-sm btn-outline-secondary w-100" href="{{ route('admin.enrollments.assign') }}">Reset</a>
              </div>
            </div>
          </form>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle" id="tblEnrollments">
              <thead>
                <tr>
                  <th style="width:56px">#</th>
                  <th>Course</th>
                  <th>Student</th>
                  <th style="min-width:140px">Assigned By</th>
                  <th style="min-width:140px">Enrolled</th>
                  <th style="min-width:180px">Progress</th>
                  <th style="min-width:120px">Status</th>
                  <th style="width:110px">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($enrollments as $i => $e)
                  <tr>
                    <td>{{ ($enrollments->firstItem() ?? 0) + $i }}</td>
                    <td>{{ $e->course?->title }}</td>
                    <td>
                      <div class="fw-semibold">{{ $e->user?->name }}</div>
                      <div class="small text-muted">{{ $e->user?->email }}</div>
                    </td>
                    <td>{{ $e->assignedBy?->name ?? '—' }}</td>
                    <td>{{ optional($e->enrolled_at)->format('Y-m-d H:i') ?? '—' }}</td>
                    <td>
                      @php $p = number_format($e->progress_percent ?? 0, 1); @endphp
                      <div class="progress" style="height:10px;max-width:180px">
                        <div class="progress-bar" role="progressbar" style="width: {{ $p }}%"></div>
                      </div>
                      <div class="small text-muted mt-1">{{ $p }}%</div>
                    </td>
                    <td>
                      @if($e->completed_at)
                        <span class="badge bg-success">Completed</span>
                      @else
                        <span class="badge bg-info text-dark">In Progress</span>
                      @endif
                    </td>
                    <td>
                      <form method="POST" action="{{ route('admin.enrollments.destroy', $e->id) }}"
                            onsubmit="return confirm('Remove this enrollment?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="8" class="text-center text-muted">No enrollments found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="small text-muted">
              Showing {{ $enrollments->firstItem() ?? 0 }}–{{ $enrollments->lastItem() ?? 0 }}
              of {{ $enrollments->total() }} results
            </div>
            <div>{{ $enrollments->onEachSide(1)->links() }}</div>
          </div>

          {{-- <div class="mt-3">
            <div class="input-group input-group-sm" style="max-width: 320px;">
              <span class="input-group-text">Quick filter</span>
              <input type="text" id="enrollFilter" class="form-control" placeholder="Type to filter this page…">
              <button class="btn btn-outline-secondary" type="button" id="enrollFilterClear">Clear</button>
            </div>
          </div> --}}

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
/** Simple dropdown search (no external plugin) */
function bindSearchableSelect(root){
  const input = root.querySelector('input[type="text"]');
  const hidden = root.querySelector('input[type="hidden"]');
  const dd = root.querySelector('.dropdown');
  const endpoint = root.dataset.endpoint;
  let controller = null, timer = null;

  input.addEventListener('input', () => {
    const q = input.value.trim();
    hidden.value = '';
    if (timer) clearTimeout(timer);
    if (!q) { dd.style.display='none'; dd.innerHTML=''; return; }

    timer = setTimeout(async () => {
      try {
        if (controller) controller.abort();
        controller = new AbortController();
        const url = endpoint + '?q=' + encodeURIComponent(q);
        const res = await fetch(url, { signal: controller.signal, headers: {'X-Requested-With': 'XMLHttpRequest'} });
        if (!res.ok) return;
        const data = await res.json();
        dd.innerHTML = '';
        (data.results || []).forEach(opt => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.textContent = opt.text;
          btn.addEventListener('click', () => {
            input.value = opt.text;
            hidden.value = opt.id;
            dd.style.display='none';
          });
          dd.appendChild(btn);
        });
        dd.style.display = dd.children.length ? 'block' : 'none';
      } catch(e){}
    }, 200);
  });

  document.addEventListener('click', (ev)=>{
    if (!root.contains(ev.target)) { dd.style.display='none'; }
  });
}

document.querySelectorAll('.searchable-select').forEach(bindSearchableSelect);

/** Bulk preview -> resolves IDs from IDs/emails server-side (simple) */
document.getElementById('bulkPreviewBtn')?.addEventListener('click', async function(){
  const lines = (document.getElementById('bulkStudents').value || '').split(/\r?\n/).map(s=>s.trim()).filter(Boolean);
  if (!lines.length) { alert('Please enter at least one ID or email.'); return; }

  // Minimal resolver: POST to a tiny endpoint OR resolve here by calling /students/search per chunk.
  // For simplicity, we’ll call a small batched search by email/ids using a single request.
  // If you want, create a dedicated endpoint. For demo, we’ll do a naive approach:
  const unique = [...new Set(lines)];
  const previewList = document.getElementById('bulkPreviewList');
  previewList.innerHTML = '';
  const resolved = [];
  const notFound = [];

  // Try to detect numeric IDs vs emails and build a “results” row
  // (In production, replace with your own resolver endpoint for accuracy.)
  for (const token of unique) {
    if (/^\d+$/.test(token)) {
      // Assume it is a user id; we’ll accept as-is and show placeholder label
      resolved.push({id: parseInt(token,10), label:`User #${token}`});
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between align-items-center';
      li.textContent = `#${token}`;
      const badge = document.createElement('span');
      badge.className = 'badge bg-secondary rounded-pill';
      badge.textContent = 'ID';
      li.appendChild(badge);
      previewList.appendChild(li);
    } else if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(token)) {
      // For emails, try to resolve via search endpoint
      const url = "{{ route('admin.students.ajaxSearch') }}" + '?q=' + encodeURIComponent(token);
      try {
        const res = await fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} });
        const data = await res.json();
        const match = (data.results || []).find(r => r.text.includes(token));
        if (match) {
          resolved.push({id: match.id, label: match.text});
          const li = document.createElement('li');
          li.className = 'list-group-item d-flex justify-content-between align-items-center';
          li.textContent = match.text;
          const badge = document.createElement('span');
          badge.className = 'badge bg-success rounded-pill';
          badge.textContent = 'OK';
          li.appendChild(badge);
          previewList.appendChild(li);
        } else {
          notFound.push(token);
        }
      } catch(e){ notFound.push(token); }
    } else {
      notFound.push(token);
    }
  }

  if (notFound.length) {
    const li = document.createElement('li');
    li.className = 'list-group-item list-group-item-warning';
    li.textContent = 'Not found / invalid: ' + notFound.join(', ');
    previewList.appendChild(li);
  }

  // Fill hidden inputs for confirmed submit
  const bulkForm = document.getElementById('bulkAssignForm');
  // Remove old inputs
  bulkForm.querySelectorAll('input[name="user_ids[]"]').forEach(el => el.remove());
  resolved.forEach(u => {
    const inp = document.createElement('input');
    inp.type='hidden'; inp.name='user_ids[]'; inp.value = u.id;
    bulkForm.appendChild(inp);
  });

  document.getElementById('bulkPreview').classList.remove('d-none');
  document.getElementById('bulkConfirmBtn').classList.toggle('d-none', resolved.length===0);
});

document.getElementById('bulkConfirmBtn')?.addEventListener('click', function(){
  document.getElementById('bulkAssignForm').submit();
});

/** Quick client-side filter */
(function(){
  const qInput = document.getElementById('enrollFilter');
  const qClear = document.getElementById('enrollFilterClear');
  const rows   = () => document.querySelectorAll('#tblEnrollments tbody tr');
  function applyFilter() {
    const q = (qInput?.value || '').toLowerCase();
    rows().forEach(tr => {
      const text = tr.innerText.toLowerCase();
      tr.style.display = text.includes(q) ? '' : 'none';
    });
  }
  qInput?.addEventListener('input', applyFilter);
  qClear?.addEventListener('click', () => { qInput.value=''; applyFilter(); });
})();
</script>
@endpush
