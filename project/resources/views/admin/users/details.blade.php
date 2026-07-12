@extends('admin.layouts.master')
@section('content')
    <div class="widget-wrapper">

        <x-widgets.four title="Number of Transactions"
            link="{{ route('admin.report.transactions') }}?search={{ $user->email }}"
            value="{{ $widget['number_of_transactions'] }}" icon="user" />
        <x-widgets.four title="Total Payment Amount" link="{{ route('admin.user.list') }}"
            value="{{ System::amountWithCurrency($widget['total_payment']) }}" icon="user" />

    </div>

    <div class="card ">
        <div class="card-header">
            <h5 class="card-header__title">@lang('User Data')</h5>
        </div>
        <form action="{{ route('admin.user.save', $user->id) }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="first_name" class="form-label">@lang('First Name')</label>
                            <input type="text" class="form-control" value="{{ $user->first_name }}"
                                placeholder="@lang('Enter first name')" name="first_name" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="last_name" class="form-label">@lang('Last Name')</label>
                            <input type="text" class="form-control" value="{{ $user->last_name }}"
                                placeholder="@lang('Enter last name')" name="last_name" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="name" class="form-label">@lang('Name')</label>
                            <input type="text" class="form-control" value="{{ $user->name }}"
                                placeholder="@lang('Enter name')" name="name" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="username" class="form-label">@lang('Username')</label>
                            <input type="text" class="form-control" value="{{ $user->username }}"
                                placeholder="@lang('Enter username')" name="username" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="email" class="form-label">@lang('Email')</label>
                            <input type="email" class="form-control" value="{{ $user->email }}"
                                placeholder="@lang('Enter email address')" name="email" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="phone_number" class="form-label">@lang('Phone Number')</label>
                            <input type="phone_number" class="form-control" value="{{ $user->phone_number }}"
                                placeholder="@lang('Enter phone number')" name="phone_number" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="city" class="form-label">@lang('City')</label>
                            <input type="text" class="form-control" value="{{ $user->city }}"
                                placeholder="@lang('Enter city name')" name="city" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="zipcode" class="form-label">@lang('Zip Code')</label>
                            <input type="text" class="form-control" value="{{ $user->zipcode }}"
                                placeholder="@lang('Enter zip code')" name="zipcode" />
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="zipcode" class="form-label">@lang('Country')</label>
                            <select class="js-select2" name="country_code">
                                @foreach (countries() as $country)
                                    <option @selected($country['code'] == $user->country_code) value="{{ $country['code'] }}">
                                        {{ __($country['name']) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <select data-search="false" class="js-select2" required name="kyc_required">
                                <option @selected($user->kyc_required == 1) value="1">@lang('Yes')</option>
                                <option @selected($user->kyc_required == 0) value="0">@lang('No')</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <select data-search="false" class="js-select2" required name="kyc_verified">
                                <option @selected($user->kyc_verified == 1) value="1">@lang('Yes')</option>
                                <option @selected($user->kyc_verified == 0) value="0">@lang('No')</option>
                            </select>
                        </div>

                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <select data-search="false" class="js-select2" name="pc" required>
                                <option @selected($user->pc == 1) value="1">@lang('Yes')</option>
                                <option @selected($user->pc == 0) value="0">@lang('No')</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            <select data-search="false" class="js-select2" name="email_verified" required>
                                <option @selected(intval(!!$user->email_verified_at) == 1) value="1">@lang('Yes')</option>
                                <option @selected(intval(!!$user->email_verified_at) == 0) value="0">@lang('No')</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-outline-theme">
                        <x-icons.save />
                        @lang('Save')
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-4 g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header__title">
                        <x-icons.send />
                        @lang('Send Notification')
                    </h5>
                </div>
                <form action="{{ route('admin.user.notify', $user->id) }}" method="POST">
                    <div class="card-body">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-4 >
                                <label class="form-label">@lang('Channel')</label>
                                <select data-search="false" class="js-select2" name="channel">
                                    <option value="email">@lang('Email')</option>
                                    <option value="sms">@lang('SMS')</option>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">@lang('Subject')</label>
                                <input type="text" name="subject" class="form-control"
                                    placeholder="@lang('Only required for emails')" />
                            </div>

                            <div class="col-12">
                                <label class="form-label">@lang('Message')</label>
                                <textarea name="message" rows="4" class="form-control" placeholder="@lang('Type your message')"></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-outline-theme">
                                <x-icons.send />
                                <span class="ms-2">@lang('Send Now')</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center card-header">
                    <h5 class="card-header__title">@lang('Notification Logs')</h5>
                    <small class="text-muted">
                        @lang('Showing latest :count', ['count' => $notifications->count()])
                    </small>
                </div>

                <div class="card-body">
                    <div class="notification-log-list">
                        @forelse ($notifications as $notification)
                            <div class="notification-log-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">{{ $notification->details }}</span>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if ($notification->link)
                                    <a href="{{ $notification->link }}" class="small text-decoration-none">
                                        @lang('Open link')
                                    </a>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                @lang('No notification activity yet.')
                            </div>
                        @endforelse
                    </div>

                    <div class="text-end mt-2">
                        <button href="{{ route('admin.report.notifications') }}" class="btn- btn btn-outline-theme">
                            @lang('View All Logs')
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('breadcrumb')
    <a target="_blank" href="{{ route('admin.user.login', $user->id) }}" class="btn btn-outline-theme">
        <x-icons.login />
        <span class="ms-2">@lang('Login as User')</span>
    </a>

    <x-back :link="route('admin.user.list')" />
@endpush

