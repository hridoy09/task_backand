<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ System::favicon() }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/shared/css/bootstrap.min.css') }}  ">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/admin.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/auth.css') }}">

    <title>{{ isset($title) ? $title : '' }}</title>

    <link href="{{ asset('assets/shared/css/toastr.min.css') }}" rel="stylesheet" />

    @stack('styles')
</head>

<body class="admin-auth-body" style="background-image: url({{ asset('assets/images/auth/auth-bg.jpg') }})">
    @include('partials.alerts')

    <div class="starfall">
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        <div class="falling-star"></div>
        </div>
    
    <div class="container-fluid">
        @yield('content')
    </div>

    <script src="{{ asset('assets/shared/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/shared/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/shared/js/bootstrap.bundle.min.js') }}"></script>

    @stack('scripts')

</body>

</html>
