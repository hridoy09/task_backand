<!DOCTYPE html>
<html @if (in_array(app()->getLocale(), config('rtl'))) dir="rtl" @endif lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>{{ generalSetting('site_title') }} - {{ isset($title) ? $title : '' }}</title>
    <!-- Favicon -->
    @include('partials.seo')

    <link rel="shortcut icon" href="{{ System::favicon() }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href={{ asset('assets/admin/css/bootstrap.min.css') }}>
    <!-- Font Awesome -->
    <link rel="stylesheet" href={{ asset('assets/admin/css/font-awesome.min.css') }}>
    <!-- Line Awesome -->
    <link rel="stylesheet" href={{ asset('assets/admin/css/line-awesome.min.css') }}>
    <!-- Select2 -->
    <link rel="stylesheet" href={{ asset('assets/admin/css/select2.min.css') }}>
    <!-- Nice Select -->
    {{-- <link rel="stylesheet" href={{ asset('assets/admin/css/nice-select.css') }}> --}}
    <!-- Summer Note -->
    <link rel="stylesheet" href={{ asset('assets/admin/css/summernote-lite.min.css') }}>
    <!-- Spectrum For Color -->
    <link rel="stylesheet" href={{ asset('assets/admin/css/spectrum.css') }}>
    <!-- Date Range Picker -->
    <link rel="stylesheet" href={{ asset('assets/admin/css/daterangepicker.css') }}>

    @stack('style-lib')
    <!-- Main Css -->
    <link rel="stylesheet" href={{ asset('assets/admin/css/main.css') }}>
    @stack('styles')
</head>

<body>
    <div class="theme-preloader">
        <div class="loader-p"></div>
    </div>
    <div class="theme-overlay"></div>
    {{-- <a class="back-to-top"><i class="fas fa-angle-double-up"></i></a> --}}
    @yield('app')

    @include('partials.alerts')

    <!-- Jquery -->
    <script src={{ asset('assets/admin/js/jquery-3.7.1.min.js') }}></script>
    <!-- Bootstrap Bundle -->
    <script src={{ asset('assets/admin/js/boostrap.bundle.min.js') }}></script>
    <!-- Jquery UI -->
    <script src={{ asset('assets/admin/js/jquery-ui.min.js') }}></script>
    <!-- Nice Select JS -->
    {{-- <script src={{ asset('assets/admin/js/jquery.nice-select.js') }}></script> --}}
    <!-- Select2 -->
    <script src={{ asset('assets/admin/js/select2.min.js') }}></script>
    <!-- Apex Charts -->
    <script src={{ asset('assets/admin/js/apexcharts.min.js') }}></script>
    <!-- Moment to help Date Range Picker -->
    <script src={{ asset('assets/admin/js/moment.min.js') }}></script>
    <!-- Date Range Picker -->
    <script src={{ asset('assets/admin/js/daterangepicker.min.js') }}></script>
    <!-- Summer Note -->
    <script src={{ asset('assets/admin/js/summernote-lite.min.js') }}></script>
    <!-- Spectrum For Color -->
    <script src={{ asset('assets/admin/js/spectrum.js') }}></script>
    <!-- Viewport -->
    <script src={{ asset('assets/admin/js/viewport.jquery.js') }}></script>
    <script src={{ asset('assets/admin/js/systemHelper.js') }}></script>

    <!-- Main -->
    @stack('pre-scripts')
    <script src={{ asset('assets/admin/js/main.js') }}></script>

    @stack('scripts')

    <script>
        $(document).ready(function() {
            // Global function to initialize Select2 with data-search attribute support
            function initSelect2(selector) {
                $(selector).each(function() {
                    var $this = $(this);
                    var searchEnabled = $this.data('search') !== false && $this.data('search') !== 'false';

                    $this.select2({
                        minimumResultsForSearch: searchEnabled ? 0 : Infinity,
                    });
                });
            }

            // Auto-initialize on page load
            $(document).ready(function() {
                initSelect2('.js-select2');
            });
        });

        
    </script>


</body>

</html>
