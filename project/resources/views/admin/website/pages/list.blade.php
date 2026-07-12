@extends('admin.layouts.master')
@section('content')
    <div class="table-responsive">
        <table class="table responsive-table-sm manage-page-table">
            <thead>
                <th>@lang('Page Title')</th>
                <th>@lang('Privacy')</th>
                <th>@lang('Default')</th>
                <th>@lang('Created At')</th>
                <th>@lang('Action')</th>
            </thead>
            <tbody>

                @foreach ($pages as $page)
                    <tr>
                        <td>
                            {{ $page->title }}
                        </td>
                        <td>@php echo $page->privacyPageBadge @endphp </td>
                        <td>@php echo $page->is_default_badge @endphp </td>

                        <td>{{ software()->getDateTime($page->created_at) }}</td>
                        <td>

                            <a class="btn btn-outline-theme" href="{{ route('admin.website.page.edit', $page->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                            @unless ($page->is_default)
                                <button class="btn btn-outline-danger confirmBtn" data-question="@lang('Are you sure to delete this page?')"
                                    data-action="{{ route('admin.website.page.delete', $page->id) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endunless

                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        <x-paginate :table="$pages" />
    </div>
    <x-confirm-modal />
@endsection

@push('breadcrumb')

<x-back link="{{ route('admin.website.page.list') }}" />
<a class="btn btn-outline-theme" href="{{ route('admin.website.page.new') }}" ><i class="fas fa-plus"></i>@lang('Add New')</a>

@endpush
