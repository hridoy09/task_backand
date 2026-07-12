@extends('admin.layouts.settings')

@section('panel')
    @php
        use App\Models\Integration;

        // Display list (static) + pull saved rows by key to show status & prefill
        $integrations = [
            [
                'key' => 'recaptcha',
                'title' => __('Google reCAPTCHA'),
                'desc' => __('Configure the credentials for Google reCAPTCHA'),
                'img' => 'google_recaptcha.png',
            ],
            // ['key'=>'hcaptcha','title'=>__('hCaptcha'),'desc'=>__('Spam protection alternative to reCAPTCHA'),'img'=>'hcaptcha.png'],
            // ['key'=>'turnstile','title'=>__('Cloudflare Turnstile'),'desc'=>__('Captcha-less user verification by Cloudflare'),'img'=>'turnstile.png'],
            // ['key'=>'ga4','title'=>__('Google Analytics (GA4)'),'desc'=>__('Connect GA4 measurement ID for site analytics'),'img'=>'ga4.png'],
            // ['key'=>'fb_pixel','title'=>__('Facebook Pixel'),'desc'=>__('Enable Meta Pixel tracking for conversions'),'img'=>'facebook_pixel.png'],
            // ['key'=>'hotjar','title'=>__('Hotjar / Clarity'),'desc'=>__('Session recordings & heatmaps'),'img'=>'hotjar.png'],
            // ['key'=>'fcm','title'=>__('Firebase Cloud Messaging'),'desc'=>__('Push notifications via Firebase'),'img'=>'fcm.png'],
            // ['key'=>'pusher','title'=>__('Pusher / Ably'),'desc'=>__('Realtime websockets for events & chats'),'img'=>'pusher.png'],
            [
                'key' => 'slack',
                'title' => __('Slack Webhook'),
                'desc' => __('Send system alerts to Slack channels'),
                'img' => 'slack.png',
            ],
            // ['key'=>'discord','title'=>__('Discord Webhook'),'desc'=>__('Notify a Discord server'),'img'=>'discord.png'],
            // ['key'=>'telegram','title'=>__('Telegram Bot'),'desc'=>__('Bot token & chat ID for notifications'),'img'=>'telegram.png'],
            // ['key'=>'aws_s3','title'=>__('AWS S3 / DO Spaces / Wasabi'),'desc'=>__('External object storage credentials'),'img'=>'s3.png'],
            // ['key'=>'gmaps','title'=>__('Google Maps'),'desc'=>__('Maps JavaScript API & Places API keys'),'img'=>'google_maps.png'],
            // ['key'=>'openai','title'=>__('OpenAI / Anthropic'),'desc'=>__('API keys for AI features'),'img'=>'openai.png'],
            // ['key'=>'zoom','title'=>__('Zoom'),'desc'=>__('OAuth + webhook for meeting scheduling'),'img'=>'zoom.jpg'],
        ];

        // Attach DB rows for quick use (status, settings, id)
        foreach ($integrations as $idx => $it) {
            $row = Integration::where('key', $it['key'])->first();
            $integrations[$idx]['row'] = $row;
        }
    @endphp

    <div class="manage-section-card-form">
        <div class="manage-section-card mb-32">
         
            <div class="row g-3">
                @foreach ($integrations as $i)
                    @php
                        $configured = (bool) optional($i['row'])->exists;
                        $enabled = (bool) optional($i['row'])->enabled;
                        $settings = optional($i['row'])->settings ?? [];
                    @endphp
                    <div class="col-12 col-lg-6">
                        <div class="card h-100 cfg-item">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div class="d-flex gap-3">
                                        <div
                                            class="cfg-item__icon rounded d-flex align-items-center justify-content-center flex-shrink-0">
                                            <img src="{{ asset('assets/images/integrations/' . $i['img']) }}"
                                                alt="{{ $i['title'] }}">
                                        </div>
                                        <div>
                                            <label
                                                class="fw-semibold mb-1 d-block user-select-none">{{ $i['title'] }}</label>
                                            <p class="text-muted mb-2">{{ $i['desc'] }}</p>
                                            @if ($configured)
                                                <span class="badge badge-success">@lang('Configured')</span>
                                            @else
                                                <span class="badge badge-danger">@lang('Not configured')</span>
                                            @endif
                                        </div>
                                    </div>

                                    <span
                                        class="badge rounded-pill {{ $enabled ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $enabled ? __('ON') : __('OFF') }}
                                    </span>
                                </div>

                                <div class="mt-3 d-flex justify-content-end">
                                    <button type="button"
                                        class="btn btn-outline-theme d-inline-flex align-items-center configureBtn"
                                        data-title="{{ $i['title'] }}" data-key="{{ $i['key'] }}"
                                        data-name="{{ $i['title'] }}" data-enabled="{{ $enabled ? 1 : 0 }}"
                                        data-id="{{ optional($i['row'])->id }}"
                                        data-settings='@json($settings)'
                                        data-action-store="{{ route('admin.setting.integration.store') }}"
                                        data-action-update="{{ optional($i['row']) ? route('admin.setting.integration.update', $i['row']->id ?? 0) : '' }}">
                                        <x-icons.cog />
                                        <span class="ms-2">@lang('Configure')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Configure Modal --}}
    <x-modal id="integrationModal" title="{{ __('Configure Integration') }}" :form="true" method="POST"
        action="{{ route('admin.setting.integration.store') }}">
        @csrf
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="id" value="">

        <x-form.group>
            <x-form.label>@lang('Name')</x-form.label>
            <x-form.input name="name" required />
        </x-form.group>

        <x-form.group>
            <x-form.label>@lang('Key')</x-form.label>
            <x-form.input name="key" required readonly />
        </x-form.group>

        <x-form.group>
            <div class="d-flex align-items-center justify-content-between gap-2">
                <label class="form-label mb-0" for="enabledCheck">@lang('Enabled')</label>
                <label class="cfg-switch mb-0">
                    <input type="checkbox" id="enabledCheck" name="enabled" value="1">
                    <span class="cfg-slider"></span>
                </label>
            </div>
        </x-form.group>

        <hr class="my-3">

        <div class="settings-area">
            {{-- JS will inject fields here depending on the key --}}
        </div>

        <x-slot:footer>
            <button class="btn btn-outline-theme" type="submit">
                <x-icons.save />
                @lang('Save')
            </button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('styles')
    <style>
        .cfg-hero {
            border: 1px solid hsl(var(--border-color));
        }

        .card {
            border-radius: 12px !important;
        }

        .card-body {
            border-radius: 12px !important;
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
    </style>
@endpush

@push('scripts')
    <script>
        'use strict';
        (function($) {
            // map of fields per integration key -> [{name,label,type,placeholder}]
            const fieldsByKey = {
                recaptcha: [{
                        name: 'site_key',
                        label: 'Site Key'
                    },
                    {
                        name: 'secret_key',
                        label: 'Secret Key'
                    },
                ],
                hcaptcha: [{
                        name: 'site_key',
                        label: 'Site Key'
                    },
                    {
                        name: 'secret_key',
                        label: 'Secret Key'
                    },
                ],
                turnstile: [{
                        name: 'site_key',
                        label: 'Site Key'
                    },
                    {
                        name: 'secret_key',
                        label: 'Secret Key'
                    },
                ],
                ga4: [{
                    name: 'measurement_id',
                    label: 'Measurement ID (G-XXXXXXX)'
                }, ],
                fb_pixel: [{
                    name: 'pixel_id',
                    label: 'Pixel ID'
                }, ],
                hotjar: [{
                    name: 'site_id',
                    label: 'Site ID'
                }, ],
                fcm: [{
                        name: 'project_id',
                        label: 'Project ID'
                    },
                    {
                        name: 'api_key',
                        label: 'Web API Key'
                    },
                    {
                        name: 'sender_id',
                        label: 'Sender ID'
                    },
                ],
                pusher: [{
                        name: 'app_id',
                        label: 'App ID'
                    },
                    {
                        name: 'key',
                        label: 'Key'
                    },
                    {
                        name: 'secret',
                        label: 'Secret'
                    },
                    {
                        name: 'cluster',
                        label: 'Cluster'
                    },
                ],
                slack: [{
                    name: 'webhook_url',
                    label: 'Webhook URL'
                }, ],
                discord: [{
                    name: 'webhook_url',
                    label: 'Webhook URL'
                }, ],
                telegram: [{
                        name: 'bot_token',
                        label: 'Bot Token'
                    },
                    {
                        name: 'chat_id',
                        label: 'Chat ID'
                    },
                ],
                aws_s3: [{
                        name: 'key',
                        label: 'Access Key'
                    },
                    {
                        name: 'secret',
                        label: 'Secret Key'
                    },
                    {
                        name: 'region',
                        label: 'Region'
                    },
                    {
                        name: 'bucket',
                        label: 'Bucket'
                    },
                    {
                        name: 'endpoint',
                        label: 'Endpoint (optional)'
                    },
                ],
                gmaps: [{
                    name: 'api_key',
                    label: 'API Key'
                }, ],
                openai: [{
                    name: 'api_key',
                    label: 'API Key'
                }, ],
                zoom: [{
                        name: 'client_id',
                        label: 'Client ID'
                    },
                    {
                        name: 'client_secret',
                        label: 'Client Secret'
                    },
                    {
                        name: 'redirect_url',
                        label: 'Redirect URL'
                    },
                ],
            };

            function buildFields(key, settings) {
                const area = $('.settings-area').empty();
                const rows = fieldsByKey[key] || [];
                if (!rows.length) {
                    area.append(
                        `<div class="text-muted small mb-2">{{ __('No specific settings for this integration.') }}</div>`
                        );
                    return;
                }
                rows.forEach(f => {
                    const val = settings?.[f.name] ?? '';
                    const input =
                        `<div class="form-group">
                    <label class="form-label">${f.label}</label>
                    <input type="text" class="form-control" name="settings[${f.name}]" value="${$('<div>').text(val).html()}" placeholder="${f.placeholder ?? ''}">
                </div>`;
                    area.append(input);
                });
            }

            $(document).ready(function() {

                // open modal for existing items
                $('.configureBtn').on('click', function() {
                    const btn = $(this);
                    const modal = $('#integrationModal');

                    const title = btn.data('title') || "{{ __('Configure Integration') }}";
                    const key = btn.data('key');
                    const name = btn.data('name') || title;
                    const id = btn.data('id') || '';
                    const enabled = Number(btn.data('enabled')) === 1;
                    const settings = btn.data('settings') || {};
                    const store = btn.data('action-store');
                    const update = btn.data('action-update');

                    // header
                    modal.find('.modal-title').text(title);

                    // form action + method spoofing
                    modal.find('form').attr('action', id && update ? update : store);
                    modal.find('input[name="_method"]').val(id ? 'POST' :
                    'POST'); // your routes use POST for update
                    modal.find('input[name="id"]').val(id);

                    // base fields
                    modal.find('[name="name"]').val(name);
                    modal.find('[name="key"]').val(key);
                    modal.find('#enabledCheck').prop('checked', enabled);

                    // settings
                    buildFields(key, settings);

                    modal.modal('show');
                });

                // optional: allow "Add Custom Integration" to open blank modal
                $('.addBtn').on('click', function() {
                    const modal = $('#integrationModal');
                    modal.find('.modal-title').text("{{ __('Add Custom Integration') }}");
                    modal.find('form').attr('action',
                    "{{ route('admin.setting.integration.store') }}");
                    modal.find('input[name="_method"]').val('POST');
                    modal.find('input[name="id"]').val('');
                    modal.find('[name="name"]').val('');
                    modal.find('[name="key"]').val('').prop('readonly', false); // allow custom key
                    modal.find('#enabledCheck').prop('checked', true);
                    $('.settings-area').html(
                        '<div class="text-muted small">{{ __('Set the key, save once, then you can extend fieldsByKey in JS for custom fields.') }}</div>'
                        );
                    modal.modal('show');
                });
            });
        })(jQuery);
    </script>
@endpush
