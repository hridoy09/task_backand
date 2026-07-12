@extends('admin.layouts.master')

@section('title', 'Edit Page Layout: ' . $page->title)
@section('content')

    <div class="page-layout-editor">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.website.page.update', $page->id) }}"
            id="pageLayoutForm">
            @csrf

            @if ($page->privacy)
                <div class="form-group">
                    <label for="content" class="form-label">@lang('Page Content')</label>
                    <textarea class="form-control" name="content" id="content-editor" rows="6" placeholder="@lang('Enter page content')">{{ old('body', $page->content ?? '') }}</textarea>
                </div>
                <div class="text-end mt-3">
                    <button type="submit">
                        <x-icons.save /> @lang('Save')
                    </button>
                </div>
            @else
                {{-- Page Details --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-header__title">@lang('Page Details')</h5>
                    </div>
                    <div class="card-body">
                        @if (!$page->is_default)
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">@lang('Page Title')</label>
                                <x-form.input :value="$page?->title ?? null" name="title" required />
                            </div>
                        @endif

                        <h5 class="mb-3">@lang('SEO Settings')</h5>
                        <div class="row">
                            <div class="col-lg-4">

                                <x-uploade-image name="seo_content[image]" label="SEO Image"
                                    path="{{ get_img($page->seo_content['image'] ?? null) }}" id="seo_image_uploader" />
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group mb-3">
                                    <label for="meta_title" class="form-label">@lang('Meta Title')</label>
                                    <input type="text" name="seo_content[meta_title]" id="meta_title"
                                        class="form-control" placeholder="@lang('Enter a meta title for the page')"
                                        value="{{ old('seo_content.meta_title', $page->seo_content['meta_title'] ?? '') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="meta_keywords" class="form-label">@lang('Meta Keywords')</label>
                                    <input type="text" name="seo_content[meta_keywords]" id="meta_keywords"
                                        class="form-control" placeholder="@lang('Add meta keywords separated by commas')"
                                        value="{{ old('seo_content.meta_keywords', is_array($page->seo_content['meta_keywords'] ?? null) ? implode(',', $page->seo_content['meta_keywords']) : $page->seo_content['meta_keywords'] ?? '') }}" />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="meta_description" class="form-label">@lang('Meta Description')</label>
                                    <textarea placeholder="@lang('Enter meta description for this page')" name="seo_content[meta_description]" id="meta_description"
                                        class="form-control" rows="3">{{ old('seo_content.meta_description', $page->seo_content['meta_description'] ?? '') }}</textarea>
                                </div>
                                <x-form.group for="social_title" label="Social Title">
                                    <input type="text" name="seo_content[social_title]" id="social_title"
                                        class="form-control" placeholder="@lang('Enter social title for this page')"
                                        value="{{ old('seo_content.social_title', $page->seo_content['social_title'] ?? '') }}">
                                </x-form.group>
                                <x-form.group for="social_description" label="Social Description">
                                    <textarea name="seo_content[social_description]" class="form-control" placeholder="@lang('Enter social description for this page')">{{ old('seo_content.social_description', $page->seo_content['social_description'] ?? '') }}</textarea>
                                </x-form.group>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sections --}}
                <div class="row gy-3 double-column-row">
                    {{-- LEFT: Active Sections --}}
                    <div class="col-md-6">
                        <div class="card drag-drop-card">
                            <div class="card-header">
                                <h5 class="card-header__title">
                                    {{ strtoupper($page->title) }} Page
                                    <span class="card-header__title-small">(Below are the sections already added to this
                                        page)</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <ol id="pageSectionsList" class="simple_with_drop sec-item vertical">
                                    @php
                                        $pageSectionsOrder = $page->sections ?? [];
                                        $allSectionsConfig = System::sections();
                                    @endphp
                                    @if (!empty($pageSectionsOrder))
                                        @foreach ($pageSectionsOrder as $sectionKey)
                                            @if (isset($allSectionsConfig[$sectionKey]))
                                                @php $section = $allSectionsConfig[$sectionKey]; @endphp
                                                <li class="highlight icon-move ui-draggable ui-draggable-handle sortable-item"
                                                    data-key="{{ $sectionKey }}">
                                                    <div class="left">
                                                        <span class="sortable-icon"><i class=""></i></span>
                                                        <div class="content">
                                                            <h6 class="title">
                                                                {{ $section['name'] ?? ucfirst(str_replace('_', ' ', $sectionKey)) }}
                                                            </h6>
                                                            <p class="desc">{{ $sectionKey }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="right">
                                                        <i
                                                            class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                                                        <button class="cog-btn remove-section-btn" type="button"><i
                                                                class="la la-cog"></i></button>
                                                    </div>
                                                    <input type="hidden" name="ordered_sections[]"
                                                        value="{{ $sectionKey }}">
                                                </li>
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="empty-list-placeholder p-4 text-center text-muted">
                                            <p>Drag sections here to build your page.</p>
                                        </div>
                                    @endif
                                </ol>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Available Sections --}}
                    <div class="col-md-6">
                        <div class="card drag-drop-card">
                            <div class="card-header">
                                <h5 class="card-header__title">
                                    Sections
                                    <span class="card-header__title-small">(Drag a section to the left to display it on the
                                        page)</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <ol id="sections_items" class="simple_with_no_drop vertical">
                                    @foreach ($sections as $key => $section)
                                        <li class="highlight icon-move sortable-item available-item"
                                            data-key="{{ $key }}">
                                            <div class="left">
                                                <span class="sortable-icon"><i class=""></i></span>
                                                <div class="content">
                                                    <h6 class="title">
                                                        {{ $section['name'] ?? ucfirst(str_replace('_', ' ', $key)) }}</h6>
                                                    <p class="desc">{{ $key }}</p>
                                                </div>
                                            </div>
                                            <div class="right">
                                                <i
                                                    class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                                                <button class="cog-btn" type="button"><i class="la la-cog"></i></button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Form Actions --}}
                <div class="d-flex justify-content-end ">
                    <button type="submit" class="btn btn-outline-theme"><i class="fas fa-save"> </i>
                        @lang('Save')</button>
                </div>
            @endif
        </form>
    </div>

    @endsection
    @push('breadcrumb')
    <x-back link="{{ route('admin.website.page.list') }}" />
    @endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/shared/css/tagify.css') }}">
@endpush

@push('pre-scripts')
    <script src="{{ asset('assets/shared/js/tagify.js') }}"></script>
    <script src="{{ asset('assets/admin/js/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/admin/js/sortable.min.js') }}"></script>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pageSectionsListEl = document.getElementById('pageSectionsList');
            const availableSectionsListEl = document.getElementById('sections_items');

            const emptyPlaceholderHtml = `
        <div class="empty-list-placeholder p-4 text-center text-muted">
            <p>Drag sections here to build your page.</p>
        </div>`;

            function updateEmptyState() {
                const hasItem = pageSectionsListEl.querySelector('.sortable-item');
                if (!hasItem) {
                    pageSectionsListEl.innerHTML = emptyPlaceholderHtml;
                } else {
                    const placeholder = pageSectionsListEl.querySelector('.empty-list-placeholder');
                    if (placeholder) placeholder.remove();
                }
            }

            // Right-hand: Available sections
            new Sortable(availableSectionsListEl, {
                group: {
                    name: 'sectionsGroup',
                    pull: 'clone',
                    put: false
                },
                sort: false,
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen'
            });

            // Left-hand: Active page sections
            new Sortable(pageSectionsListEl, {
                group: 'sectionsGroup',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onAdd: function(evt) {
                    const itemEl = evt.item;
                    const sectionKey = itemEl.dataset.key;

                    // Remove old hidden input
                    const oldInput = itemEl.querySelector('input[type="hidden"]');
                    if (oldInput) oldInput.remove();

                    const sectionConfig = @json($sections);
                    const sectionName = sectionConfig[sectionKey]?.name || sectionKey.replace(/_/g, ' ')
                        .replace(/\b\w/g, l => l.toUpperCase());

                    itemEl.classList.remove('available-item');

                    itemEl.innerHTML = `
                <div class="left">
                    <span class="sortable-icon"><i class=""></i></span>
                    <div class="content">
                        <h6 class="title">${sectionName}</h6>
                        <p class="desc">${sectionKey}</p>
                    </div>
                </div>
                <div class="right">
                    <i class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                    <button class="cog-btn remove-section-btn" type="button"><i class="la la-cog"></i></button>
                </div>
                <input type="hidden" name="ordered_sections[]" value="${sectionKey}">
            `;

                    updateEmptyState();
                },
                onUpdate: updateEmptyState,
                onRemove: updateEmptyState
            });

            // Trash icon
            pageSectionsListEl.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-icon');
                if (removeBtn) {
                    removeBtn.closest('.sortable-item').remove();
                    updateEmptyState();
                }
            });

            updateEmptyState();
        });
    </script>
@endpush
