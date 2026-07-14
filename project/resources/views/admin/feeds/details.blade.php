@extends('admin.layouts.master')

@section('content')
    <div class="widget-wrapper">
        <x-widgets.four title="Feed Likes" link="{{ route('admin.feed.list') }}"
            value="{{ $feed->likes_count }}" icon="user" />
        <x-widgets.four title="Comments Count" link="{{ route('admin.feed.list') }}"
            value="{{ $feed->comments_count }}" icon="user" />
        <x-widgets.four title="Shares Count" link="{{ route('admin.feed.list') }}"
            value="{{ $feed->shares_count }}" icon="user" />
        <x-widgets.four title="Post Type" link="{{ route('admin.feed.list') }}"
            value="{{ ucfirst($feed->type) }}" icon="user" />
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header__title">@lang('Feed Details')</h5>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">@lang('User')</label>
                    <div>{{ $feed->user->name ?? 'User #' . $feed->user_id }}</div>
                    <small class="text-muted">{{ $feed->user->email ?? '-' }}</small>
                </div>

                <div class="col-lg-3">
                    <label class="form-label fw-semibold">@lang('Privacy')</label>
                    <div>{{ ucfirst($feed->privacy) }}</div>
                </div>

                <div class="col-lg-3">
                    <label class="form-label fw-semibold">@lang('Created At')</label>
                    <div>{{ $feed->created_at->diffForHumans() }}</div>
                </div>

                <div class="col-lg-12">
                    <label class="form-label fw-semibold">@lang('Body')</label>
                    <div class="border rounded p-3">{{ $feed->body ?: __('No content') }}</div>
                </div>

                @if ($feed->media_path)
                    <div class="col-lg-12">
                        <label class="form-label fw-semibold">@lang('Media')</label>
                        <div class="border rounded p-3">
                            @if ($feed->media_type === 'video')
                                <video controls class="w-100 rounded">
                                    <source src="{{ asset($feed->media_path) }}">
                                </video>
                            @else
                                <img src="{{ asset($feed->media_path) }}" alt="feed-media" class="img-fluid rounded">
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-header__title">@lang('Liked Users')</h5>
        </div>
        <div class="card-body">
            <div class="table-wrapper table-responsive">
                <table class="table theme-tab-listle responsive-table-sm">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Email')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($feed->likedByUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email ?? '-' }}</td>
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
        </div>
    </div>
@endsection

@push('breadcrumb')
    <x-back :link="route('admin.feed.list')" />
@endpush
