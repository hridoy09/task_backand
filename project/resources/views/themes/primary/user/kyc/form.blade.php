@extends('theme::user.layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card>
                <div class="card-header">
                    <h4 class="mb-0">{{ $title }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.kyc.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <x-dynamic-form slug="kyc-form" />

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">@lang('Submit KYC')</button>
                        </div>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
