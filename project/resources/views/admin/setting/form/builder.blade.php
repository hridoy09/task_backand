@extends('admin.layouts.settings')

@section('panel')
    <div class="manage-section-card-form">
        <div class="card manage-section-card mb-32">
            <div class="row">
                <div class="col-lg-12">
                    <form id="builder-form" method="POST" action="{{ route('admin.setting.form.builder.save', $form->id) }}">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-header__title">{{ $form->name ?? __('Form Builder') }}</h4>
                            </div>

                            <div class="card-body">
                                <div class="builder-shell">
                                    <div class="builder-topbar">
                                        <div>
                                            <h5 class="builder-title mb-1">{{ __('Form Workspace') }}</h5>
                                            <p class="builder-subtitle mb-0">
                                                {{ __('Drag fields, configure rules, and save when your form is ready.') }}
                                            </p>
                                        </div>
                                    
                                    </div>

                                    <div id="fb-editor" class="form-builder-wrapper"></div>
                                    <input type="hidden" name="form_data" id="form-data">
                                </div>
                                
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" id="save-form" class="btn btn-outline-theme">
                                  <i class="fas fa-save"></i>
                                    {{ __('Save Form') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/jquery-ui.min.css') }}">

    <!-- FormBuilder default CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/form-builder.min.css') }}">

    <style>
        .builder-shell {
            --builder-border: #d9e2ec;
            --builder-border-strong: #c3d0df;
            --builder-text: #0f172a;
            --builder-muted: #5b6472;
            background: linear-gradient(180deg, #f7f9fd 0%, #f2f6fb 100%);
            border: 1px solid #e3e9f1;
            border-radius: 14px;
            padding: 18px;
        }

        .builder-topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .builder-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--builder-text);
            letter-spacing: 0.2px;
        }

        .builder-subtitle {
            font-size: 0.86rem;
            color: var(--builder-muted);
        }

        .form-builder-wrapper {
            background: #f5f7fb;
            border: 1px solid var(--builder-border);
            border-radius: 12px;
            padding: 16px;
            min-height: 520px;
        }

        .form-builder-wrapper .frmb {
            background: transparent;
            border: none;
        }

        .form-builder-wrapper .form-actions {
            display: none !important;
        }

        .form-builder-wrapper .stage-wrap {
            background: #fff;
            border: 1px dashed var(--builder-border-strong);
            border-radius: 10px;
            padding: 1rem;
            min-height: 420px;
        }

        .form-builder-wrapper .cb-wrap {
            background: #fff;
            border: 1px solid var(--builder-border);
            border-radius: 10px;
            overflow: hidden;
        }

        .form-builder-wrapper .frmb .form-elements,
        .form-builder-wrapper .frmb .field-actions {
            background: #fff;
            border: 1px solid var(--builder-border);
            border-radius: 10px;
        }

        #save-form {
            min-width: 140px;
            font-weight: 600;
            border-radius: 8px;
        }

        @media (max-width: 991px) {
            .builder-topbar {
                flex-direction: column;
            }

            .form-builder-wrapper {
                min-height: 460px;
                padding: 12px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/form-builder.min.js') }}"></script>

    <script>
        'use strict';
        (function($) {
            $(document).ready(function() {
                const fbEditor = document.getElementById('fb-editor');

                const formData = @json($form->form_data);
                
                var formBuilder = $(fbEditor).formBuilder({
                    formData,
                    typeUserAttrs: {
                        text: {
                            autocomplete: {
                                label: 'Autocomplete',
                                options: { on: 'On', off: 'Off' },
                                value: 'on'
                            },
                            required: {
                                label: 'Required',
                                options: { true: 'Yes', false: 'No' },
                                value: 'false'
                            }
                        }
                    }
                });

                $('#save-form').on('click', function(e) {
                    e.preventDefault();
                    let formData = formBuilder.actions.getData('json');
                    $('#form-data').val(formData);
                    $('#builder-form').submit();
                });
            });
        })(jQuery);
    </script>
@endpush
