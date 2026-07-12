@extends('admin.layouts.settings')

@section('panel')
    <div class="manage-section-card-form">
        <div class="card manage-section-card mb-32">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('admin.setting.global_seo.save') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-header__title">@lang('Global SEO Setting')</h4>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-12">
                                        <label for="">@lang('Seo Image')</label>
                                         <x-uploade-image id="seoImage" name="image" :path="get_img($generalSetting['site_favicon'] ?? null)" :size="imageSize('seo')"  />
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <x-form.group for="meta_title" label="Meta Title">
                                                    <input required type="text" name="meta_title" id="meta_title" class="form-control"
                                                        value="{{ old('meta_title', $globalSeo['meta_title'] ?? '') }}">
                                                </x-form.group>
                                            </div>

                                            <div class="col-12">
                                                <x-form.group for="meta_keywords" label="Meta Keywords (Comma Sepearated)">
                                                    <input type="text" required name="meta_keywords" id="meta_keywords" class="form-control"
                                                        value="{{ old('meta_keywords', isset($globalSeo['meta_keywords']) ? implode(',', $globalSeo['meta_keywords']) : '') }}" />
                                                </x-form.group>
                                            </div>

                                            <div class="col-12">
                                                <x-form.group for="meta_description" label="Meta Description">
                                                    <x-form.textarea required name="meta_description" class="form-control">{{ old('meta_description', $globalSeo['meta_description'] ?? '') }}</x-form.textarea>
                                                </x-form.group>
                                            </div>

                                            <div class="col-12">
                                                <x-form.group for="social_title" label="Social Title">
                                                    <input required type="text" name="social_title" id="social_title" class="form-control"
                                                        value="{{ old('social_title', $globalSeo['social_title'] ?? '') }}">
                                                </x-form.group>
                                            </div>

                                            <div class="col-12">
                                                <x-form.group for="social_description" label="Social Description">
                                                    <x-form.textarea required name="social_description" class="form-control">{{ old('social_description', $globalSeo['social_description'] ?? '') }}</x-form.textarea>
                                                </x-form.group>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-outline-theme">
                                            <i class="fas fa-save"></i>
                                            @lang('Save')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/shared/css/tagify.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/shared/js/tagify.js') }}"></script>
    <script>
        (function() {
            const input = document.querySelector('#meta_keywords');
            if (input) {
                new Tagify(input);
            }
        })();
    </script>
@endpush
