@extends('theme::layouts.main')

@section('content')
    @foreach (\App\Models\Page::where('slug', 'home')->first()?->sections ?? [] as $section)
        @if(view()->exists('theme::sections.' . $section))
            @include('theme::sections.' . $section)
        @endif
    @endforeach
@endsection
