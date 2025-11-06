<!DOCTYPE html>
<html lang="en" class="js">
  <head>
    @include('panel.layouts._head')
  </head>
  <body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
      <div class="nk-main">
        <div class="nk-wrap nk-wrap-nosidebar">
          <div class="nk-content">
            @yield('content')
          </div>
          {{-- Optional footer (can be included here or inside page) --}}
          @hasSection('auth-footer')
            <div class="nk-footer nk-auth-footer-full">
              @yield('auth-footer')
            </div>
          @endif
        </div>
      </div>
    </div>

    @include('panel.layouts._scripts')
  </body>
</html>
