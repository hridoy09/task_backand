@extends('admin.layouts.master')

@section('content')
    <form action="{{ route('admin.blog.save', $blogPost->id ?? null) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-9">
                <dib class="card">
                    <div class="card-header">
                        <h5 class="card-header__title">@lang('Post Details')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Title')</label>
                                    <input type="text" class="form-control" name="title"
                                        value="{{ old('title', $blogPost->title ?? '') }}" required
                                        placeholder="@lang('Enter post title')" />
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <x-uploade-image id="blogImage" name="image" :path="get_img($blogPost->image) ?? null" :size="imageSize('blog')" />
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">@lang('Post Body')</label>
                                    <textarea class="form-control" name="body" id="post-editor" rows="6" placeholder="@lang('Enter post body')">{{ old('body', $blogPost->body ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </dib>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-header__title">@lang('SEO Settings')</h5>
                    </div>

                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label>@lang('Meta Title')</label>
                            <input type="text" name="seo_content[meta_title]" class="form-control"
                                value="{{ old('seo_content.meta_title', $blogPost->seo_content->meta_title ?? '') }}">
                        </div>

                        <div class="form-group mb-3">
                            <label for="meta_description" class="form-label">@lang('Meta Description')</label>
                            <textarea placeholder="@lang('Enter meta description for this page')" name="seo_content[meta_description]" id="meta_description"
                                class="form-control" rows="3">{{ old('seo_content.meta_description', $blogPost->seo_content->meta_description ?? '') }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="meta_keywords" class="form-label">@lang('Meta Keywords')</label>
                            <input placeholder="@lang('Add meta keywords separted by commas')" type="text" name="seo_content[meta_keywords]"
                                id="meta_keywords" class="form-control"
                                value="{{ old('seo_content.meta_keywords', $blogPost->seo_content->meta_keywords ?? '') }}" />
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-3">
                <aside class="software-sidebar">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-header__title">@lang('Publish Settings')</h5>
                        </div>
                        <div class="card-body">
                            <x-form.group for="category_id" label="Category">
                               
                                    <select class="js-select2" name="category_id">
                                        <option hidden value="">@lang('Select A Category')</option>
                                        @foreach ($categories as $category)
                                            <option @selected(isset($blogPost) && $blogPost?->category_id == $category->id) value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                             
                            </x-form.group>

                            <x-form.group for="status" label="Status">
                                
                                    <select data-search="false" class="js-select2" name="status">
                                        <option value="1" @selected(isset($blogPost) && $blogPost?->status == 1 ?? false)>@lang('Published')</option>
                                        <option value="0" @selected(isset($blogPost) && $blogPost?->status == 0 ?? false)>@lang('Unpublished')</option>
                                    </select>

                               
                            </x-form.group>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="submit" class="btn btn-outline-theme">
                                    <i class="fas fa-save"></i>
                                    @lang('Save')
                                </button>

                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </form>
@endsection

@push('breadcrumb')
    <x-back link="{{ route('admin.blog.list') }}" />
@endpush


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/shared/css/tagify.css') }}">
@endpush

@push('pre-scripts')

<script src="{{ asset('assets/admin/js/ckeditor.js') }}"></script>
<script src="{{ asset('assets/shared/js/tagify.js') }}"></script>
@endpush

@push('scripts')

    <script>
        $(document).ready(function() {
            SystemHelper.initEditor('#post-editor');
            new Tagify(document.querySelector('[name="seo_content[meta_keywords]"]'))
        });
    </script>
@endpush
