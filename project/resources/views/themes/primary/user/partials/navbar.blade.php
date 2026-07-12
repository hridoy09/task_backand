@php
    $pages = \App\Models\Page::where('is_default', 0)->get();
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ System::logo() }}" class="logo" alt="Logo" />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">

                {{-- Home --}}
                <li class="nav-item">
                    <a class="nav-link {{ activeClass('home') }}" href="{{ route('home') }}">@lang('Home')</a>
                </li>

                {{-- Pages --}}
                @foreach ($pages as $page)
                    <li class="nav-item">
                        <a class="nav-link {{ activeClass('site.page', $page->slug) }}" href="{{ route('site.page', $page->slug) }}">
                            {{ __($page->title) }}
                        </a>
                    </li>
                @endforeach

                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ activeClass('user.support.list') }}" href="{{ route('user.support.list') }}">
                            @lang('Support Tickets')
                        </a>
                    </li>

                    {{-- User Dropdown --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">@lang('Dashboard')</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.payment.history') }}">@lang('Payments')</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.payment.new') }}">@lang('New Payment')</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.setting.profile') }}">@lang('Profile')</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.2fa.settings') }}">@lang('2Fa')</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">
                                        <x-icons.logout /> @lang('Logout')
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth

                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ activeClass('login') }}" href="{{ route('login') }}">@lang('Login')</a>
                    </li>

                    @if(generalSetting('user_registration'))
                        <li class="nav-item">
                            <a class="nav-link {{ activeClass('register') }}" href="{{ route('register') }}">@lang('Register')</a>
                        </li>
                    @endif
                @endguest
            </ul>
        </div>
    </div>
</nav>
