@extends('admin.layouts.master')
@section('content')
    <div class="table-wrapper table-responsive">
        <table class="table theme-tab-listle responsive-table-sm">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Email')</th>
                    <th>@lang('Username')</th>
                    <th>@lang('Phone Number')</th>
                    <th>@lang('Role')</th>
                    <th>@lang('Created At')</th>
                    <th class="text-end">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $member)
                    <tr>
                        <td>{{ __($member->name) }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->username }}</td>
                        <td>
                            <a
                                @if ($member->phone_number) href="tel:{{ $member->phone_number }}" @endif>{{ $member->phone_number ?? 'N/A' }}</a>
                        </td>
                        <td>{{ optional($member->role)->name }}</td>
                        <td>{{ System::getDateTime($member->created_at) }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a class="btn btn-outline-theme" href="{{ route('admin.admin.edit', $member->id) }}">
                                    <i class="fas fa-edit"></i>
                                  
                                </a>
                                <button class="btn btn-outline-danger confirmBtn"
                                    data-action="{{ route('admin.admin.delete', $member->id) }}"
                                    data-question="@lang('Are you sure you want to delete this admin?')">
                                  <i class="fas fa-trash"></i>
                                   
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted p-0">
                            <x-admin-no-data />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <x-paginate :table="$data" />
    </div>

    <x-confirm-modal />
@endsection

@push('breadcrumb')

<a href="{{ route('admin.admin.create') }}" class="btn btn-outline-theme"><i class="fas fa-plus"></i>@lang('Add Admin')</a>

@endpush
