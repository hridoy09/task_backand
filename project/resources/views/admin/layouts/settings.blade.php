@extends('admin.layouts.master')
@section('content')
    <div class="manage-section-wrapper">
        @include('admin.partials.settings_sidebar')
        @yield('panel')
    </div>
@endsection
