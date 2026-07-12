@extends('admin.layouts.app')

@section('app')
    <div class="dashboard position-relative">
        <div class="dashboard__inner flex-wrap">
            {{-- sidebar --}}
            @include('admin.partials.sidebar')
            <div class="dashboard__right">
                {{-- header --}}
                @include('admin.partials.navbar')
                {{-- main content --}}
                <div class="dashboard-body">
                    <div class="dashboard-body__heading">
                        <h4 class="dashboard-body__title mb-0">{{ __($title)}}</h4>
                        <div class="d-flex gap-2">
                            @stack('breadcrumb')
                        </div>
                    </div>
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection

