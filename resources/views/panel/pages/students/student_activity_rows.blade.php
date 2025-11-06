{{-- AJAX partial: no @extends --}}
<div class="row g-3">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <div class="fw-semibold">{{ $user->name }}</div>
        <div class="small text-muted">{{ $user->email }}</div>
      </div>
      <span class="badge bg-light text-dark">{{ $enrollments->count() }} enrollments</span>
    </div>
  </div>

  <div class="col-12">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-secondary">
          <tr>
            <th>Course</th>
            <th class="text-center" style="width:120px">Progress</th>
            <th class="text-center" style="width:140px">Lessons</th>
            <th class="text-center" style="width:140px">Position</th>
            <th style="width:160px">Enrolled</th>
            <th style="width:160px">Completed</th>
          </tr>
        </thead>
        <tbody>
          @forelse($enrollments as $en)
            @php
              $progress = (int) ($en->progress_percent ?? 0);
              $totalLessons = (int) ($en->course->lessons_count ?? 0);
              $completedLessons = (int) ($en->completed_lessons_count ?? 0);
              $pos = (int) ($en->last_position_seconds ?? 0);
              $h = floor($pos/3600); $m = floor(($pos%3600)/60); $s = $pos%60;
              $hhmmss = sprintf('%02d:%02d:%02d',$h,$m,$s);
            @endphp
            <tr>
              <td class="fw-semibold">{{ $en->course->title }}</td>
              <td class="text-center">
                <div class="small fw-semibold">{{ $progress }}%</div>
                <div class="progress" style="height:6px">
                  <div class="progress-bar @if($progress>=100) bg-success @elseif($progress>=50) bg-info @else bg-secondary @endif" style="width: {{ $progress }}%"></div>
                </div>
              </td>
              <td class="text-center">
                <span class="badge bg-light text-dark">{{ $completedLessons }} / {{ $totalLessons }}</span>
              </td>
              <td class="text-center">
                <span class="badge bg-secondary-subtle text-dark border">{{ $hhmmss }}</span>
              </td>
              <td>
                <div class="small text-muted">{{ optional($en->enrolled_at)->format('M d, Y') ?? '—' }}</div>
                <div class="small">{{ optional($en->enrolled_at)->diffForHumans() ?? '' }}</div>
              </td>
              <td>
                @if($en->completed_at)
                  <span class="badge bg-success-subtle text-success border">{{ $en->completed_at->format('M d, Y') }}</span>
                @else
                  <span class="badge bg-warning-subtle text-warning border">In Progress</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-3">No enrollments yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-body py-2">
        <div class="small fw-semibold mb-2">Recent Lesson Completions</div>
        @if($recentCompletions->isEmpty())
          <div class="small text-muted">No recent completions.</div>
        @else
          <ul class="list-unstyled mb-0">
            @foreach($recentCompletions as $rc)
              <li class="d-flex justify-content-between border-bottom py-1">
                <span class="text-truncate-2" style="max-width:70%">{{ $rc->course_title }}</span>
                <span class="small text-muted">{{ \Illuminate\Support\Carbon::parse($rc->completed_at)->diffForHumans() }}</span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>
