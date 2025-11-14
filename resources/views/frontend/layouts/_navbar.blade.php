<div class="sticky-stack">
  {{-- ✅ promo above navbar --}}
  @includeWhen(isset($promoBanner) && $promoBanner, 'frontend.layouts._promo_bar')

  @php
    // ✅ Session-based cart item count
    $cart = session('cart', ['items' => []]);
    $cartCount = 0;
    if (!empty($cart['items'])) {
        $cartCount = count($cart['items']);
    }
  @endphp

  <style>
    .header-area {
      background: rgba(0, 0, 0, 0); /* Transparent */
      box-shadow: none;
    }

    /* .menu-trigger span,
    .menu-trigger span::after,
    .menu-trigger span::before {
      background-color: white !important;
    } */

    /* === Cart Badge Styling === */
    .cart-link {
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      font-weight: 600;
      color: #fff;
    }

    .cart-link i {
      font-size: 1rem;
    }

    .cart-count {
      display: inline-grid;
      place-items: center;
      min-width: 20px;
      height: 20px;
      padding: 0 6px;
      font-size: 12px;
      line-height: 1;
      font-weight: 700;
      border-radius: 999px;
      color: #fff;
      background: #ff4d4f;
      box-shadow: 0 2px 6px rgba(255, 77, 79, .35);
      margin-left: 4px;
    }

    .cart-link:hover .cart-count {
      background: #ff6a6a;
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

              <li class="scroll-to-section">
                <a href="{{ route('courses.list') }}">Courses</a>
              </li>

              {{-- ✅ Cart link with count --}}
              <li class="scroll-to-section">
                <a href="{{ route('checkout.page') }}" class="cart-link">
                  <i class="fa fa-shopping-cart"></i>
                  <span>Cart</span>
                  <span class="cart-count">{{ $cartCount }}</span>
                </a>
              </li>

              {{-- ✅ User login/dashboard --}}
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
