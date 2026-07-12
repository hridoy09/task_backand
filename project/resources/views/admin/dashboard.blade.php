@extends('admin.layouts.master')
@section('content')
    @if ($canViewUsers)
        <div class="widget-wrapper">
            <x-widgets.one title="{{ __('Total Users') }}" value="{{ $widget['total_users'] }}" icon="user"
                link="{{ route('admin.user.list') }}" />

            <x-widgets.one title="{{ __('Email Unverified Users') }}" value="{{ $widget['email_unverified_users'] }}"
                icon="user_unverified" shape="user_unverified" link="{{ route('admin.user.email.unverified') }}" />

            <x-widgets.one title="{{ __('New Users') }}" value="{{ $widget['new_users'] }}" icon="new_user" shape="new_user"
                link="{{ route('admin.user.new_list') }}" />

            <x-widgets.one title="{{ __('Profile Incomplete Users') }}" value="{{ $widget['incmoplete_profile_users'] }}"
                icon="profile_incomplate" shape="new_user" link="{{ route('admin.user.incomplete') }}" />
        </div>
    @endif
    @if ($canViewTickets)
        <div class="widget-wrapper">
            <x-widgets.three title="{{ __('Total Tickets') }}" value="{{ $widget['total_tickets'] }}" icon="ticket"
                link="{{ route('admin.support_ticket.list') }}" />
            <x-widgets.three title="{{ __('Open Tickets') }}" value="{{ $widget['open_tickets'] }}" icon="open_ticket"
                link="{{ route('admin.support_ticket.open') }}" />
            <x-widgets.three title="{{ __('Answered Tickets') }}" value="{{ $widget['answered_tickets'] }}"
                icon="ticket_answare" link="{{ route('admin.support_ticket.answered') }}" />
            <x-widgets.three title="{{ __('Closed Support Tickets') }}" value="{{ $widget['closed_tickets'] }}"
                icon="ticket_closed" link="{{ route('admin.support_ticket.closed') }} " />

        </div>
    @endif

    <div class="widget-wrapper">

        <x-widgets.four title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.four title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.four title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.four title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
    </div>
    <div class="widget-wrapper">
        <x-widgets.five title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.five title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.five title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.five title="{{ __('Demo') }}" value="00" icon="globe" link="#" />

    </div>
    <div class="widget-wrapper">
        <x-widgets.six title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.six title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.six title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.six title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
    </div>
    <div class="widget-wrapper">
        <x-widgets.seven title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.seven title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.seven title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
        <x-widgets.seven title="{{ __('Demo') }}" value="00" icon="globe" link="#" />
    </div>
    <div class="row justify-content-center gy-3 mb-4 triple-column-row">
        <div class="col-xxl-4 col-md-6">
            <div class="card top-worker-card">
                <div class="card-header">
                    <h5 class="card-header__title">Top Ranking Worker</h5>
                    <a href="#" class="card-header__link">See All</a>
                </div>
                <div class="card-body">
                    <div class="top-worker-item">
                        <div class="top-worker-item__avatar">
                            <img src="assets/images/thumbs/top-worker-avatar.png" class="image-fitted" alt="">
                        </div>
                        <div class="top-worker-item__content">
                            <h6 class="top-worker-item__name">Jakob Septimus</h6>
                            <p class="top-worker-item__id">#1022345</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-md-6">
            <div class="card top-worker-card">
                <div class="card-header">
                    <h5 class="card-header__title">Top Ranking Worker</h5>
                    <a href="#" class="card-header__link">See All</a>
                </div>
                <div class="card-body">
                    <div class="top-worker-item">
                        <div class="top-worker-item__avatar">
                            <img src="assets/images/thumbs/top-worker-avatar.png" class="image-fitted" alt="">
                        </div>
                        <div class="top-worker-item__content">
                            <h6 class="top-worker-item__name">Jakob Septimus</h6>
                            <p class="top-worker-item__id">#1022345</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-md-6">
            <div class="card top-worker-card">
                <div class="card-header">
                    <h5 class="card-header__title">Top Ranking Worker</h5>
                    <a href="#" class="card-header__link">See All</a>
                </div>
                <div class="card-body">
                    <div class="top-worker-item">
                        <div class="top-worker-item__avatar">
                            <img src="assets/images/thumbs/top-worker-avatar.png" class="image-fitted" alt="">
                        </div>
                        <div class="top-worker-item__content">
                            <h6 class="top-worker-item__name">Jakob Septimus</h6>
                            <p class="top-worker-item__id">#1022345</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center gy-3 triple-column-row">
        <div class="col-xxl-4 col-md-6">
            <div class="card financial-report-card">
                <div class="card-header">
                    <h5 class="card-header__title">Top Ranking Worker</h5>
                </div>
                <div class="card-body">
                    <div id="apexchart-one"></div>
                </div>
            </div>
        </div>


    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            const el = document.querySelector('#apexchart-one');
            if (!el || typeof ApexCharts === 'undefined') return;

            // Safely parse Laravel data
            const payload = JSON.parse(`@json($chartTransactions ?? ['labels' => [], 'credit' => [], 'debit' => []])`);

            //  ApexChart One
            var options = {
                series: [{
                        name: "@lang('Credit')",
                        data: payload.credit
                    },
                    {
                        name: "@lang('Debit')",
                        data: payload.debit
                    }
                ],

                chart: {
                    type: "bar",
                    height: 237,
                    toolbar: {
                        show: false
                    }
                },

                colors: ["#24A19C", "#E76F51"],

                title: {
                    text: 'January - June 2024',
                    align: 'left',
                    offsetX: 0,
                    style: {
                        fontSize: '12px',
                        fontWeight: 400,
                        color: '#1A1A1C'
                    }
                },

                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "45%",
                        borderRadius: 2, // smooth rounded bars
                    }
                },

                dataLabels: {
                    enabled: false
                },

                stroke: {
                    show: false
                },

                grid: {
                    show: false
                },

                xaxis: {
                    categories: [],
                    show: false,
                    labels: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    axisBorder: {
                        show: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },

                yaxis: {
                    min: 0,
                    max: 100,
                    tickAmount: 4,
                    labels: {
                        style: {
                            fontSize: "12px",
                        }
                    }
                },

                legend: {
                    show: false
                },

                tooltip: {
                    y: {
                        formatter: (val) => val
                    }
                }
            };
            var chart = new ApexCharts(document.querySelector("#apexchart-one"), options);
            chart.render();

        });
    </script>
@endpush
