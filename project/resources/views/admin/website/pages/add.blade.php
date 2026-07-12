@extends('admin.layouts.master')

@section('content')
    <form method="POST" action="{{ route('admin.website.page.save') }}" id="pageLayoutForm">

        @csrf
        <div class="card mb-32">
            <div class="card-body">

                <x-form.group label="Page Title">
                    <x-form.input required name="title" :placeholder="__('Enter a page title')" />
                </x-form.group>

                
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-outline-theme">
                <i class="fas fa-save"></i>
                @lang('Save')
            </button>
        </div>

    </form> {{-- End of form --}}
@endsection


@push('breadcrumb')
    <x-back link="{{ route('admin.website.page.list') }}" />
@endpush
