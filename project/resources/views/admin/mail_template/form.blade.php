@extends('admin.layouts.master')
@section('content')


    <form action="{{ $template->exists ? route('admin.setting.mail_template.update', $template) : route('admin.setting.mail_template.store') }}"
          method="POST"
          class="ajax-form"
          enctype="multipart/form-data">
        @csrf
        @php
            $selectedView = old('view', $template->view ?? 'global');
            $emailViews = $emailViews ?? [];
            $emailViewCount = count($emailViews);
        @endphp

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <x-form.group :label="__('Template Name')" for="template-name">
                                    <input type="text" name="name" id="template-name" class="form-control"
                                           value="{{ old('name', $template->name) }}" required />
                                </x-form.group>
                            </div>
                            <div class="col-md-6">
                                <x-form.group :label="__('Template Code')" for="template-code">
                                    <input type="text" name="code" id="template-code" class="form-control"
                                           value="{{ old('code', $template->code) }}"
                                           {{ $template->exists ? 'readonly' : '' }}
                                           placeholder="USER_REGISTERED" required />
                                </x-form.group>
                            </div>
                            <div class="col-12">
                                <x-form.group :label="__('Mail Subject')" for="template-subject">
                                    <input type="text" name="subject" id="template-subject" class="form-control"
                                           value="{{ old('subject', $template->subject) }}" required />
                                </x-form.group>
                            </div>
                            <div class="col-12">
                                <x-form.group :label="__('Mail Body')" for="template-body">
                                    <textarea
                                        name="body"
                                        id="template-body"
                                        class="form-control"
                                        rows="12"
                                        required
                                    >{{ old('body', $template->body) }}</textarea>
                                </x-form.group>
                                <small class="text-muted">
                                    @lang('Rich formatting is supported. Use the shortcodes listed on the right to inject dynamic values.')
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Attachments')</h5>
                        <p class="text-muted small">
                            @lang('Attachments will be included in every email sent using this template.')
                        </p>

                        <div class="mb-3">
                            <input type="file" name="attachments[]" class="form-control" multiple>
                            <small class="text-muted d-block mt-1">@lang('Maximum size 5MB per file.')</small>
                        </div>

                        @if ($template->attachment_collection->isNotEmpty())
                            <div class="list-group">
                                @foreach ($template->attachment_collection as $attachment)
                                    <label class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $attachment['name'] ?? basename($attachment['path']) }}</strong>
                                            <div class="text-muted small">{{ $attachment['path'] }}</div>
                                        </div>
                                        <span class="d-flex align-items-center gap-2">
                                            <span class="text-muted small">@lang('Remove')</span>
                                            <input type="checkbox" name="remove_attachments[]" value="{{ $attachment['path'] }}">
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-outline-theme">
                        <i class="fas fa-save"></i>
                        @lang('Save')
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">@lang('Available Shortcodes')</h5>
                        <p class="text-muted small">
                            @lang('Use these placeholders inside the subject or body. They are managed in code and replaced automatically when the email is sent.')
                        </p>
                        <div class="list-group list-group-flush">
                            @forelse ($availableShortcodes as $shortcode)
                                <div class="list-group-item">
                                    <div class="fw-semibold">{{ $shortcode['key'] }}</div>
                                    <div class="text-muted small">{{ $shortcode['description'] }}</div>
                                </div>
                            @empty
                                <div class="list-group-item text-muted small">
                                    @lang('No shortcodes are configured for this template yet.')
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('breadcrumb')
<x-back :link="route('admin.setting.mail_template.index')" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/ckeditor.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.SystemHelper && typeof window.SystemHelper.initEditor === 'function') {
                window.SystemHelper.initEditor('#template-body');
            }
        });
    </script>
@endpush
