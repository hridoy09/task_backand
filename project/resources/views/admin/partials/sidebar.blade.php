<div class="sidebar-menu">
    <div class="sidebar-menu__inner">
        <span class="sidebar-menu__close d-lg-none d-inline-flex">
            <i class="las la-angle-double-left"></i>
        </span>

        <div class="sidebar-menu__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-menu__logo-link">
                <img src="{{ System::logo(true) }}" alt="logo">
            </a>
        </div>

        <ul class="sidebar-menu-list">

            {{-- MAIN --}}
            @if (userCan('view-dashboard'))
                <li class="menu-title">@lang('Main')</li>
                <li class="sidebar-menu-list__item {{ activeClass('admin.dashboard') }}">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.dashboard-v1 /></span>
                        <span class="text">@lang('Dashboard')</span>
                    </a>
                </li>
            @endif

            {{-- PEOPLE --}}
            @if (userCan('view-members') || userCan('view-user'))
                <li class="menu-title">@lang('People')</li>
            @endif

            @if (userCan('view-members'))
                <li class="sidebar-menu-list__item {{ activeClass('admin.admin.*') }}">
                    <a href="{{ route('admin.admin.list') }}" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.users-v1 /></span>
                        <span class="text">@lang('Admins')</span>
                    </a>
                </li>
            @endif

            @if (userCan('view-user'))
                <li class="sidebar-menu-list__item has-dropdown {{ activeClass('admin.user.*') }}">
                    <a href="javascript:void(0)" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.users-v1 /></span>
                        <span class="text">@lang('Users')</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul class="sidebar-submenu-list">
                            <li>
                                <a href="{{ route('admin.user.list') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.user.list') }}">@lang('All Users')</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.user.email.unverified') }}"
                                    class="sidebar-submenu-list__link d-flex justify-content-between {{ activeClass('admin.user.email.unverified') }}">
                                    @lang('Email Unverified')
                                    @if (($emailUnverifiedUsers ?? false) > 0)
                                        <span
                                            class="badge rounded-pill badge-danger">{{ $emailUnverifiedUsers }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a
                                    class="sidebar-submenu-list__link {{ route('admin.user.mobile.unverified') }}">@lang('Mobile Unverified')</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.user.incomplete') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.user.incomplete') }}">
                                    @lang('Incomplete Profile')
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.user.new_list') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.user.new_list') }}">
                                    @lang('New Users')
                                </a>
                            </li>
                            <li>
                                <a
                                    href="{{ route('admin.user.kyc.pending') }}"class="sidebar-submenu-list__link d-flex justify-content-between {{ activeClass('admin.user.kyc.pending') }}">
                                    @lang('KYC Pending')
                                    @if (($kycPendingUsers ?? false) > 0)
                                        <span class="badge rounded-pill badge-danger">{{ $kycPendingUsers }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-list__item {{ activeClass('admin.feed.*') }}">
                    <a href="{{ route('admin.feed.list') }}" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.newspaper /></span>
                        <span class="text">@lang('Feeds')</span>
                    </a>
                </li>
            @endif

            {{-- SUPPORT --}}
            @if (userCan('view-support-departments') || userCan('view-support-tickets'))
                <li class="menu-title">@lang('Help / Support')</li>
            @endif

            @if (userCan('view-support-departments'))
                <li class="sidebar-menu-list__item {{ activeClass('admin.support_department.list') }}">
                    <a href="{{ route('admin.support_department.list') }}" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.life-buoy /></span>
                        <span class="text">@lang('Support Departments')</span>
                    </a>
                </li>
            @endif

            @if (userCan('view-support-tickets'))
                <li class="sidebar-menu-list__item has-dropdown {{ activeClass('admin.support_ticket.*') }}">
                    <a href="javascript:void(0)" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.ticket /></span>
                        <span class="text">@lang('Support Tickets')</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul class="sidebar-submenu-list">
                            <li>
                                <a href="{{ route('admin.support_ticket.list') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.support_ticket.list') }}">@lang('All Tickets')</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.support_ticket.open') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.support_ticket.open') }}">@lang('Open')</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.support_ticket.answered') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.support_ticket.answered') }}">@lang('Answered')</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.support_ticket.closed') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.support_ticket.closed') }}">@lang('Closed')</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- BLOG --}}
            @if (userCan('view-blog-categories') || userCan('view-blog-posts'))
                <li class="menu-title">@lang('Blog')</li>
            @endif

            @if (userCan('view-blog-categories'))
                <li class="sidebar-menu-list__item {{ activeClass('admin.blog_category.list') }}">
                    <a href="{{ route('admin.blog_category.list') }}" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.tag /></span>
                        <span class="text">@lang('Blog Categories')</span>
                    </a>
                </li>
            @endif

            @if (userCan('view-blog-posts'))
                <li class="sidebar-menu-list__item has-dropdown {{ activeClass('admin.blog.*') }}">
                    <a href="javascript:void(0)" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.newspaper /></span>
                        <span class="text">@lang('Blog')</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul class="sidebar-submenu-list">
                            <li>
                                <a href="{{ route('admin.blog.list') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.blog.list') }}">@lang('All Blogs')</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.blog.unpublished') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.blog.unpublished') }}">@lang('Unpublished Blogs')</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.blog.published') }}"
                                    class="sidebar-submenu-list__link {{ activeClass('admin.blog.published') }}">@lang('Published Blogs')</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- WEBSITE --}}
            @if (userCan(['view-website.pages', 'view-website.sections']))
                <li class="menu-title">@lang('Website / Frontend')</li>
                <li class="sidebar-menu-list__item has-dropdown {{ activeClass('admin.website.*') }}">
                    <a href="javascript:void(0)" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.globe /></span>
                        <span class="text">@lang('Website')</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul class="sidebar-submenu-list">
                            @if (userCan('view-website.pages'))
                                <li>
                                    <a href="{{ route('admin.website.page.list') }}"
                                        class="sidebar-submenu-list__link {{ activeClass('admin.website.page.*') }}">@lang('Pages')</a>
                                </li>
                            @endif
                            @if (userCan('view-website.sections'))
                                <li>
                                    <a href="{{ route('admin.website.section.list') }}"
                                        class="sidebar-submenu-list__link {{ activeClass('admin.website.section.*') }}">@lang('Sections')</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{-- REPORTS --}}
            @if (userCan(['view-reports.notifications', 'view-reports.transactions', 'view-reports.admin-logins']))
                <li class="menu-title">@lang('Reports / Logs')</li>
                <li class="sidebar-menu-list__item has-dropdown {{ activeClass('admin.report.*') }}">
                    <a href="javascript:void(0)" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.scroll-text /></span>
                        <span class="text">@lang('Reports')</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul class="sidebar-submenu-list">
                            @if (userCan('view-reports.notifications'))
                                <li><a
                                        href="{{ route('admin.report.notifications') }}"
                                        class="sidebar-submenu-list__link {{ activeClass('admin.report.notifications') }}">@lang('Notifications')</a></li>
                            @endif
                            @if (userCan('view-reports.transactions'))
                                <li><a
                                        href="{{ route('admin.report.transactions') }}"
                                        class="sidebar-submenu-list__link {{ activeClass('admin.report.transactions') }}">@lang('Transactions')</a></li>
                            @endif
                            @if (userCan('view-reports.admin-logins'))
                                <li><a
                                        href="{{ route('admin.report.admin_login') }}"
                                        class="sidebar-submenu-list__link {{ activeClass('admin.report.admin_login') }}">@lang('Admin Logins')</a></li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{-- PAYMENT --}}
            @if (userCan('view-payment-gateways') || userCan('view-payments'))
                <li class="menu-title">@lang('Payment')</li>
            @endif

            @if (userCan('view-payment-gateways'))
                <li class="sidebar-menu-list__item {{ activeClass('admin.payment_gateway.*') }}">
                    <a href="{{ route('admin.payment_gateway.list') }}" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.badge-dollar-sign /></span>
                        <span class="text">@lang('Payment Gateways')</span>
                    </a>
                </li>
            @endif

            @if (userCan('view-payments'))
                <li class="sidebar-menu-list__item has-dropdown {{ activeClass('admin.payment.*') }}">
                    <a href="javascript:void(0)" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.credit-card /></span>
                        <span class="text">@lang('Payments')</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul class="sidebar-submenu-list">
                            <li class="{{ activeClass('admin.payment.list') }}"><a
                                    href="{{ route('admin.payment.list') }}"
                                    class="sidebar-submenu-list__link">@lang('All Payments')</a></li>
                            <li class="{{ activeClass('admin.payment.pending') }}"><a
                                    href="{{ route('admin.payment.pending') }}"
                                    class="sidebar-submenu-list__link">@lang('Pending Payments')</a></li>
                            <li class="{{ activeClass('admin.payment.successfull') }}"><a
                                    href="{{ route('admin.payment.successfull') }}"
                                    class="sidebar-submenu-list__link">@lang('Successful Payments')</a></li>
                            <li class="{{ activeClass('admin.payment.failed') }}"><a
                                    href="{{ route('admin.payment.failed') }}"
                                    class="sidebar-submenu-list__link">@lang('Failed Payments')</a></li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- SETTINGS --}}
            @if (userCan('view-settings.general-settings') ||
                    userCan('view-settings.notifications') ||
                    userCan('view-settings.system-configuration') ||
                    userCan('view-mail-templates') ||
                    userCan('view-acl') ||
                    userCan('view-misc.update') ||
                    userCan('view-misc.cache') ||
                    userCan('view-misc.custom-css'))
                <li class="menu-title">@lang('Settings & Configuration')</li>
            @endif

            @if (userCan('view-mail-templates'))
                <li class="sidebar-menu-list__item {{ activeClass('admin.setting.mail_template.*') }}">
                    <a href="{{ route('admin.setting.mail_template.index') }}" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.send /></span>
                        <span class="text">@lang('Mail Templates')</span>
                    </a>
                </li>
            @endif

            @if (userCan(['view-settings.general-settings', 'view-settings.notifications', 'view-settings.system-configuration']))
                <li class="sidebar-menu-list__item has-dropdown {{ activeClass('admin.setting.*') }}">
                    <a href="javascript:void(0)" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.setting /></span>
                        <span class="text">@lang('Settings')</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul class="sidebar-submenu-list">
                            @if (userCan('view-settings.general-settings'))
                                <li class="{{ activeClass('admin.setting.general') }}"><a
                                        href="{{ route('admin.setting.general') }}"
                                        class="sidebar-submenu-list__link">@lang('General Settings')</a></li>
                            @endif
                            @if (userCan('view-settings.notifications'))
                                <li class="{{ activeClass('admin.setting.notification') }}"><a
                                        href="{{ route('admin.setting.notification') }}"
                                        class="sidebar-submenu-list__link">@lang('Notification Settings')</a></li>
                            @endif
                            @if (userCan('view-settings.system-configuration'))
                                <li class="{{ activeClass('admin.setting.configuration') }}"><a
                                        href="{{ route('admin.setting.configuration') }}"
                                        class="sidebar-submenu-list__link">@lang('System Configuration')</a></li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{-- ACL --}}
            @if (userCan('view-acl'))
                <li class="sidebar-menu-list__item has-dropdown {{ activeClass('admin.acl.*') }}">
                    <a href="javascript:void(0)" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.shield /></span>
                        <span class="text">@lang('ACL')</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul class="sidebar-submenu-list">
                            <li class="{{ activeClass('admin.acl.role.list') }}"><a
                                    href="{{ route('admin.acl.role.list') }}"
                                    class="sidebar-submenu-list__link">@lang('Roles')</a></li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- MISCELLANEOUS --}}
            @if (userCan(['view-misc.update', 'view-misc.cache', 'view-misc.custom-css']))
                <li class="sidebar-menu-list__item has-dropdown {{ activeClass('admin.miscell.*') }}">
                    <a href="javascript:void(0)" class="sidebar-menu-list__link">
                        <span class="icon"><x-icons.circle-ellipsis /></span>
                        <span class="text">@lang('Miscellaneous')</span>
                    </a>
                    <div class="sidebar-submenu">
                        <ul class="sidebar-submenu-list">
                            @if (userCan('view-misc.update'))
                                <li class="{{ activeClass('admin.miscell.update') }}"><a
                                        href="{{ route('admin.miscell.update') }}"
                                        class="sidebar-submenu-list__link">@lang('Update')</a></li>
                            @endif
                            @if (userCan('view-misc.cache'))
                                <li class="{{ activeClass('admin.miscell.cache') }}"><a
                                        href="{{ route('admin.miscell.cache') }}"
                                        class="sidebar-submenu-list__link">@lang('Cache')</a></li>
                            @endif
                            @if (userCan('view-misc.custom-css'))
                                <li class="{{ activeClass('admin.miscell.custom.css') }}"><a
                                        href="{{ route('admin.miscell.custom.css') }}"
                                        class="sidebar-submenu-list__link">@lang('Custom CSS')</a></li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{-- LOGOUT --}}
            <li class="menu-divider"></li>
            <li class="sidebar-menu-list__item">
                <a href="{{ route('admin.logout') }}" class="sidebar-menu-list__link">
                    <span class="icon"><x-icons.logout /></span>
                    <span class="text">@lang('Logout')</span>
                </a>
            </li>

        </ul>
    </div>
</div>
