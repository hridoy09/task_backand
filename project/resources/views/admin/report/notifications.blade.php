@extends('admin.layouts.master')

@section('content')
    <div class="widget-wrapper">
        <x-widgets.seven title="{{ __('Total') }}" value="{{ $notifications->total() }}" icon="globe" />
        <x-widgets.seven title="{{ __('Unread') }}" value="{{ $unreadNotification }}" icon="globe" />
        <x-widgets.seven title="{{ __('Page') }}"
            value="{{ $notifications->currentPage() }} / {{ $notifications->lastPage() }}" icon="globe" />
    </div>

    <div class="notification-list">
        @forelse ($notifications as $notification)
            @php $isUnread = !$notification->is_read; @endphp
            <div class="card notif-card {{ $isUnread ? 'notif-unread' : '' }} mb-1">
                <div class="card-body d-flex align-items-start justify-content-between">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="notif-icon">
                            🔔
                        </div>
                        <div>
                            <a href="{{ route('admin.report.notifications.read', $notification->id) }}"
                                class="fw-semibold text-dark text-decoration-none">
                                {{ str()->limit($notification->details, 150) }}
                            </a>
                            <div class="text-muted small mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.report.notifications.read', $notification->id) }}"
                            class="btn btn-sm btn-outline-theme">
                            @if ($isUnread)
                                <i class="fas fa-eye"></i>
                            @else
                                <i class="fas fa-desktop"></i>
                            @endif
                        </a>
                        <button confirm href="{{ route('admin.report.notifications.delete', $notification->id) }}"
                            class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="card text-center">
                <x-admin-no-data :title="__('No Notifications Found')" />
            </div>
        @endforelse
    </div>

        @if ($notifications->hasPages())
        <div class="mt-4">
            {!! $notifications->links() !!}
        </div>
    @endif

@endsection

@push('breadcrumb')
    <x-form.search />
    @if ($unreadNotification > 0)
        <a class="btn btn-outline-theme" href="{{ route('admin.report.notifications.read.all') }}">
            @lang('Mark all as read')
        </a>
    @endif

@endpush

@push('styles')
    <style>
        .card {
            border-radius: 10px !important;
        }

        .card-body {
            border-radius: 10px !important;
        }

        .notification-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .notif-card {
            border-left: 4px solid transparent;
            transition: 0.2s;
            margin-bottom: 0;
        }

        .notif-card:hover {
            background-color: #f9fafc;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .notif-card.notif-unread {
            border-left-color: #914CFF;
            background-color: #eef5ff;
        }

        .notif-icon {
            font-size: 1.5rem;
            line-height: 1;
        }
    </style>
@endpush
