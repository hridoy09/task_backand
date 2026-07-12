@extends('admin.layouts.master')

@section('content')
<div class="container-fluid cache-page">
    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <div class="card cache-card">
                <div class="cache-card-header">
                    <div class="cache-status-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div>
                        <h3 class="cache-title mb-1">@lang('Cache Management')</h3>
                        <p class="cache-subtitle mb-0">@lang('Clear stale files to keep your admin panel and website fast and up to date.')</p>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if(session('cache_cleared'))
                        <div class="alert alert-success cache-alert mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-2"></i>
                                <span><strong>@lang('Success'):</strong> @lang('The selected caches have been cleared.')</span>
                            </div>
                        </div>
                    @endif

                    @php
                        $cacheItems = [
                            ['icon' => 'fas fa-eye', 'label' => __('View Cache'), 'desc' => __('Compiled Blade templates')],
                            ['icon' => 'fas fa-route', 'label' => __('Route Cache'), 'desc' => __('Optimized route map')],
                            ['icon' => 'fas fa-cogs', 'label' => __('Configuration Cache'), 'desc' => __('Cached config values')],
                            ['icon' => 'fas fa-bolt', 'label' => __('Application Cache'), 'desc' => __('General runtime cache')],
                        ];
                    @endphp

                    <div class="cache-grid mb-4">
                        @foreach ($cacheItems as $item)
                            <div class="cache-item">
                                <div class="cache-item-icon">
                                    <i class="{{ $item['icon'] }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $item['label'] }}</h6>
                                    <small>{{ $item['desc'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="cache-note mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <span>@lang('You can safely clear cache at any time. It will be rebuilt automatically when needed.')</span>
                    </div>

                    <form action="{{ route('admin.miscell.cache.clear') }}" method="POST" class="text-center" id="cacheClearForm">
                        @csrf
                        <button class="btn btn-outline-theme btn-clear-cache px-4" type="submit" id="cacheClearBtn">
                            <i class="fas fa-sync-alt me-1"></i>
                            @lang('Clear All Caches')
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            @lang('Last action'):
                            {{ session('cache_cleared') ? now()->toDateTimeString() : __('Not cleared in this session') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .cache-page .cache-card {
        border: 0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 14px 40px rgba(14, 22, 35, 0.08);
    }

    .cache-page .cache-card-header {
        background: hsl(var(--theme-color) / 0.85);
        color: #fff;
        padding: 1.5rem 1.75rem;
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .cache-page .cache-status-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.14);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .cache-page .cache-title {
        font-weight: 700;
        color: #fff;
    }

    .cache-page .cache-subtitle {
        color: rgba(255, 255, 255, 0.78);
    }

    .cache-page .cache-alert {
        border: 0;
        border-radius: 10px;
    }

    .cache-page .cache-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .cache-page .cache-item {
        border: 1px solid #e9edf2;
        border-radius: 12px;
        padding: 0.9rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        background: #fff;
    }

    .cache-page .cache-item h6 {
        font-weight: 600;
        color: #1f2937;
    }

    .cache-page .cache-item small {
        color: #6b7280;
    }

    .cache-page .cache-item-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f3f6fa;
        color: #334155;
    }

    .cache-page .cache-note {
        border-radius: 10px;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e9edf2;
        padding: 0.85rem 1rem;
        font-size: 0.95rem;
    }

    .cache-page .btn-clear-cache {
        border-radius: 10px;
        font-weight: 600;
        padding-top: 0.7rem;
        padding-bottom: 0.7rem;
        min-width: 220px;
    }

    .cache-page .btn-clear-cache i {
        transition: transform 0.2s ease;
    }

    .cache-page .btn-clear-cache:hover i {
        transform: rotate(180deg);
    }

    @media (max-width: 767.98px) {
        .cache-page .cache-grid {
            grid-template-columns: 1fr;
        }

        .cache-page .cache-card-header {
            padding: 1.25rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        var form = document.getElementById('cacheClearForm');
        var button = document.getElementById('cacheClearBtn');

        if (form && button) {
            form.addEventListener('submit', function () {
                button.setAttribute('disabled', 'disabled');
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ __('Clearing...') }}';
            });
        }
    })();
</script>
@endpush
