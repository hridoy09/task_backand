@extends('admin.layouts.master')

@section('content')
    <div class="table-responsive">
        <table class="table responsive-table-sm manage-page-table">
            <thead class="table-light">
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Code')</th>
                    <th>@lang('Subject')</th>
                    <th>@lang('Attachments')</th>
                    <th>@lang('Updated')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templates as $template)
                    <tr>
                        <td>{{ $template->name }}</td>
                        <td><span class="badge text-bg-light">{{ $template->code }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit($template->subject, 70) }}</td>
                        <td>
                            @if ($template->attachment_collection->isEmpty())
                                <span class="text-muted">@lang('None')</span>
                            @else
                                <span class="badge text-bg-secondary">{{ $template->attachment_collection->count() }}</span>
                            @endif
                        </td>
                        <td>{{ $template->updated_at?->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('admin.setting.mail_template.edit', $template) }}"
                                class="btn btn-outline-theme">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="submit" data-action="{{ route('admin.setting.mail_template.delete', $template) }}"
                                data-question="@lang('Are you sure you want to delete this template?')" class="btn btn-outline-danger confirmBtn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            @lang('No mail templates found yet.')
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
    @if ($templates->hasPages())
        {{ $templates->links() }}
    @endif

    <x-confirm-modal />
@endsection

@push('breadcrumb')
    <a class="btn btn-outline-theme" href="{{ route('admin.setting.mail_template.create') }}">
        <i class="fas fa-plus"></i>
        @lang('Add Template')
    </a>
@endpush
