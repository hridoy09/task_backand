@extends('theme::user.layouts.main')

@section('content')
    <div class="container py-4">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2>@lang('Payment history')</h2>
            <a href="{{ route('user.payment.new') }}" class="btn btn-primary">@lang('New Payment')</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>@lang('Transaction No')</th>
                    <th>@lang('Gateway')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Created At')</th>
                    <th>@lang('Paid At')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->transaction_no }}</td>
                        <td>{{ $payment->method }}</td>
                        <td>{{ System::amountWithCurrency($payment->amount) }}</td>
                        <td>{{ $payment->status }}</td>
                        <td>{{ System::getDateTime($payment->created_at) }}</td>
                        <td>{{ $payment->paid_at ? System::getDateTime($payment->paid_at) : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($payments->hasPages())
            {{ $payments->links() }}
        @endif

    </div>
@endsection
