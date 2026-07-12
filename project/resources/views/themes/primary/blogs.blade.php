@extends('theme::layouts.main')

@section('content')
    <x-breadcrumb title="Blog Posts" />

    <div class="container py-5">
        <div class="row gy-3">
            @foreach ($blogPosts as $post)
                <div class="col-lg-4">
                    <div class="card blog-card">
                        <a href="{{ route('site.blog.details', $post->slug) }}" class="blog-card__thumb">
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="img-fluid" />
                        </a>

                        <div class="card-body">
                            <small class="d-inline-block mb-2">{{ System::getDateTime($post->created_at) }}</small>
                            <a class="blog-card__title" href="{{ route('site.blog.details', $post->slug) }}">
                                <h5>{{ $post->title }}</h5>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($blogPosts->hasPages())
            {!! $blogPosts->links() !!}
        @endif
    </div>
@endsection
