@php
    $section = 'banner';
    $about = \App\Models\Setting::where('key', 'section_about_content')->first()->value ?? null;

    $about = (object) $about;
@endphp

<div class="about-section py-60">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <div class="about-section__thumb">
                    <img class="img-fluid" src="{{  asset($about->image) }}" alt="@lang('About Image')">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-section__content">
                    <h4 class="title">{{ __($about->heading)  }}</h4>

                    <p class="desc">
                        {{ __($about->description) }}
                    </p>

                    {{-- <ul class="timeline-links">
                        <li><a href="#">Education</a></li>
                        <li><a href="#">Charity</a></li>
                        <li><a href="#">Dawah</a></li>
                    </ul> --}}
                </div>

            </div>
        </div>
    </div>
</div>
