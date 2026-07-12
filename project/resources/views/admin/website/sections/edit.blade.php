@extends('admin.website.sections.layout')
@section('section_content')
    <div class="manage-section-card-form">

        <div class="card manage-section-card mb-32">
            <div class="card-header">
                <h5 class="card-header__title">
                    {{ $title }}
                </h5>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card page-content-card">
                        <div class="card-body">
                            <form method="POST"  action="{{ route('admin.website.section.update', $sectionKey) }}"
                                enctype="multipart/form-data" id="sectionEditForm">
                                @csrf

                                @foreach ($sectionConfig['config'] as $fieldKey => $field)
                                    <div class="field-block card shadow-sm mb-4">

                                        <label class="form-label">
                                            {{ $field['label'] ?? ucfirst(str_replace('_', ' ', $fieldKey)) }}

                                        </label>

                                        {{-- Text Input --}}
                                        @if ($field['type'] === 'image')
                                            @php $currentImage = $contentData[$fieldKey] ?? null; @endphp
                                            <x-uploade-image name="content[{{ $fieldKey }}]"
                                                id="content_{{ $fieldKey }}" :path="get_img($currentImage)" :size="$field['size']" />
                                        @elseif ($field['type'] === 'text')
                                            <input type="text" id="content_{{ $fieldKey }}"
                                                name="content[{{ $fieldKey }}]" class="form-control"
                                                value="{{ old('content.' . $fieldKey, $contentData[$fieldKey] ?? '') }}">

                                            {{-- Textarea --}}
                                        @elseif($field['type'] === 'textarea')
                                            <textarea id="content_{{ $fieldKey }}" name="content[{{ $fieldKey }}]" class="form-control" rows="5">{{ old('content.' . $fieldKey, $contentData[$fieldKey] ?? '') }}</textarea>

                                            {{-- Image --}}


                                            {{-- Group --}}
                                        @elseif($field['type'] === 'group')
                                            <div class="field-group-container p-3 bg-white border rounded">
                                                @foreach ($field['fields'] as $childKey => $childField)
                                                    @php $groupValue = $contentData[$fieldKey][$childKey] ?? null; @endphp
                                                    <div class="form-group mb-3">
                                                        <label for="content_{{ $fieldKey }}_{{ $childKey }}"
                                                            class="form-label">
                                                            {{ $childField['label'] ?? ucfirst(str_replace('_', ' ', $childKey)) }}
                                                            @if ($childField['type'] === 'image' && isset($childField['size']))
                                                                <small class="text-muted fw-normal">(Recommended:
                                                                    {{ $childField['size'] }})</small>
                                                            @endif
                                                        </label>

                                                        @if ($childField['type'] === 'image')
                                                            @if ($groupValue)
                                                                <div class="mb-2 current-image-preview">
                                                                    <img src="{{ asset($groupValue) }}"
                                                                        class="img-thumbnail" style="max-height:100px;">
                                                                </div>
                                                            @endif
                                                            <input type="file"
                                                                id="content_{{ $fieldKey }}_{{ $childKey }}"
                                                                name="content[{{ $fieldKey }}][{{ $childKey }}]"
                                                                class="form-control">
                                                        @elseif($childField['type'] === 'textarea')
                                                            <textarea id="content_{{ $fieldKey }}_{{ $childKey }}"
                                                                name="content[{{ $fieldKey }}][{{ $childKey }}]" class="form-control" rows="3">{{ old("content.$fieldKey.$childKey", $groupValue ?? '') }}</textarea>
                                                        @else
                                                            <input type="text"
                                                                id="content_{{ $fieldKey }}_{{ $childKey }}"
                                                                name="content[{{ $fieldKey }}][{{ $childKey }}]"
                                                                class="form-control"
                                                                value="{{ old("content.$fieldKey.$childKey", $groupValue ?? '') }}">
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Repeater --}}
                                        @elseif($field['type'] === 'repeater')
                                            <div class="repeater-container" data-key="{{ $fieldKey }}">
                                                <div class="repeater-items-list">
                                                    @php $items = old('content.' . $fieldKey, $contentData[$fieldKey] ?? []); @endphp
                                                    @foreach ($items as $index => $itemData)
                                                        <div class="repeater-item card  mb-3">
                                                            <div class=" card-header">
                                                                <h5 class="card-header__title">@lang('Item')
                                                                    {{ $index + 1 }}</h5>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger remove-repeater-item">@lang('Remove')</button>
                                                            </div>
                                                            <div class="card-body">
                                                                @foreach ($field['fields'] as $subKey => $subField)
                                                                    <div class="form-group mb-3">
                                                                        <label
                                                                            for="content_{{ $fieldKey }}_{{ $index }}_{{ $subKey }}"
                                                                            class="form-label">
                                                                            {{ $subField['label'] ?? ucfirst(str_replace('_', ' ', $subKey)) }}
                                                                            @if ($subField['type'] === 'image' && isset($subField['size']))
                                                                                <small
                                                                                    class="text-muted fw-normal">(@lang('Recommended'):
                                                                                    {{ $subField['size'] }})</small>
                                                                            @endif
                                                                        </label>

                                                                        @if ($subField['type'] === 'image')
                                                                            @php $repeaterImage = $itemData[$subKey] ?? null; @endphp

                                                                            @if ($repeaterImage)
                                                                                <x-uploade-image
                                                                                    id="content_{{ $fieldKey }}_{{ $index }}_{{ $subKey }}"
                                                                                    path="{{ get_img($repeaterImage) ?? '' }}"
                                                                                    name="content[{{ $fieldKey }}][{{ $index }}][{{ $subKey }}]" />
                                                                            @endif
                                                                        @elseif($subField['type'] === 'textarea')
                                                                            <textarea name="content[{{ $fieldKey }}][{{ $index }}][{{ $subKey }}]" class="form-control"
                                                                                rows="3">{{ $itemData[$subKey] ?? '' }}</textarea>
                                                                        @else
                                                                            <input type="text"
                                                                                name="content[{{ $fieldKey }}][{{ $index }}][{{ $subKey }}]"
                                                                                class="form-control"
                                                                                value="{{ $itemData[$subKey] ?? '' }}">
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <button type="button"
                                                    class="btn btn-outline-primary add-repeater-item mt-2"
                                                    data-template-id="repeater-template-{{ $fieldKey }}">
                                                    @lang('Add')
                                                    {{ $field['button_label'] ?? ($field['label'] ?? 'Item') }}
                                                </button>

                                                {{-- Repeater Template --}}
                                                <script type="text/template" id="repeater-template-{{ $fieldKey }}">
                                                <div class="repeater-item card mb-3">
                                                    <div class="card-header">
                                                        <h6 class="card-header__title">@lang('New Item')</h6>
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-repeater-item">@lang('Remove')</button>
                                                    </div>
                                                    <div class="repeater-item-content card-body">
                                                        @foreach ($field['fields'] as $subKey => $subField)
                                                            <div class="form-group mb-3">
                                                                <label class="form-label">{{ $subField['label'] ?? ucfirst(str_replace('_', ' ', $subKey)) }}</label>
                                                                @if ($subField['type'] === 'image')
                                                                    <x-uploade-image name="content[{{ $fieldKey }}][__INDEX__][{{ $subKey }}]" id="content[{{ $fieldKey }}][__INDEX__][{{ $subKey }}]" path="{{ get_img(null) }}" />
                                                                    
                                                                @elseif($subField['type'] === 'textarea')
                                                                    <textarea name="content[{{ $fieldKey }}][__INDEX__][{{ $subKey }}]" class="form-control" rows="3"></textarea>
                                                                @else
                                                                    <input type="text" name="content[{{ $fieldKey }}][__INDEX__][{{ $subKey }}]" class="form-control">
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </script>
                                            </div>
                                        @endif

                                    </div>
                                @endforeach


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=" text-end">
            <button type="submit" form="sectionEditForm" class="btn btn-outline-theme">
                <i class="fas fa-save"></i>
                @lang('Save')
            </button>
        </div>
    </div>
@endsection
@push('breadcrumb')
    <a href="{{ route('admin.website.section.list') }}" class="btn btn-outline-theme">
        <x-icons.list />
        @lang('Section List')
    </a>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.repeater-container').each(function() {
                $(this).data('next-index', $(this).find('.repeater-item').length);
            });

            $('body').on('click', '.add-repeater-item', function() {
                let $container = $(this).closest('.repeater-container');
                let templateId = $(this).data('template-id');
                let index = $container.data('next-index') || 0;
                let template = $('#' + templateId).html().replace(/__INDEX__/g, index);
                $container.find('.repeater-items-list').append(template);
                $container.data('next-index', index + 1);
            });

            $('body').on('click', '.remove-repeater-item', function() {
                $(this).closest('.repeater-item').remove();
            });
        });
    </script>
@endpush
