@extends('admin.layouts.master')

@section('content')
    <div class="table-wrapper table-responsive">
        <table class="table theme-tab-listle responsive-table-sm">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Total Tickets')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $dep)
                    <tr>
                        <td>{{ $dep->name }}</td>
                        <td>
                            <div>
                                <span>{{ $dep->tickets_count }}</span>
                                <a href="{{ route('admin.support_ticket.by_department', $dep->id) }}">@lang('View')</a>
                            </div>
                        </td>
                        <td>@php echo $dep->statusBadge; @endphp</td>
                        <td>
                            <div>
                                @if ($dep->status == 0)
                                    <button class="btn btn-outline-success confirmBtn" 
                                        data-question="{{ __('Enable this department?') }}"
                                        data-action="{{ route('admin.support_department.status', $dep->id) }}">
                                       <i class="fas fa-eye"></i>
                                    </button>
                                @else
                                    <button class="btn btn-outline-danger confirmBtn"
                                        data-question="{{ __('Disable this department?') }}"
                                        data-action="{{ route('admin.support_department.status', $dep->id) }}">
                                      <i class="fas fa-eye-slash"></i>
                                    </button>
                                @endif

                                <button data-title="{{ __('Edit Department') }}" class="editBtn btn btn-outline-theme"
                                    data-action="{{ route('admin.support_department.save', $dep->id) }}"
                                    data-name="{{ $dep->name }}">
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



    <x-modal id="depModal" title="{{ __('Add Department') }}" :form="true" method="POST"
        action="{{ route('admin.support_department.save') }}">
        @csrf
        <x-form.group>
            <x-form.label>@lang('Name')</x-form.label>
            <x-form.input required name="name" :placeholder="__('Enter department name')" />
        </x-form.group>
        <x-slot:footer>
            <button class="btn btn-outline-theme" type="submit">
                <x-icons.save /> @lang('Save')
            </button>
        </x-slot:footer>
    </x-modal>
    <x-confirm-modal />
@endsection

@push('breadcrumb')
    <x-form.search />
    <button class="addBtn btn btn-outline-theme"><x-icons.add /> @lang('Add New')</button>
@endpush


@push('scripts')
    <script>
        $('.addBtn').on('click', function() {
            const $m = $('#depModal');
            $m.find('form').attr('action', "{{ route('admin.support_department.save') }}");
            $m.find('.modal-title').text("{{ __('Add Department') }}");
            $m.find('[name="name"]').val('');
            $m.modal('show');
        });

        $('.editBtn').on('click', function() {
            const data = $(this).data();
            const $m = $('#depModal');
            $m.find('form').attr('action', data.action);
            $m.find('.modal-title').text(data.title);
            $m.find('[name="name"]').val(data.name);
            $m.modal('show');
        });
    </script>
@endpush
