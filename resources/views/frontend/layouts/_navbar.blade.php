<div class="sticky-stack">
{{-- ✅ promo above navbar --}}
@includeWhen(isset($promoBanner) && $promoBanner, 'frontend.layouts._promo_bar')
<style>
  .header-area {
  background: rgba(0, 0, 0, 0); /* Adjust the alpha (0.4) for transparency */
  box-shadow: none; /* Optional: remove the shadow if you want it to be fully transparent */
}
.menu-trigger span {
  background-color: white !important; 
}
.menu-trigger span::after {
  background-color: white !important; 
}
.menu-trigger span::before {
  background-color: white !important; 
}
</style>
<header class="header-area header-sticky">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <nav class="main-nav">
          {{-- ✅ Logo or Site Name --}}
          <a href="{{ route('frontend.index') }}" class="logo">
            @if(setting('logo'))
              <img src="{{ asset(setting('logo')) }}" 
                   alt="{{ setting('site_name') }}" 
                   style="height:40px; max-height:50px;">
            @else
              {{ setting('site_name') }}<em> Studio</em>
            @endif
          </a>

          <ul class="nav">
            <li class="scroll-to-section">
              <a href="{{ route('frontend.index') }}" class="active">Home</a>
            </li>
            {{-- <li class="scroll-to-section"><a href="#features">About</a></li> --}}
            <li class="scroll-to-section"><a href="{{ route('courses.list') }}">Courses</a></li>
            {{-- <li class="scroll-to-section"><a href="#schedule">Schedules</a></li> --}}
            {{-- <li class="scroll-to-section"><a href="#contact-section">Contact</a></li> --}}
            {{-- <li class="scroll-to-section">
              <a href="{{ route('cart.checkout') }}">
                Cart ({{ auth()->user() ? auth()->user()->carts()->count() : 0 }})
              </a>
            </li> --}}

            <li class="main-button">
              @guest
                <a href="{{ route('login') }}">Sign In</a>
              @else
                <a href="{{ route('home') }}">Dashboard</a>
              @endguest
            </li>
          </ul>

          <a class="menu-trigger"><span>Menu</span></a>
        </nav>
      </div>
    </div>
  </div>
</header>
</div>
