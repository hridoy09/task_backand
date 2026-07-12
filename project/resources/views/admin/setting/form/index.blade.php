@extends('admin.layouts.settings')

@section('panel')
    <div class="manage-section-card-form">
        <div class="card manage-section-card mb-32">
            <div class="table-wrapper table-responsive">
                <table class="table theme-tab-listle responsive-table-sm">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Description')</th>
                            <th class="text-end">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($forms as $form)
                            <tr>
                                <td>{{ $form->name }}</td>
                                <td>{{ $form->description }}</td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                        <a href="{{ route('admin.setting.form.builder', $form->slug) }}"
                                            class="btn btn-outline-theme btn-sm">
                                            <i class="fas fa-bold"></i>
                                        </a>
                                        @if (!$form->default)
                                            <button class="editBtn btn btn-outline-primary btn-sm"
                                                data-action="{{ route('admin.setting.language.save', $form->id) }}"
                                                data-name="{{ $form->name }}"
                                                data-default="{{ $form->default }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
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

            <x-admin-paginate :model="$forms" />
        </div>
    </div>

    <x-modal id="formModal" title="Add new form" :form="true" method="POST"
        action="{{ route('admin.setting.form.save') }}">
        @csrf

        <x-form.group>
            <x-form.label>@lang('Name')</x-form.label>
            <x-form.input required name="name" :placeholder="__('Enter the form name')" />
        </x-form.group>


        <x-form.group>
            <x-form.label>@lang('Description')</x-form.label>
            <textarea class="form-control" name="description" placeholder="@lang('Enter the form description')"></textarea>
        </x-form.group>

        <x-slot:footer>
            <button class="w-100" type="submit">
                <x-icons.save />
                @lang('Save')
            </button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
    <script>
        'use strict';
        (function() {
            $(document).ready(function() {
                $('.addBtn').on('click', function() {
                    const modal = $('#formModal');
                    modal.find('.modal-title').html("{{ __('Add new form') }}");
                    $('#formModal').modal('show');
                });
            });
        })(jQuery);
    </script>
@endpush
