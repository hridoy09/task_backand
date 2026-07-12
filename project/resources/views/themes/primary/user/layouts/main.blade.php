<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title : '' }}</title>

    <link rel="icon" href="{{ System::favicon() }}">

    <link rel="stylesheet" href="{{ asset('assets/shared/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/shared/css/toastr.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/main.css') }}">

    @stack('styles')

</head>

<body>
    @include('partials.alerts')

    @include('theme::user.partials.navbar')


    <div class="container">
        @php
            $kyc = \App\Models\KycSubmission::where('user_id', auth()->id())->latest('id')->first();
        @endphp

        @if($kyc && $kyc->status === 'pending')
            <div class="alert alert-info text-center my-3">
                <strong>@lang('Your KYC is pending')</strong> — 
                @lang('Our team is reviewing your documents. You will be notified once it is approved.')
            </div>
        @elseif($kyc && $kyc->status === 'rejected')
            <div class="alert alert-danger text-center my-3">
                <strong>@lang('Your KYC was rejected')</strong> — 
                @lang('Please resubmit your information correctly.')
                <a href="{{ route('user.kyc.form') }}" class="alert-link">@lang('Click here to resubmit')</a>.
            </div>
        @endif
    </div>

    <div class="container">
        @yield('content')
    </div>
    
    <script src="{{ asset('assets/shared/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/shared/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/main.js') }}"></script>

    @stack('scripts')

</body>

</html>
