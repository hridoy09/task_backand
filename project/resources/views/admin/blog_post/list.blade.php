@extends('admin.layouts.master')

@section('content')
    <div class="table-wrapper table-responsive">
        <table class="table theme-tab-listle responsive-table-sm">
            <thead>
                <th>@lang('Title')</th>
                <th>@lang('Category')</th>
                <th>@lang('Status')</th>
                <th>@lang('Image')</th>
                <th>@lang('Created At')</th>
                <th>@lang('Last Modifed At')</th>
                <th class="text-end">@lang('Action')</th>
            </thead>

            <tbody>
                @forelse ($data as $blog)
                    <tr>
                        <td>{{ $blog->title }}</td>
                        <td>{{ $blog?->category?->name ?? '-' }}</td>
                        <td>@php echo $blog->statusBadge; @endphp</td>
                        <td>
                            @if ($blog->image)
                                <div class="table-author justify-content-center">
                                    <div class="table-author__thumb">
                                        <img src="{{ get_img($blog->image) }}" alt="{{ $blog->title }}" height="40">
                                    </div>
                                </div>
                            @else
                                {{ __('N/A') }}
                            @endif
                        </td>
                        <td>{{ System::getDateTime($blog->created_at) }}</td>
                        <td>{{ System::getDateTime($blog->updated_at) }}</td>
                        <td>

                            <a class="btn btn-outline-theme" href="{{ route('admin.blog.edit', $blog->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>

                            <button class="btn btn-outline-danger" data-question="@lang('Are you sure you want to delete this blog post?')"
                                data-action="{{ route('admin.blog.delete', $blog->id) }}">
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center text-muted">
                            <x-admin-no-data />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <x-admin-paginate :model="$data" />
    </div>
    <x-confirm-modal />
@endsection

@push('breadcrumb')
    <x-form.search />
    <a href="{{ route('admin.blog.create') }}" class="addBtn btn btn-outline-theme"><i
            class="fas fa-plus"></i>@lang('Add New')</a>
@endpush
