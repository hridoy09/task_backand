@extends('admin.layouts.master')

@section('content')
    <div class="table-wrapper table-responsive">
        <table class="table theme-tab-listle responsive-table-sm">
            <thead>
                <tr>
                    <th>@lang('User')</th>
                    <th>@lang('Type')</th>
                    <th>@lang('Content')</th>
                    <th>@lang('Privacy')</th>
                    <th>@lang('Likes')</th>
                    <th>@lang('Created')</th>
                    <th class="text-end">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $feed)
                    <tr>
                        <td data-label="@lang('User')">
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">{{ $feed->user->name ?? 'User #' . $feed->user_id }}</span>
                                <small class="text-muted">{{ $feed->user->email ?? '-' }}</small>
                            </div>
                        </td>
                        <td>{{ ucfirst($feed->type) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit(strip_tags($feed->body ?? ''), 60) ?: '-' }}</td>
                        <td>{{ ucfirst($feed->privacy) }}</td>
                        <td>{{ $feed->likes_count }}</td>
                        <td>{{ $feed->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3 justify-content-end">
                                <a class="btn btn-outline-theme" href="{{ route('admin.feed.details', $feed->id) }}">
                                    <i class="fas fa-desktop"></i>
                                </a>
                            </div>
                        </td>
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

        <x-paginate :table="$data" />
    </div>
@endsection

@push('breadcrumb')
    <x-form.search />
@endpush
