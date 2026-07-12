@extends('admin.layouts.master')
@section('content')
    <div class="manage-section-wrapper">
        @include('admin.website.sections.sidenav')
        @yield('section_content')
    </div>
@endsection
