<div class="nk-header nk-header-fixed">
  <div class="container-fluid">
    <div class="nk-header-wrap">
      <div class="nk-menu-trigger d-xl-none ms-n1">
        <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu">
          <em class="icon ni ni-menu"></em>
        </a>
      </div>

      {{-- ✅ Dynamic logo / site name --}}
      <div class="nk-header-brand d-xl-none">
        <a href="{{ route('home') }}" class="logo-link">
          @if(setting('logo'))
            <img class="logo-light logo-img" src="{{ asset(setting('logo')) }}" alt="{{ setting('site_name', 'LMS') }}">
          @else
            <span class="fw-bold">{{ setting('site_name', 'LMS') }}</span>
          @endif
        </a>
      </div>

      {{-- Tools --}}
      <div class="nk-header-tools">
        <ul class="nk-quick-nav">
          {{-- Notifications, Messages, Profile Dropdown --}}
          <li class="dropdown user-dropdown">
            <a href="#" class="dropdown-toggle me-n1" data-bs-toggle="dropdown">
              <div class="user-toggle">
                <div class="user-avatar sm"><em class="icon ni ni-user-alt"></em></div>
                <div class="user-info d-none d-xl-block">
                  <div class="user-status">{{ isAdmin() ? "Administrator" : "Student" }}</div>
                  <div class="user-name dropdown-indicator">{{ auth()->user()->name ?? 'User' }}</div>
                </div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
              <div class="dropdown-inner">
                <ul class="link-list">
                  <li><a href="{{ route('profile.show') }}"><em class="icon ni ni-user-alt"></em><span>Profile</span></a></li>
                  <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      <em class="icon ni ni-signout"></em><span>Sign out</span>
                    </a>
                  </li>
                </ul>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
