@extends('theme::user.layouts.main')

@section('content')

<div class="container py-5">
    <h2>@lang('Support Tickets')</h2>
    <a href="{{ route('user.support.open') }}">@lang('Open Ticket')</a>
    
    <table class="table">
        <thead>
            <tr>
                <th>@lang('Title')</th>
                <th>@lang('Department')</th>
                <th>@lang('Priority')</th>
                <th>@lang('Status')</th>
                <th>@lang('Created At')</th>
                <th>@lang('Last Reply')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tickets as $ticket)
            <tr>
            </tr>
            @empty
                <tr>
                    <td colspan="100%" class="text-center">@lang('No data found')</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@endsection