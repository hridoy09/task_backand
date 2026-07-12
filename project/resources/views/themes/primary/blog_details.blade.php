@extends('theme::layouts.main')

@section('content')
    <div class="container py-5">
        <div class="card blog-details-card">
            <a href="{{ route('site.blog.details', $post->slug) }}" class="blog-card__thumb">
                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="img-fluid" />
            </a>

            <div class="card-body">
                <h1>{{ $post->title }}</h1>
                <small class="d-inline-block mb-2">{{ System::getDateTime($post->created_at) }}</small>

                {!! $post->body !!}
            </div>
        </div>
    </div>
@endsection