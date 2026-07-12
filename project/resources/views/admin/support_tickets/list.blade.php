@extends('admin.layouts.master')

@section('content')

    <div class="table-wrapper table-responsive">
        <table class="table theme-tab-listle responsive-table-sm">
            <thead>
                <tr>
                    <th>@lang('Title')</th>
                    <th>@lang('Department')</th>
                    <th>@lang('Submitted By')</th>
                    <th>@lang('Priority')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Last Replied By')</th>
                    <th>@lang('Updated')</th>
                    <th class="text-end">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $t)
                    @php
                        $ticketUser = $t->user;
                    @endphp
                    <tr>
                        <td>{{ $t->title }}</td>
                        <td>{{ $t->department?->name ?? __('N/A') }}</td>
                        <td data-label="@lang('Name')">
                            @if ($ticketUser)
                                <div>
                                    <a href="{{ route('admin.user.details', $ticketUser->id) }}"
                                        class="fw-semibold text-decoration-none">
                                        {{ $ticketUser->name ?? ($ticketUser->username ?? 'User #' . $ticketUser->id) }}
                                    </a> <br>
                                    <small class="text-muted">
                                        {{ $ticketUser->email ?? '—' }}
                                    </small>
                                </div>
                            @else
                                <span class="text-muted">@lang('User unavailable')</span>
                            @endif
                        </td>
                        <td>@php echo $t->priorityBadge; @endphp</td>
                        <td>@php echo $t->statusBadge; @endphp</td>
                        <td>
                            @if ($t->last_replied_by)
                                {{ optional(\App\Models\Admin::find($t->last_replied_by))->name ?? __('Admin') }}
                                <div class="text-muted small">
                                    {{ optional($t->last_replied_at)->diffForHumans() }}
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ System::getDateTime($t->updated_at) }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a class="btn btn-outline-theme" href="{{ route('admin.support_ticket.edit', $t->id) }}">
                                    <i class="fas fa-desktop"></i>
                                </a>
                                <button class="btn btn-outline-danger confirmBtn" data-question="@lang('Are you sure you want to delete this support ticket?')" 
                                    data-action="{{ route('admin.support_ticket.delete', $t->id) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center text-muted">
                            <x-admin-no-data />
                        </td>
                    </tr>0
                @endforelse
            </tbody>
        </table>
        <x-admin-paginate :model="$data" />
    </div>
    <x-confirm-modal />
@endsection
