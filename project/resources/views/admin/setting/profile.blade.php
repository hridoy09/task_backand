@extends('admin.layouts.app')

@section('content')
    <x-page-header :page_title="__('Profile Setting')">
        <x-button href="{{ route('admin.setting.password') }}" var="outline-primary" class="d-inline-flex align-items-center">
            <x-icons.key />
            <span class="ms-2">@lang('Edit Password')</span>
        </x-button>
    </x-page-header>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img 
                            class="img-fluid admin-image" 
                            src="{{ asset(admin()->image) }}"
                            alt="image">
                    </div>


                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>@lang('Name')</strong>
                            <span>{{ $admin->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>@lang('Username')</strong>
                            <span>{{ $admin->username }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>@lang('Email')</strong>
                            <span>{{ $admin->email }}</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="col-lg-8">
            <form action="{{ route('admin.setting.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <x-card>
                    <div class="form-group mb-3">
                        <x-file-uploader 
                            name="image" 
                            label="Profile Photo" 
                            :preview="admin()->image ?? null"
                        />
                    </div>

                    <x-form.group label="Name">
                        <x-form.input value="{{ $admin->name }}" :placeholder="__('Enter your name')" name="name" />
                    </x-form.group>

                    <x-form.group class="mt-3" label="Email">
                        <x-form.input value="{{ $admin->email }}" type="email" placeholder="@lang('Enter your email')" name="email" />
                    </x-form.group>

                    <div class="d-flex mt-3 justify-content-end">
                        <x-button type="submit">
                            <x-icons.save />
                            @lang('Save')
                        </x-button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .admin-image {
            max-width: 100px;
            border-radius: 50%;
            margin: auto;
            margin-bottom: 10px;
        }
    </style>
@endpush


@push('scripts')
    <script>
        'use strict';
        
    </script>
@endpush
