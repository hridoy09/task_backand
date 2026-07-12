@extends('admin.layouts.settings')

@section('panel')
    <div class="manage-section-card-form">
        <div class="card manage-section-card mb-32">
            <div class="row">
                <div class="col-lg-12">
                    <form class="ajax-form" action="{{ route('admin.setting.general.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-header__title">@lang('General Setting')</h4>
                            </div>

                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <x-form.group for="site_title" label="Site Title"
                                            help="Site title is required to show throught the admin panel and website">
                                            <x-form.input name="site_title" required
                                                value="{{ old('site_title', $generalSetting['site_title']) }}" />
                                        </x-form.group>
                                    </div>
    
                                    <div class="col-md-6">
                                        <x-form.group for="site_email" label="Site Email">
                                            <input type="email" name="site_email" id="site_email" class="form-control"
                                                value="{{ old('site_email', $generalSetting['site_email']) }}">
                                        </x-form.group>
                                    </div>
    
                                    <div class="col-md-6">
                                        <x-form.group for="site_phone" label="Site Phone">
                                            <input type="text" name="site_phone" id="site_phone" class="form-control"
                                                value="{{ old('site_phone', $generalSetting['site_phone']) }}">
                                        </x-form.group>
                                    </div>
    
                                    <div class="col-md-6">
                                        <x-form.group for="app_url" label="App Url">
                                            <input type="text" name="app_url" id="app_url" class="form-control"
                                                value="{{ old('app_url', $generalSetting['app_url']) }}">
                                        </x-form.group>
                                    </div>
    
                                    <div class="col-md-4">
                                        <x-form.group for="currency" label="System Currency">
                                            <select name="currency" class="form-control js-select2"
                                                data-placeholder="@lang('Select A Currency')" id="currency">
                                                @foreach (System::currencies() as $currency)
                                                    <option @selected($generalSetting['currency'] == $currency['code']) value="{{ $currency['code'] }}">
                                                        {{ $currency['code'] }} - {{ $currency['name'] }} -
                                                        {{ html_entity_decode($currency['symbol']) }}</option>
                                                @endforeach
                                            </select>
                                        </x-form.group>
                                    </div>
    
                                    <div class="col-md-4">
                                        <x-form.group for="timezone" label="Timezone">
                                            <select name="timezone" class="form-control select2"
                                                data-placeholder="@lang('Select A Timezone')" id="timezone">
                                                @foreach (DateTimeZone::listIdentifiers() as $tz)
                                                    <option @selected($generalSetting['timezone'] == $tz) value="{{ $tz }}">
                                                        {{ $tz }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </x-form.group>
                                    </div>
    
                                    <div class="col-md-4">
                                        <x-form.group for="demo_mode" label="Demo Mode"
                                            help="Whether the system is in demo mode or not">
                                            <select name="demo_mode" class="form-control">
                                                <option @selected(1 == generalSetting('demo_mode')) value="1">@lang('Yes')</option>
                                                <option @selected(0 == generalSetting('demo_mode')) value="0">@lang('No')</option>
                                            </select>
                                        </x-form.group>
                                    </div>
    
                                    <div class="col-md-4">
                                        <x-form.group for="admin_prefix" label="Admin Prefix"
                                            help="It will set the prefix for admin panel">
                                            <x-form.input name="admin_prefix" required
                                                value="{{ old('admin_prefix', $generalSetting['admin_prefix']) }}" />
                                        </x-form.group>
                                    </div>
    
                                    @if (config('system.routes.user'))
                                        <div class="col-md-4">
                                            <x-form.group for="user_prefix" label="User Prefix"
                                                help="It will set the prefix for user dashboard">
                                                <x-form.input name="user_prefix" required
                                                    value="{{ old('user_prefix', $generalSetting['user_prefix']) }}" />
                                            </x-form.group>
                                        </div>
                                    @endif
    
                                    <div class="col-md-4">
                                        <x-form.group for="app_env" label="App ENV"
                                            help="This will set the app env production/local">
                                            <select name="app_env" class="form-control">
                                                <option @selected('production' == generalSetting('app_env')) value="production">@lang('Production')
                                                </option>
                                                <option @selected('local' == generalSetting('app_env')) value="local">@lang('Local')</option>
                                            </select>
                                        </x-form.group>
                                    </div>
    
                                    <div class="col-md-12">
                                        <x-form.group for="site_description" label="Site Description">
                                            <textarea name="site_description" id="site_description" rows="4" class="form-control">{{ old('site_description', $generalSetting['site_description']) }}</textarea>
                                        </x-form.group>
                                    </div>
    
                                   
                                    <div class="col-md-6">
                                         <x-uploade-image id="darkLogo" dark="true" name="site_logo_dark" :path="imageSrc($generalSetting['site_logo_dark'] ?? null)"  />
                                    </div>
    
                                   
                                    <div class="col-md-6">
                                         <x-uploade-image id="liteLogo" name="site_logo" :path="imageSrc($generalSetting['site_logo'] ?? null)"  />

                               
                                    </div>
    
                                    <div class="col-md-6">
                                         <x-uploade-image id="favicon" name="site_favicon" :path="imageSrc($generalSetting['site_favicon'] ?? null)"  />
                                    </div>
    
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-outline-theme">
                                            <i class="fas fa-save"></i>
                                            @lang('Save')
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection