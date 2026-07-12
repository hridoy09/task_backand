@extends('admin.layouts.master')
@section('content')
    <div class="card mb-32">
        <div class="card-header">
            <h5 class="card-header__title">@lang('Admin Details')</h5>
        </div>
        <div class="card-body">
            <form method="POST" id="admin-form" action="{{ route('admin.admin.store', @$model->id) }}">
                @csrf

                <div class="row">
                    <div class="col-lg-6">
                        <x-form.group>
                            <x-form.label>@lang('Name')</x-form.label>
                            <x-form.input name="name" required :placeholder="__('Enter full name')"
                                value="{{ old('name', @$model->name) }}" />
                        </x-form.group>
                    </div>
                    <div class="col-lg-6">
                        <x-form.group>
                            <x-form.label>@lang('Email')</x-form.label>
                            <x-form.input type="email" name="email" required :placeholder="__('Enter email address')"
                                value="{{ old('email', @$model->email) }}" />
                        </x-form.group>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <x-form.group>
                            <x-form.label>@lang('Username')</x-form.label>
                            <x-form.input required name="username" :placeholder="__('Enter an username')"
                                value="{{ old('username', @$model->username) }}" />
                        </x-form.group>
                    </div>

                    <div class="col-lg-6">
                        <x-form.group>
                            <x-form.label>@lang('Phone Number')</x-form.label>
                            <x-form.input name="phone_number" :placeholder="__('Enter a phone number')"
                                value="{{ old('phone_number', @$model->phone_number) }}" />
                        </x-form.group>
                    </div>

                    @if (!@$model)
                        <div class="col-lg-6">
                            <x-form.group>
                                <x-form.label>@lang('Password')</x-form.label>
                                <x-form.input type="password" name="password" :placeholder="__('Enter password')" />
                            </x-form.group>
                        </div>
                    @endif

                    <div class="col-lg-6">
                        <x-form.group>
                            <x-form.label>@lang('Role')</x-form.label>
                          
                                <select  class="js-select2" required name="role_id" >
                                    @foreach ($roles as $role)
                                        <option @isset($member) @selected($member?->roles?->contains('id', $role->id))  @endisset
                                            value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                         
                        </x-form.group>
                    </div>
                </div>

            </form>

        </div>
    </div>
    <div class="text-end">
        <button form="admin-form" class="btn btn-outline-theme" type="submit">
            <x-icons.save />
            @lang('Submit')
        </button>
    </div>
@endsection

@push('breadcrumb')
<x-back :link="route('admin.admin.list')" />
@endpush