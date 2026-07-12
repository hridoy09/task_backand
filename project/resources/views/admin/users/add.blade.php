@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-3">@lang('Add New User')</h3>

    <x-button href="{{ route('admin.user.list') }}">
        <x-icons.back-v1 />
        @lang('Back')
    </x-button>
</div>

<div class="card">
    
    <div class="card-body">

        <form action="{{ route('admin.user.save') }}" method="POST">
            @csrf 
            
            <div class="row gy-4">

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="name" class="form-label">@lang('Name')</label>
                        <input type="text" class="form-control" value="{{ old('name') }}" placeholder="@lang('Enter name')" name="name" />
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="username" class="form-label">@lang('Username')</label>
                        <input type="text" class="form-control" value="{{ old('username') }}" placeholder="@lang('Enter username')" name="username" />
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="email" class="form-label">@lang('Email')</label>
                        <input type="email" class="form-control" value="{{ old('email') }}" placeholder="@lang('Enter email address')" name="email" />
                    </div>
                </div> 

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="password" class="form-label">@lang('Password')</label>
                        <input type="password" class="form-control" placeholder="@lang('Enter a password')" name="password" />
                    </div>
                </div> 

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">@lang('Password Confirmation')</label>
                        <input type="password_confirmation" class="form-control" value="{{ old('email') }}" placeholder="@lang('Confirm the password')" name="password_confirmation" />
                    </div>
                </div> 

            </div>

            <div class="d-flex justify-content-end mt-4">
                <x-button type="submit">
                    <x-icons.save />
                    @lang('Save')
                </x-button>
            </div>


        </form>

    </div>
</div>

@endsection