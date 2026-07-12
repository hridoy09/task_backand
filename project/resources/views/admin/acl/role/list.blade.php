@extends('admin.layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="table-wrapper table-responsive">
                <table class="table theme-tab-listle responsive-table-sm">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Title')</th>
                            <th>@lang('Created At')</th>
                            <th class="text-end">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->title }}</td>
                                <td>{{ System::getDateTime($role->created_at) }}</td>
                                <td>

                                    <a class="btn btn-outline-theme btn-sm"
                                        href="{{ route('admin.acl.role.edit', $role->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button class="btn btn-outline-danger btn-sm confirmBtn"
                                        data-action="{{ route('admin.acl.role.delete', $role->id) }}" data-question="@lang('Are you sure to delete this role?')">
                                        <i class="fas fa-trash"></i>
                                    </button>

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
            </div>

            <x-paginate :table="$roles" />
        </div>
    </div>
    <x-confirm-modal />
@endsection

@push('breadcrumb')
    <button type="button" data-action="{{ route('admin.acl.ability.generate') }}" data-question="@lang('Are you sure to generate abilities?')" class="btn btn-outline-theme confirmBtn ">
        <i class="fas fa-cogs"></i>@lang('Generate Abilities')
    </button>
@endpush
