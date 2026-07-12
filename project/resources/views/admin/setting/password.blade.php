@extends('admin.layouts.app')

@section('content')
    <x-page-header :page_title="__('Password Setting')" />

    <div class="row">
        <div class="col-lg-8 offset-md-2">
            <form action="{{ route('admin.setting.password.update') }}" method="POST">
                @csrf
                <x-card>
                    <x-form.group label="Old Password">
                        <x-form.input type="password" name="old_password" autofocus required />
                    </x-form.group>

                    <x-form.group label="New Password">
                        <x-form.input type="password" name="password" required />
                    </x-form.group>

                    <x-form.group label="Password Confirmation">
                        <x-form.input type="password" name="password_confirmation" required />
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
