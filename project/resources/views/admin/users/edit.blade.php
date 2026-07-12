@extends('admin.layouts.app')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-3 text-xl">@lang('Edit User')</h3>

    <div>
        <x-button target="_blank" href="{{ route('admin.user.login', $user->id) }}">@lang('Login as User')</x-button>
        <x-button href="{{ route('admin.user.list') }}">
            <x-icons.back-v1 />
            @lang('Back')
        </x-button>
    </div>
</div>

<div class="card">
    
    <div class="card-body">

        <form action="{{ route('admin.user.save', $user->id) }}" method="POST">
            @csrf 
            
            <div class="row gy-4">

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="first_name" class="form-label">@lang('First Name')</label>
                        <input type="text" class="form-control" value="{{ $user->first_name }}" placeholder="@lang('Enter first name')" name="first_name" />
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="last_name" class="form-label">@lang('Last Name')</label>
                        <input type="text" class="form-control" value="{{ $user->last_name }}" placeholder="@lang('Enter last name')" name="last_name" />
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="username" class="form-label">@lang('Username')</label>
                        <input type="text" class="form-control" value="{{ $user->username }}" placeholder="@lang('Enter username')" name="username" />
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="email" class="form-label">@lang('Email')</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" placeholder="@lang('Enter email address')" name="email" />
                    </div>
                </div> 

                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="phone_number" class="form-label">@lang('Phone Number')</label>
                        <input type="text" class="form-control" value="{{ $user->phone_number }}" placeholder="@lang('Enter phone number')" name="phone_number" />
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