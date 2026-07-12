@extends('admin.layouts.settings')

@section('panel')
    @php
        $configurations = [
            [
                'key' => 'user_registration',
                'title' => __('User Registration'),
                'description' => __('If disabled, new users cannot register in the system.'),
                'image' => 'user_register.png',
                'enabled' => (bool) $generalSetting->user_registration,
            ],
            [
                'key' => 'kyc',
                'title' => __('KYC Verification'),
                'description' => __('Require users to complete KYC before accessing key features.'),
                'image' => 'kyc.jpg',
                'enabled' => (bool) $generalSetting->kyc,
            ],
            [
                'key' => 'maintenance_mode',
                'title' => __('Maintenance Mode'),
                'description' => __('Temporarily disable frontend access while doing maintenance.'),
                'image' => 'maintenance.png',
                'enabled' => (bool) $generalSetting->maintenance_mode,
            ],
            [
                'key' => 'force_ssl',
                'title' => __('Force SSL'),
                'description' => __('Redirect all traffic to secure HTTPS connections.'),
                'image' => 'ssl.png',
                'enabled' => (bool) $generalSetting->force_ssl,
            ],
            [
                'key' => 'user_api',
                'title' => __('User API'),
                'description' => __('Allow API access for user applications such as mobile and desktop clients.'),
                'image' => 'api.png',
                'enabled' => (bool) $generalSetting->user_api,
            ],
        ];
    @endphp

    <div class="manage-section-card-form">
        <div class=" manage-section-card mb-32">
            <form action="{{ route('admin.setting.configuration.update') }}" class="ajax-form" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="card mb-3 cfg-hero">
                    <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
                        <div>
                            <p class="small text-uppercase fw-semibold mb-1 cfg-hero__eyebrow">@lang('Admin Controls')</p>
                        </div>
                        <p class="text-muted mb-0">@lang('Enable or disable core platform behavior from one place.')</p>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach ($configurations as $configuration)
                        <div class="col-12 col-lg-6">
                            <div class="card h-100 cfg-item" data-config-item>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div class="d-flex gap-3">
                                            <div class="cfg-item__icon rounded d-flex align-items-center justify-content-center flex-shrink-0">
                                                <img src="{{ asset('assets/images/configurations/' . $configuration['image']) }}"
                                                    alt="{{ $configuration['title'] }}">
                                            </div>
                                            <div>
                                                <label for="{{ $configuration['key'] }}" class="fw-semibold mb-1 d-block cursor-pointer">
                                                    {{ $configuration['title'] }}
                                                </label>
                                                <p class="text-muted mb-0">{{ $configuration['description'] }}</p>
                                            </div>
                                        </div>

                                        <span class="badge rounded-pill cfg-status {{ $configuration['enabled'] ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ $configuration['enabled'] ? __('ON') : __('OFF') }}
                                        </span>
                                    </div>

                                    <div class="mt-3 d-flex justify-content-end">
                                        <label class="cfg-switch mb-0">
                                            <input type="hidden" name="{{ $configuration['key'] }}" value="0">
                                            <input type="checkbox" id="{{ $configuration['key'] }}"
                                                name="{{ $configuration['key'] }}" value="1"
                                                @checked($configuration['enabled'])>
                                            <span class="cfg-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-outline-theme">
                          <i class="fas fa-save"></i>@lang('Save')
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card{
            border-radius: 12px!important;
        }
       
        .card-body {
            border-radius: 12px!important;
        }
        .cfg-hero {
            border: 1px solid hsl(var(--border-color));
        }

        .cfg-hero__eyebrow {
            color: hsl(var(--theme-color));
            letter-spacing: .08em;
        }

        .cfg-item {
            border: 1px solid hsl(var(--theme-color) / .15);
            transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
        }

        .cfg-item:hover {
            border-color: hsl(var(--theme-color));
            transform: translateY(-1px);
            box-shadow: var(--box-shadow);
        }

        .cfg-item__icon {
            width: 56px;
            height: 56px;
            background: hsl(var(--sec-bg-color));
            border: 1px solid hsl(var(--border-color));
        }

        .cfg-item__icon img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .cfg-switch {
            position: relative;
            display: inline-block;
            width: 54px;
            height: 30px;
            cursor: pointer;
        }

        .cfg-switch input[type="checkbox"] {
            position: absolute;
            width: 0;
            height: 0;
            opacity: 0;
        }

        .cfg-slider {
            position: absolute;
            inset: 0;
            border-radius: 100px;
            background: hsl(var(--bs-color-secondary) / .35);
            transition: background-color .22s ease;
        }

        .cfg-slider::before {
            content: "";
            position: absolute;
            left: 4px;
            top: 4px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: hsl(var(--white-color));
            box-shadow: 0 2px 6px rgba(0, 0, 0, .2);
            transition: transform .22s ease;
        }

        .cfg-switch input[type="checkbox"]:checked + .cfg-slider {
            background: hsl(var(--theme-color));
        }

        .cfg-switch input[type="checkbox"]:checked + .cfg-slider::before {
            transform: translateX(24px);
        }

        .cfg-switch input[type="checkbox"]:focus-visible + .cfg-slider {
            box-shadow: 0 0 0 3px hsl(var(--theme-color) / .2);
        }
    </style>
@endpush

@push('scripts')
    <script>
        'use strict';
        (function($) {
            function syncStatus($card) {
                var checked = $card.find('.cfg-switch input[type="checkbox"]').is(':checked');
                var $badge = $card.find('.cfg-status');
                $badge.text(checked ? @json(__('ON')) : @json(__('OFF')));
                $badge.removeClass('text-bg-success text-bg-secondary');
                $badge.addClass(checked ? 'text-bg-success' : 'text-bg-secondary');
            }

            $(document).ready(function() {
                $('[data-config-item]').each(function() {
                    syncStatus($(this));
                });

                $(document).on('change', '.cfg-switch input[type="checkbox"]', function() {
                    syncStatus($(this).closest('[data-config-item]'));
                });
            });
        })(jQuery);
    </script>
@endpush
