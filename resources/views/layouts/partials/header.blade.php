<div class="header">
    <div class="header-left">{{--
        <div class="menu-icon dw dw-menu"></div> --}}{{--
        <div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div> --}}
        <div class="header-search">
            <div class="brand-logo">
                <a href="{{url('/')}}">
                    <img src="{{ asset('deskapp/vendors/images/logo-white.png') }}" alt="" class="dark-logo">
                    <img src="{{ asset('deskapp/vendors/images/logo-dark.png') }}" alt="" class="light-logo">
                </a>
            </div>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-primary btn-sm ml-2 dropdown-toggle" type="button" id="menuDropdown"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="dw dw-menu"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list" aria-labelledby="menuDropdown">
                <a class="dropdown-item" href="{{ route('dashboard.index') }}">
                    <i id="dashboard-icon" class="dw dw-analytics-8"></i> Dashboard
                </a>
                <a class="dropdown-item" href="{{ route('trades.index') }}">
                    <i class="dw dw-table"></i> Trades
                </a>
                <a class="dropdown-item" href="{{ url('/calendar') }}">
                    <i id="calendar-icon" class="dw dw-calendar"></i> Calendar
                </a>
                <!-- <a class="dropdown-item" href="{{ url('/charts') }}">
                    <i class="dw dw-bar-chart1"></i> Charts
                </a> -->
                <a class="dropdown-item" href="{{ route('rules_tips.index') }}">
                    <i class="dw dw-list"></i> Trading Guidelines
                </a>
                {{-- <a class="dropdown-item refresh_view_btn" href="{{ url('/calendar') }}">
                    <i class="dw dw-refresh1"></i> Refresh View
                </a> --}}
            </div>
        </div>


        <button class="btn btn-outline-primary btn-sm ml-2 toggle-sidebar-btn">
            <i class="dw dw-filter"></i>
        </button>


    </div>
    <div class="header-right">
        <div class="dashboard-setting user-notification">

        </div>
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <span class="user-icon">
                        <img src="{{ asset('milogo.png') }}" alt="">
                    </span>
                    <span class="user-name">Admin</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item update_bettica_data_btn" href="javascript:void(0)">
                        <i class="dw dw-settings2"></i> Update Bettica Data
                    </a>
                </div>
            </div>
        </div>
        <div class="github-link">
            <a href="https://github.com/dropways/deskapp" target="_blank"><img src="vendors/images/github.svg"
                    alt=""></a>
        </div>
    </div>
</div>

@yield('left-sidebar')
