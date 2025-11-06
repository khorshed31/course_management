<div class="nk-sidebar nk-sidebar-fixed" data-content="sidebarMenu">
  <div class="nk-sidebar-element nk-sidebar-head">
    <div class="nk-sidebar-brand">
      <a href="{{ url('/') }}" class="logo-link nk-sidebar-logo">
        {{-- ✅ Dynamic logo (fallback to site_name text) --}}
        @if(setting('logo'))
          <img class="logo-light logo-img" src="{{ asset(setting('logo')) }}" alt="{{ setting('site_name', 'LMS') }}">
          <img class="logo-dark  logo-img" src="{{ asset(setting('logo')) }}" alt="{{ setting('site_name', 'LMS') }}">
        @else
          <span class="fw-bold fs-5">{{ setting('site_name', 'LMS') }}</span>
        @endif
      </a>
    </div>

    <div class="nk-menu-trigger me-n2">
      <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu">
        <em class="icon ni ni-arrow-left"></em>
      </a>
      <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu">
        <em class="icon ni ni-menu"></em>
      </a>
    </div>
  </div>

  <div class="nk-sidebar-element">
    <div class="nk-sidebar-content">
      <div class="nk-sidebar-menu" data-simplebar>
        <ul class="nk-menu">

          {{-- Dashboard --}}
          <li class="nk-menu-item">
            <a href="{{ route('home') }}" class="nk-menu-link">
              <span class="nk-menu-icon"><em class="icon ni ni-dashboard-fill"></em></span>
              <span class="nk-menu-text">Dashboard</span>
            </a>
          </li>

          {{-- === Group: LMS Management === --}}
          <li class="nk-menu-heading">
            <h6 class="overline-title text-primary-alt">LMS Management</h6>
          </li>

          @if (isAdmin() ?? false)
            {{-- ===================== ADMIN VIEW ===================== --}}
            <li class="nk-menu-item has-sub">
              <a href="#" class="nk-menu-link nk-menu-toggle">
                <span class="nk-menu-icon"><em class="icon ni ni-book-fill"></em></span>
                <span class="nk-menu-text">Courses</span>
              </a>
              <ul class="nk-menu-sub">
                <li class="nk-menu-item">
                  <a href="{{ route('admin.courses.index') }}" class="nk-menu-link">
                    <span class="nk-menu-text">Course List</span>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nk-menu-item has-sub">
              <a href="#" class="nk-menu-link nk-menu-toggle">
                <span class="nk-menu-icon"><em class="icon ni ni-users-fill"></em></span>
                <span class="nk-menu-text">Students</span>
              </a>
              <ul class="nk-menu-sub">
                <li class="nk-menu-item">
                  <a href="{{ route('admin.students.page') }}" class="nk-menu-link">
                    <span class="nk-menu-text">Activity</span>
                  </a>
                </li>
                <li class="nk-menu-item">
                  <a href="{{ route('admin.enrollments.assign') }}" class="nk-menu-link">
                    <span class="nk-menu-text">Assign</span>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nk-menu-item">
              <a href="{{ route('admin.custom-pages.index') }}" class="nk-menu-link">
                <span class="nk-menu-icon"><em class='icon ni ni-copy-page'></em></span>
                <span class="nk-menu-text">Custom Pages</span>
              </a>
            </li>

            <li class="nk-menu-item">
              <a href="{{ route('admin.promotions.index') }}" class="nk-menu-link">
                <span class="nk-menu-icon"><em class='icon ni ni-activity'></em></span>
                <span class="nk-menu-text">Promotions</span>
              </a>
            </li>

            <li class="nk-menu-item">
              <a href="{{ route('admin.books.index') }}" class="nk-menu-link">
                <span class="nk-menu-icon"><em class='icon ni ni-book'></em></span>
                <span class="nk-menu-text">Books</span>
              </a>
            </li>

            {{-- <li class="nk-menu-item">
              <a href="{{ route('admin.contacts.index') }}" class="nk-menu-link">
                <span class="nk-menu-icon"><em class='icon ni ni-mail-fill'></em></span>
                <span class="nk-menu-text">Contact Messages</span>
                @if(\App\Models\ContactMessage::where('status','open')->count())
                  <span class="badge bg-danger ms-auto">{{ \App\Models\ContactMessage::where('status','open')->count() }}</span>
                @endif
              </a>
            </li> --}}

            <li class="nk-menu-item">
              <a href="{{ route('admin.settings.index') }}" class="nk-menu-link">
                <span class="nk-menu-icon"><em class='icon ni ni-setting'></em></span>
                <span class="nk-menu-text">Settings</span>
              </a>
            </li>
          @else
            {{-- ===================== STUDENT VIEW ===================== --}}

            @php
              $customPages = \App\Models\CustomPage::where('is_published', true)
                ->orderBy('position')
                ->orderBy('title')
                ->get(['title','slug','icon']);
            @endphp

            @if($customPages->count())
              <li class="nk-menu-heading">
                <h6 class="overline-title text-primary-alt">Pages</h6>
              </li>

              @foreach($customPages as $cp)
                <li class="nk-menu-item">
                  <a href="{{ route('pages.show', $cp->slug) }}" class="nk-menu-link">
                    <span class="nk-menu-icon">
                      @if($cp->icon)
                        <em class="icon {{ $cp->icon }}"></em>
                      @else
                        <em class="icon ni ni-file-text"></em>
                      @endif
                    </span>
                    <span class="nk-menu-text">{{ $cp->title }}</span>
                  </a>
                </li>
              @endforeach
            @endif
          @endif

          <li class="nk-menu-item">
            <a href="{{ route('library') }}" class="nk-menu-link">
              <span class="nk-menu-icon"><em class='icon ni ni-book-read'></em></span>
              <span class="nk-menu-text">My Library</span>
            </a>
          </li>

          {{-- === Extra: Visit Website Button === --}}
          <li class="nk-menu-item mt-3">
            <a href="{{ url('/') }}" target="_blank" class="nk-menu-link">
              <span class="nk-menu-icon"><em class="icon ni ni-globe"></em></span>
              <span class="nk-menu-text">Visit Website</span>
            </a>
          </li>

        </ul>
      </div>
    </div>
  </div>
</div>
