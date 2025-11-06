@extends('panel.layouts.app')

@push('styles')
<style>
/* Scoped */
#admin-dashboard .stat-card .amount{font-weight:800;font-size:1.35rem}
#admin-dashboard .stat-card .sub{color:#8094ae;font-size:.875rem}

#student-dashboard .course-card{border-radius:14px;overflow:hidden}
#student-dashboard .thumb{aspect-ratio:16/9;background:#f5f7fb;overflow:hidden}
#student-dashboard .thumb img{width:100%;height:100%;object-fit:cover;display:block}
#student-dashboard .price{font-weight:700;color:#1e90ff}
#student-dashboard .badge-free{background:#1e90ff;color:#fff;border-radius:4px;padding:.15rem .4rem}
#student-dashboard .progress-wrap{font-size:.875rem;color:#8094ae}
</style>
@endpush

@section('content')

{{-- Flash alerts --}}
@if(session('success'))
  <div class="alert alert-success alert-icon alert-dismissible fade show">
    <em class="icon ni ni-check-circle"></em>
    <strong>{{ session('success') }}</strong>
    <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
      <em class="icon ni ni-cross"></em>
    </button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-icon alert-dismissible fade show">
    <em class="icon ni ni-alert-circle"></em>
    <strong>{{ session('error') }}</strong>
    <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
      <em class="icon ni ni-cross"></em>
    </button>
  </div>
@endif

@if($errors->any())
  <div class="alert alert-danger alert-icon alert-dismissible fade show">
    <em class="icon ni ni-alert-circle"></em>
    <strong>{{ $errors->first() }}</strong>
    <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
      <em class="icon ni ni-cross"></em>
    </button>
  </div>
@endif

  @if($isAdmin ?? false)
  {{-- ===================== ADMIN VIEW ===================== --}}
  <div id="admin-dashboard">
    <div class="nk-block-head nk-block-head-sm">
      <div class="nk-block-between">
        <div class="nk-block-head-content">
          <h3 class="nk-block-title page-title">Admin Dashboard</h3>
          <div class="nk-block-des text-soft"><p>Overview at a glance.</p></div>
        </div>
      </div>
    </div>

    {{-- Stats --}}
    <div class="nk-block">
      <div class="row g-gs">
        <div class="col-6 col-md-3">
          <div class="card card-bordered stat-card">
            <div class="card-inner">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <div class="amount">{{ number_format($totalCourses) }}</div>
                  <div class="sub">Courses</div>
                </div>
                <em class="icon ni ni-book-fill"></em>
              </div>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-3">
          <div class="card card-bordered stat-card">
            <div class="card-inner">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <div class="amount">{{ number_format($totalStudents) }}</div>
                  <div class="sub">Students</div>
                </div>
                <em class="icon ni ni-users-fill"></em>
              </div>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-3">
          <div class="card card-bordered stat-card">
            <div class="card-inner">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <div class="amount">{{ number_format($totalEnrollments) }}</div>
                  <div class="sub">Enrollments</div>
                </div>
                <em class="icon ni ni-growth-fill"></em>
              </div>
            </div>
          </div>
        </div>

        <div class="col-6 col-md-3">
          <div class="card card-bordered stat-card">
            <div class="card-inner">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <div class="amount">{{ number_format($revenue, 2) }} &#x062F;&#x002E;&#x0643;</div>
                  <div class="sub">Est. Revenue</div>
                </div>
                <em class="icon ni ni-coin"></em>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Latest Enrollments --}}
      <div class="row g-gs mt-1">
        <div class="col-lg-7">
          <div class="card card-bordered h-100">
            <div class="card-inner">
              <div class="card-title mb-3"><h6 class="title">Latest Enrollments</h6></div>
              <div class="nk-tb-list nk-tb-ulist">
                <div class="nk-tb-item nk-tb-head">
                  <div class="nk-tb-col"><span>Student</span></div>
                  <div class="nk-tb-col tb-col-md"><span>Course</span></div>
                  <div class="nk-tb-col tb-col-md"><span>Price</span></div>
                  <div class="nk-tb-col tb-col-md"><span>Enrolled</span></div>
                </div>
                @forelse($latestEnrollments as $e)
                  @php
                    $c = $e->course; $u = $e->user;
                    $p = is_numeric($c->price ?? null) ? (float)$c->price : 0;
                  @endphp
                  <div class="nk-tb-item">
                    <div class="nk-tb-col">
                      <div class="user-card">
                        <div class="user-info">
                          <span class="tb-lead">{{ $u->name ?? 'Unknown' }}</span>
                          <span>{{ $u->email ?? '' }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="nk-tb-col tb-col-md"><span>{{ $c->title ?? 'Deleted Course' }}</span></div>
                    <div class="nk-tb-col tb-col-md">
                      @if($p>0) {{ number_format($p,2) }} &#x062F;&#x002E;&#x0643; @else <span class="badge badge-dim badge-primary">Free</span> @endif
                    </div>
                    <div class="nk-tb-col tb-col-md"><span>{{ optional($e->enrolled_at ?? $e->created_at)->format('d M, Y H:i') }}</span></div>
                  </div>
                @empty
                  <div class="alert alert-light">No recent enrollments.</div>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        {{-- Popular Courses --}}
        <div class="col-lg-5">
          <div class="card card-bordered h-100">
            <div class="card-inner">
              <div class="card-title mb-3"><h6 class="title">Popular Courses</h6></div>
              <div class="row g-gs">
                @forelse($popularCourses as $pc)
                  @php
                    $img = $pc->image ? asset($pc->image) : asset('training-studio/assets/images/first-trainer.jpg');
                    $count = $pc->enrollments_count ?? 0;
                  @endphp
                  <div class="col-sm-6">
                    <div class="card card-bordered h-100">
                      <a href="{{ route('courses.show', $pc->slug) }}" class="ratio ratio-16x9">
                        <img src="{{ $img }}" alt="{{ e($pc->title) }}" style="object-fit:cover;">
                      </a>
                      <div class="card-inner">
                        <h6 class="mb-1 text-truncate"><a href="{{ route('courses.show', $pc->slug) }}">{{ $pc->title }}</a></h6>
                        <div class="text-soft small">{{ $count }} enrollments</div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="col-12"><div class="alert alert-light">No data.</div></div>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  @else
  {{-- ===================== STUDENT VIEW ===================== --}}
  <div id="student-dashboard">

    <div class="nk-block-head nk-block-head-sm">
      <div class="nk-block-between">
        <div class="nk-block-head-content">
          <h3 class="nk-block-title page-title">My Enrolled Courses</h3>
          <div class="nk-block-des text-soft">
            <p>All courses you’ve enrolled in appear here.</p>
          </div>
        </div>
        <div class="nk-block-head-content">
          <form method="get" class="d-flex align-items-center gap-2">
            <div class="form-control-wrap">
              <div class="form-icon form-icon-left">
                <em class="icon ni ni-search"></em>
              </div>
              <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Search courses">
            </div>
            <button class="btn btn-primary"><em class="icon ni ni-filter-alt"></em><span>Filter</span></button>
          </form>
        </div>
      </div>
    </div>

    <div class="nk-block">
      @if($enrollments->count() === 0)
        <div class="alert alert-info">
          You haven’t enrolled in any course yet.
          <a class="alert-link" href="{{ route('courses.list') }}">Browse courses</a>
        </div>
      @else
        <div class="row g-gs">
          @foreach ($enrollments as $enrollment)
            @php
              $c = $enrollment->course;
              if (!$c) continue;
              $img = $c->image ? asset($c->image) : asset('training-studio/assets/images/first-trainer.jpg');
              $price = is_numeric($c->price ?? null) ? (float)$c->price : 0;
              $progress = is_numeric($enrollment->progress_percent ?? null) ? (float)$enrollment->progress_percent : 0;
              $active = (bool)($c->status ?? 1);
            @endphp

            <div class="col-12 col-sm-6 col-lg-4">
              <div class="card card-bordered course-card h-100">
                <a href="{{ route('courses.show', $c->slug) }}" class="thumb">
                  <img src="{{ $img }}" alt="{{ e($c->title) }}">
                </a>
                <div class="card-inner">
                  <div class="d-flex align-items-start justify-content-between">
                    <h6 class="title mb-1">
                      <a class="text-dark" href="{{ route('courses.show', $c->slug) }}">{{ $c->title }}</a>
                    </h6>
                    <span class="badge {{ $active ? 'badge-success' : 'badge-dim badge-danger' }}">
                      {{ $active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>

                  <div class="d-flex align-items-center justify-content-between mt-1">
                    <div class="price">
                      @if($price > 0)
                        {{ number_format($price, 2) }} &#x062F;&#x002E;&#x0643;
                      @else
                        <span class="badge-free">Free</span>
                      @endif
                    </div>
                    <div class="text-soft small">
                      Enrolled: {{ optional($enrollment->enrolled_at ?? $enrollment->created_at)->format('d M, Y') }}
                    </div>
                  </div>

                  <div class="progress-wrap mt-2">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span>Progress</span>
                      <span>{{ number_format($progress, 0) }}%</span>
                    </div>
                    <div class="progress progress-md">
                      <div class="progress-bar" data-progress="{{ $progress }}" style="width: {{ $progress }}%;"></div>
                    </div>
                  </div>

                  <div class="mt-3 d-flex justify-content-between">
                    <a href="{{ route('courses.show', $c->slug) }}" class="btn btn-outline-primary btn-sm">
                      <em class="icon ni ni-eye"></em><span>Details</span>
                    </a>

                    @if($active)
                      <a href="{{ route('learn.course', $c->id) }}" class="btn btn-primary btn-sm">
                        <em class="icon ni ni-play"></em><span>Continue</span>
                      </a>
                    @else
                      <button class="btn btn-dim btn-secondary btn-sm" disabled>
                        <em class="icon ni ni-na"></em><span>Unavailable</span>
                      </button>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="mt-4">
          {{ $enrollments->links() }}
        </div>
      @endif
    </div>
  </div>
  @endif
@endsection
