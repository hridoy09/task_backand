@extends('admin.layouts.master')
@section('content')
    <div class="table-responsive">
        <table class="table responsive-table-sm manage-page-table">
            <thead>
                <th>@lang('IP')</th>
                <th>@lang('City')</th>
                <th>@lang('Browser')</th>
                <th>@lang('OS')</th>
                <th>@lang('Device Type')</th>
                <th>@lang('Login Time')</th>
            </thead>
            <tbody>
                @forelse ($adminLogins as $adminLogin)
                    <tr>
                        <td>{{ $adminLogin->ip }}</td>
                        <td>{{ $adminLogin->city }}</td>
                        <td>{{ $adminLogin->browser }}</td>
                        <td>{{ $adminLogin->os }}</td>
                        <td>{{ $adminLogin->device_type }}</td>
                        <td>

                            {{ System::getDateTime($adminLogin->created_at) }}
                            <br>
                            <strong>{{ $adminLogin->created_at->diffForHumans() }}</strong>

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
        <x-paginate :table="$adminLogins" />
    </div>
@endsection

@push('breadcrumb')
    <x-form.search />
@endpush
