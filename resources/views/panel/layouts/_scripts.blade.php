{{-- Global JS (from public/panel) --}}
<script src="{{ asset('panel/assets/js/bundle9b70.js') }}"></script>
<script src="{{ asset('panel/assets/js/scripts9b70.js') }}"></script>

{{-- Optional extras (uncomment if you use) --}}
<script src="{{ asset('panel/assets/js/demo-settings9b70.js') }}"></script>
<script src="{{ asset('panel/assets/js/charts/chart-lms9b70.js') }}"></script>

{{-- <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script> --}}
<script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>


{{-- Page-specific JS --}}
@stack('scripts')
