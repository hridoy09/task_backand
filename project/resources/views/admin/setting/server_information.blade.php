@extends('admin.layouts.settings')

@section('panel')
    <div class="manage-section-card-form">
        <div class="manage-section-card mb-32">
            <div class="row g-3">
                @foreach ($serverInformation as $sectionTitle => $infoItems)
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-header__title mb-0">{{ __($sectionTitle) }}</h4>
                            </div>

                            <div class="card-body">
                                @if (empty($infoItems))
                                    <p class="text-muted mb-0">@lang('No information available for this section.')</p>
                                @else
                                    <div class="server-info-list">
                                        @php $itemCount = 0; @endphp
                                        @foreach ($infoItems as $key => $value)
                                            @continue($itemCount >= 10 && $sectionTitle !== 'PHP Configuration')
                                            @php
                                                $valueClass = '';
                                                if (is_bool($value)) {
                                                    $displayValue = $value ? __('Yes') : __('No');
                                                    $valueClass = $value ? 'status-yes' : 'status-no';
                                                } elseif (is_string($value)) {
                                                    $lowerVal = strtolower($value);
                                                    if ($lowerVal === 'connected') {
                                                        $valueClass = 'status-connected';
                                                    } elseif (in_array($lowerVal, ['enabled', 'yes'])) {
                                                        $valueClass = 'status-enabled';
                                                    } elseif (in_array($lowerVal, ['disabled', 'no'])) {
                                                        $valueClass = 'status-disabled';
                                                    } elseif (
                                                        str_contains(strtolower($key), 'error') ||
                                                        str_contains($lowerVal, 'error') ||
                                                        str_contains($lowerVal, 'could not connect')
                                                    ) {
                                                        $valueClass = 'status-error';
                                                    } elseif (
                                                        str_contains(strtolower($key), 'debug mode') &&
                                                        $lowerVal === 'enabled'
                                                    ) {
                                                        $valueClass = 'status-warning';
                                                    }
                                                    $displayValue = __($value);
                                                } elseif (is_null($value)) {
                                                    $displayValue = __('N/A');
                                                } else {
                                                    $displayValue = is_array($value)
                                                        ? json_encode($value, JSON_PRETTY_PRINT)
                                                        : __((string) $value);
                                                }
                                            @endphp

                                            <div class="server-info-item">
                                                <p class="server-info-key mb-0">{{ __($key) }}</p>
                                                <div class="server-info-value {{ $valueClass }}">{{ $displayValue }}</div>
                                            </div>
                                            @php $itemCount++; @endphp
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .server-info-hero {
            border: 1px solid hsl(var(--border-color));
            border-radius: 12px!important;
        }

        

        .server-info-hero__eyebrow {
            color: hsl(var(--theme-color));
            letter-spacing: .08em;
        }

        .server-info-card {
            border: 1px solid hsl(var(--theme-color) / .15);
            transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
        }

        .server-info-card:hover {
            border-color: hsl(var(--theme-color));
            transform: translateY(-1px);
            box-shadow: var(--box-shadow);
        }

        .server-info-list {
            border-top: 1px solid hsl(var(--border-color) / .65);
        }

        .server-info-item {
            display: grid;
            grid-template-columns: minmax(140px, 42%) 1fr;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid hsl(var(--border-color) / .65);
        }

        .server-info-key {
            font-size: 0.9rem;
            font-weight: 600;
            color: hsl(var(--bs-color-secondary));
            word-break: break-word;
        }

        .server-info-value {
            font-size: 0.9rem;
            color: hsl(var(--dark-color));
            overflow-wrap: anywhere;
            word-break: break-word;
            text-align: right;
        }

        .server-info-value.status-enabled,
        .server-info-value.status-yes,
        .server-info-value.status-connected {
            color: hsl(var(--success-color));
            font-weight: 600;
        }

        .server-info-value.status-disabled,
        .server-info-value.status-no,
        .server-info-value.status-error {
            color: hsl(var(--danger-color));
            font-weight: 600;
        }

        .server-info-value.status-warning {
            color: hsl(var(--warning-color));
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .server-info-item {
                grid-template-columns: 1fr;
                gap: 6px;
                padding: 10px 0;
            }

            .server-info-key,
            .server-info-value {
                font-size: 0.85rem;
            }

            .server-info-value {
                text-align: left;
            }
        }
    </style>
@endpush
