
<link rel="stylesheet" href="{{ asset('assets/shared/css/toastr.min.css') }}">
@push('pre-scripts')
<script src="{{ asset('assets/shared/js/toastr.min.js') }}"></script>
@endpush

@push('scripts')
<script>
    // Global Toastr Configuration
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "10000",
        "hideDuration": "1000",
        "timeOut": 5000, // 🔥 No auto close
        "extendedTimeOut": 0, // 🔥 Stay visible
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
</script>
@endpush

{{-- Success Notification --}}
@if (session('success'))
    @push('scripts')
    <script>
        toastr.success("{{ session('success') }}", "Success");
    </script>
    @endpush
@endif

{{-- Error Notification --}}
@if (session('error'))
    @push('scripts')
    <script>
        toastr.error("{{ session('error') }}", "Error");
    </script>
    @endpush
@endif

{{-- Info Notification --}}
@if (session('info'))
    @push('scripts')
    <script>
        toastr.info("{{ session('info') }}", "Info");
    </script>
    @endpush
@endif

{{-- Validation Errors --}}
@if ($errors->any())
    @push('scripts')
    <script>
        @foreach ($errors->all() as $error)
            toastr.error(@json($error), "Validation Error");
        @endforeach
    </script>
    @endpush
@endif
