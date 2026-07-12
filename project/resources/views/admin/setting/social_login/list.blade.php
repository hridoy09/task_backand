@extends('admin.layouts.settings')

@section('panel')
    <div class="manage-section-card-form">
        <div class="card manage-section-card mb-32">
           
                <div class="table-wrapper table-responsive">
                    <table class="table theme-tab-listle responsive-table-sm">
                        <thead>
                            <tr>
                                <th>@lang('Provider')</th>
                                <th>@lang('Status')</th>
                                <th class="text-end">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($providers as $key => $provider)
                                <tr>
                                    <td>
                                        <div class="social-login-provider">
                                            <img src="{{ asset('assets/' . $provider['image']) }}" alt="@lang('Image')" />

                                            <div>
                                                <strong class="d-block">{{ $provider['name'] }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div title="Toggle this to enable or disable the provider">
                                            <x-form.toggle-switch :checked="boolval($provider['status'] ?? false)"
                                                :small="false" id="social_login_{{ $key }}"
                                                data-key="{{ $key }}"
                                                name="social_login[{{ $provider['name'] }}]" />
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-theme btn-sm editBtn"
                                            data-key="{{ $key }}">
                                            <i class="fas fa-edit"></i>
                                            @lang('Edit')
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center text-muted">@lang('No providers found.')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
          
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">@lang('Configure Social Login')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form class="theForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="render-here"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-theme w-100">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .social-login-provider {
            display: flex;
            gap: 10px;
        }

        .social-login-provider img {
            width: 3rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        'use strict';
        (function() {
            $(document).ready(function() {
                $('[name^="social_login"]').on('change', function() {
                    const key = $(this).data('key');
                    const checked = $(this).is(':checked');


                    $.post(
                        "{{ route('admin.setting.social_login.config.status', ':key') }}".replace(
                            ':key', key), {
                            _token: "{{ csrf_token() }}",
                            status: checked
                        },
                        function(data) {
                            if (data.status == 'success') {
                                toastr.success(data.message, 'Success');
                            }
                        }
                    );
                });


                $('.editBtn').on('click', function() {
                    const modal = $('#editModal');
                    const key = $(this).data('key');

                    $('.theForm').attr('action',
                        "{{ route('admin.setting.social_login.config.save', ':key') }}".replace(
                            ':key', key));

                    $.get("{{ route('admin.setting.social_login.fields', ':key') }}".replace(':key',
                        key), function(data) {
                        modal.find('.render-here').html(data);
                        window?.renderFormControls();
                    });
                    modal.modal('show');
                });
            });
        })(jQuery);
    </script>
@endpush
