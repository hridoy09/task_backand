<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ isset($title) ? $title : '' }}</title>

    @include('partials.seo')

    <link rel="shortcut icon" href="{{ System::favicon() }}">

    <link rel="stylesheet" href="{{ asset('assets/shared/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/shared/css/toastr.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/custom_css.css') }}">

    <style>
        .select2 .selection {
            width: 100%;
        }
    </style>
    
    @stack('styles')
</head>

<body>

    @include('partials.alerts')
    
    @include('theme::user.partials.navbar')

    @yield('content')

    @include('theme::user.partials.footer')

    <script src="{{ asset('assets/shared/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/shared/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/main.js') }}"></script>

    <script>
        $(document).ready(function (){
            $('.changeLang').on('change', function() {
                const langKey = $(this).val();
                window.location.href = "{{ route('lang.switch', ':key') }}".replace(':key', langKey);
            });

            $('select.select2').each(function() {
                var $this = $(this);

                $this.select2({
                    placeholder: $this.data('placeholder'),
                    minimumResultsForSearch: $this.data('minimum-results-for-search') || 10
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
