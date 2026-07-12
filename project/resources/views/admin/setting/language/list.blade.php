@extends('admin.layouts.settings')

@section('panel')
    <div class="manage-section-card-form">
        <div class="card manage-section-card mb-32">

            <div class="table-wrapper table-responsive">
                <table class="table theme-tab-listle responsive-table-sm">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Code')</th>
                            <th>@lang('Flag')</th>
                            <th class="text-end">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($languages as $language)
                            <tr>
                                <td>{{ $language->name }}</td>
                                <td>{{ $language->code }}</td>
                                <td>{{ $language->flag }}</td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center gap-3 justify-content-end">
                                        <button type="button" data-code="{{ $language->code }}" class="importBtn btn btn-outline-success btn-sm">
                                            <i class="fas fa-download"></i>
                                            
                                        </button>
                                        <a class="btn btn-outline-info btn-sm"
                                            href="{{ route('admin.setting.language.translate', $language->code) }}">
                                            <i class="fas fa-language"></i>
                                            
                                        </a>
                                        <button class="editBtn btn btn-outline-primary btn-sm" type="button"
                                            data-action="{{ route('admin.setting.language.save', $language->id) }}"
                                            data-name="{{ $language->name }}" data-code="{{ $language->code }}">
                                            <i class="fas fa-edit"></i>
                                            
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">
                                    <x-admin-no-data />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-admin-paginate :model="$languages" />
        </div>
    </div>

    <x-modal size="md" id="importModal" title="Import Language" :form="true" method="POST">
        <x-form.group label="Select langage to import from">
            <select name="code" id="" class="form-control select-2">
                <option value="{{ 'system' }}">{{ __('System') }}</option>
                @foreach ($languages as $lang)
                    <option value="{{ $lang->code }}">{{ $lang->name }}</option>
                @endforeach
            </select>
        </x-form.group>

        <x-slot:footer>
            <button var="second" type="submit">
                @lang('Import Now')
            </button>
        </x-slot:footer>
    </x-modal>


    <x-modal size="lg" id="keywordsModal" title="Language Keywords">
        <textarea name="" readonly id="" class="form-control keywords-here"></textarea>

        <x-slot:footer>
            <button class="clipboardBtn" var="second">
                <x-icons.clipboard />
                @lang('Copy to Clipboard')
            </button>
        </x-slot:footer>
    </x-modal>

    <x-modal id="langModal" title="Add new language" :form="true" method="POST"
        action="{{ route('admin.setting.language.save') }}">
        @csrf

        <x-form.group>
            <x-form.label>@lang('Name')</x-form.label>
            <x-form.input required name="name" :placeholder="__('Enter the language name')" />
        </x-form.group>

        <x-form.group>
            <x-form.label>@lang('Code')</x-form.label>
            <x-form.input required name="code" :placeholder="__('Enter the language code')" />
        </x-form.group>

        <x-slot:footer>
            <button class="w-100" type="submit">
                <x-icons.save />
                @lang('Save')
            </button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('breadcrumb')
    <button class="keyworsdBtn btn btn-outline-info" var="second">
        <i class="fas fa-copy"></i>
        @lang('Copy Keywords')
    </button>

    <button class="addBtn btn btn-outline-theme">
        <i class="fas fa-plus"></i>
        @lang('Add New')
    </button>
@endpush

@push('styles')
    <style>
        .keywords-here {
            min-height: 400px !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        'use strict';
        (function() {
            $(document).ready(function() {
                $('.clipboardBtn').on('click', function() {
                    const keywords = $('.keywords-here').val();
                    window.navigator.clipboard.writeText(keywords).then(() => {
                        toastr.success("{{ __('Keywords copied to clipboard') }}", 'Success');
                    });
                });

                $('.keyworsdBtn').on('click', function() {
                    const modal = $('#keywordsModal');

                    $.get("{{ route('admin.setting.language.keywords') }}", function(response) {
                        if (response?.status == 'success') {
                            return $('.keywords-here').val(Object.values(response?.keywords)
                                .join('\n'));
                        }
                    });

                    modal.modal('show');
                });

                $('.addBtn').on('click', function() {
                    const modal = $('#langModal');
                    modal.find('.modal-title').html("{{ __('Add new Language') }}");
                    $('#langModal').modal('show');
                });

                $('.editBtn').on('click', function() {
                    const modal = $('#langModal');
                    modal.find('.modal-title').html("{{ __('Edit Language') }}");
                    modal.find('form').attr('action', $(this).attr('data-action'));
                    modal.find('[name="name"]').val($(this).attr('data-name'));
                    modal.find('[name="code"]').val($(this).attr('data-code'));
                    modal.modal('show');
                });

                $('.importBtn').on('click', function() {
                    const route = "{{ route('admin.setting.language.import', ':code') }}".replace(
                        ':code', $(this).data('code'));
                    $('#importModal').find('form').attr('action', route);
                    $('#importModal').modal('show');
                });
            });
        })(jQuery);
    </script>
@endpush
