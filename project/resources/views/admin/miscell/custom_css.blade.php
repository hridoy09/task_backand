@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid custom-css-page">
        <form action="{{ route('admin.miscell.custom.css.save') }}" method="POST" id="customCssForm">
            @csrf

            <div class="card  mb-32">
                <div class="card-header">
                    <div>
                        <div class="custom-css-header__icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <h4 class="custom-css-header__title mb-1">@lang('Custom CSS')</h4>
                        <p class="custom-css-header__subtitle mb-0">@lang('Add advanced style overrides for your website layout and components.')</p>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="custom-css-warning mb-4" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                            <div>
                                <strong>@lang('Important'):</strong>
                                <span>@lang('Only modify CSS if you understand the impact. Incorrect rules can break website styling.')</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="custom-css-meta-item">
                                <small>@lang('Target')</small>
                                <strong>@lang('Frontend Theme')</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-css-meta-item">
                                <small>@lang('Scope')</small>
                                <strong>@lang('Global Override')</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-css-meta-item">
                                <small>@lang('Editor')</small>
                                <strong>CodeMirror</strong>
                            </div>
                        </div>
                    </div>

                    <div class="editor-frame">
                        <div class="editor-frame__header">
                            <span class="editor-dot"></span>
                            <span class="editor-dot"></span>
                            <span class="editor-dot"></span>
                            <small class="ms-2">@lang('custom.css')</small>
                        </div>
                        <textarea name="custom_css" id="css-editor">{{ $customCss ?? '/* Your custom CSS code goes here */' }}</textarea>
                    </div>
                </div>

                
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-outline-theme">
                    <i class="fas fa-save"></i>
                    @lang('Save')
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/codemirror.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/dracula.min.css') }}">

    <style>
        .custom-css-page .custom-css-header__icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.14);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .custom-css-page .custom-css-header__title {
            color: #fff;
            font-weight: 700;
        }

        .custom-css-page .custom-css-header__subtitle {
            color: rgba(255, 255, 255, 0.78);
        }

        .custom-css-page .custom-css-warning {
            border: 1px solid #f8d7a4;
            background: #fff8eb;
            color: #7a4b00;
            border-radius: 10px;
            padding: 0.9rem 1rem;
        }

        .custom-css-page .custom-css-meta-item {
            border: 1px solid #e9edf2;
            border-radius: 10px;
            background: #f8fafc;
            padding: 0.75rem 0.9rem;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .custom-css-page .custom-css-meta-item small {
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .custom-css-page .custom-css-meta-item strong {
            color: #1f2937;
            font-weight: 600;
        }

        .custom-css-page .editor-frame {
            border: 1px solid #d9e1ea;
            border-radius: 12px;
            overflow: hidden;
        }

        .custom-css-page .editor-frame__header {
            display: flex;
            align-items: center;
            padding: 0.55rem 0.8rem;
            background: #0b1220;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .custom-css-page .editor-frame__header small {
            color: rgba(255, 255, 255, 0.8);
        }

        .custom-css-page .editor-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            margin-right: 5px;
            background: #ef4444;
        }

        .custom-css-page .editor-dot:nth-child(2) {
            background: #f59e0b;
        }

        .custom-css-page .editor-dot:nth-child(3) {
            background: #10b981;
        }

        .custom-css-page .CodeMirror {
            height: 560px;
            border: 0;
            font-size: 14px;
        }

        .custom-css-page .custom-css-footer {
            background: #f8fafc;
            border-top: 1px solid #e9edf2;
            padding: 0.9rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .custom-css-page .custom-css-footer__hint {
            color: #64748b;
            font-size: 0.88rem;
        }


        @media (max-width: 767.98px) {
            .custom-css-page .custom-css-header {
                padding: 1.2rem;
            }

            .custom-css-page .CodeMirror {
                height: 460px;
            }

            .custom-css-page .custom-css-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/codemirror.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/css.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/closebrackets.min.js') }}"></script>

    <script>
        (function() {
            var editor = CodeMirror.fromTextArea(document.getElementById('css-editor'), {
                mode: 'css',
                theme: 'dracula',
                lineNumbers: true,
                lineWrapping: true,
                autoCloseBrackets: true,
                matchBrackets: true
            });

       
        })();
    </script>
@endpush
