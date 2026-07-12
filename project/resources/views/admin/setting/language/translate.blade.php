@extends('admin.layouts.settings')

@section('panel')
    <div class="manage-section-card-form">
        <div class="card manage-section-card mb-32">


            <form action="{{ route('admin.setting.language.bulkUpdate', $language->code) }}" method="POST">
                @csrf

                <div class="table-wrapper table-responsive">
                    <table class="table theme-tab-listle responsive-table-sm">
                        <thead>
                            <tr>
                                <th>@lang('Keyword')</th>
                                <th class="text-end">@lang('Value')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($translations as $key => $value)
                                <tr>
                                    <td class="align-middle">{{ $key }}</td>
                                    <td class="text-end">
                                        <input type="text" class="form-control form-control-sm"
                                            name="translations[{{ $key }}]" value="{{ $value }}" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center text-muted">@lang('No translations found.')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-outline-theme">
                        <i class="fas fa-save"></i>
                        @lang('Save All')
                    </button>
                </div>
            </form>


            <x-admin-paginate :model="$translations" />
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">@lang('Add New Keyword')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.setting.language.keyword.add', $language->code) }}">
                    @csrf
                    <div class="modal-body">
                        <x-form.group for="keyword" label="Keyword">
                            <input type="text" name="keyword" id="keyword" class="form-control" />
                        </x-form.group>

                        <x-form.group for="value" label="Value">
                            <input type="text" name="value" id="value" class="form-control" />
                        </x-form.group>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-theme">
                            <i class="fas fa-save"></i>
                            @lang('Submit')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb')
  
    <button type="button" class="btn btn-outline-theme addNew">
        <i class="fas fa-plus"></i>
        @lang('Add New Keyword')
    </button>
<x-back link="{{ route('admin.setting.language.list') }}"  />

@endpush

@push('scripts')
    <script>
        'use strict';
        (function($) {
            $(document).ready(function() {
                $('.addNew').on('click', function() {
                    $('#addModal').modal('show');
                });
            });
        })(jQuery);
    </script>
@endpush
