@if (!isset($seoContent))
    @php
        $seoContent = (object) generalSetting('global_seo');
    @endphp
@endif

@if (isset($seoContent))
    {{-- Basic SEO --}}
    <meta name="title" content="{{ $seoContent?->meta_title ?? config('app.name') }}">
    <meta name="description" content="{{ $seoContent?->meta_description ?? '' }}">
   

    {{-- Open Graph (Facebook, LinkedIn, etc.) --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seoContent?->social_title ?? $seoContent?->meta_title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $seoContent?->social_description ?? $seoContent?->meta_description ?? '' }}">
    <meta property="og:image" content="{{ asset($seoContent?->image ?? 'assets/images/default-seo.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoContent?->social_title ?? $seoContent?->meta_title ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $seoContent?->social_description ?? $seoContent?->meta_description ?? '' }}">
    <meta name="twitter:image" content="{{ asset($seoContent?->image ?? 'assets/images/default-seo.jpg') }}">
@endif
