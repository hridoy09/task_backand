@extends('admin.layouts.master')
@section('content')
    <div class="table-wrapper table-responsive">
        <table class="table theme-tab-listle responsive-table-sm">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Total Blogs')</th>
                    <th>@lang('Status')</th>
                    <th class="text-end">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $blogCategory)
                    <tr>
                        <td>{{ $blogCategory->name }}</td>
                        <td>
                            <div>
                                <span>{{ $blogCategory->blogs_count }}</span>
                                <a href="{{ route('admin.blog.catagorised', $blogCategory->id) }}">@lang('View')</a>
                            </div>
                        </td>
                        <td>@php echo $blogCategory->statusBadge; @endphp</td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                @if ($blogCategory->status == 0)
                                    <button class="btn btn-outline-success confirmBtn"
                                        data-question="{{ __('Are you sure to enable this?') }}"
                                        data-action="{{ route('admin.blog_category.status', $blogCategory->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @else
                                    <button class="btn btn-outline-danger confirmBtn"
                                        data-question="{{ __('Are you sure to disable this?') }}"
                                        data-action="{{ route('admin.blog_category.status', $blogCategory->id) }}">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                @endif

                                <button class="btn btn-outline-theme editBtn"
                                    data-action="{{ route('admin.blog_category.save', $blogCategory->id) }}"
                                    data-name="{{ $blogCategory->name }}">
                                    <i class="fas fa-edit"></i>
                                </button>
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

    <x-modal id="addModal" title="Add Blog Category" :form="true" method="POST"
        action="{{ route('admin.blog_category.save') }}">
        @csrf

        <x-form.group>
            <x-form.label>@lang('Name')</x-form.label>
            <x-form.input required name="name" :placeholder="__('Enter the blog category name')" />
        </x-form.group>

        <x-slot:footer>
            <button class="btn btn-outline-theme" type="submit">
           <i class="fas fa-save"></i>
                @lang('Save')
            </button>
        </x-slot:footer>
    </x-modal>
    <x-confirm-modal />
@endsection

@push('breadcrumb')
    <x-form.search />
    <button class="btn btn-outline-theme addBtn">
    <i class="fas fa-plus"></i>
        @lang('Add New')
    </button>
@endpush



@push('scripts')
    <script>
        $('.addBtn').on('click', function() {
            $('#addModal').find('form').attr('action', "{{ route('admin.blog_category.save') }}");
            $('#addModal').find('.modal-title').text("{{ __('Add New Blog Category') }}");
            $('#addModal').find('[name="name"]').val('');
            $('#addModal').modal('show');
        });

        $('.editBtn').on('click', function() {
            const data = $(this).data();
            $('#addModal').find('form').attr('action', data.action);
            $('#addModal').find('.modal-title').text(data.title);
            $('#addModal').find('[name="name"]').val(data.name);
            $('#addModal').modal('show');
        });
    </script>
@endpush
