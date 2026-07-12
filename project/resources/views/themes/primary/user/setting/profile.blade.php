@extends('theme::user.layouts.main')

@section('content')
    <h1>user profile</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('user.setting.profile.save') }}" method="POST">
                @csrf 

                <div class="row g-3">
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label class="form-label" for="first_name">@lang('First Name')</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" />
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label class="form-label" for="last_name">@lang('Last Name')</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" />
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label class="form-label" for="username">@lang('Username')</label>
                            <input type="text" class="form-control" name="username" id="username" value="{{ old('username', $user->username) }}" />
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label class="form-label" for="city">@lang('City')</label>
                            <input type="text" class="form-control" name="city" id="city" value="{{ old('city', $user->city) }}" />
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label class="form-label" for="zipcode">@lang('Zipcode')</label>
                            <input type="text" class="form-control" name="zipcode" id="zipcode" value="{{ old('zipcode', $user->zipcode) }}" />
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="form-group">
                            <label class="form-label" for="country_code">@lang('Country')</label>
                            <select class="form-control" name="country_code" id="country_code">
                                @foreach (countries() as $country)
                                    <option value="{{ $country['code'] }}" @selected($country['code'] == $user->country_code)>{{ __($country['name']) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="form-group">
                            <label class="form-label" for="address">@lang('Address')</label>
                            <textarea name="address" id="address" class="form-control">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>

@endsection