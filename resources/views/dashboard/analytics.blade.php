@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('deskapp/src/plugins/air-datepicker/dist/css/datepicker.min.css') }}">
<style>
    /* Anti-Gravity Design System */
    :root {
        --ag-primary: #667eea;
        --ag-secondary: #764ba2;
        --ag-success: #00e676;
        --ag-danger: #ff5252;
        --ag-warning: #ffc107;
        --ag-info: #29b6f6;
        --ag-neutral-50: #f8f9fa;
        --ag-neutral-100: #e9ecef;
        --ag-neutral-200: #dee2e6;
        --ag-neutral-700: #495057;
        --ag-neutral-900: #212529;
        --ag-shadow-sm: 0 2px 8px rgba(102, 126, 234, 0.08);
        --ag-shadow-md: 0 4px 16px rgba(102, 126, 234, 0.12);
        --ag-shadow-lg: 0 8px 24px rgba(102, 126, 234, 0.16);
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
    }

    /* Floating Card Base */
    .ag-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: var(--ag-shadow-md);
        border: 1px solid rgba(102, 126, 234, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .ag-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--ag-shadow-lg);
    }

    /* KPI Tiles */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .kpi-tile {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--ag-shadow-md);
        border: 1px solid rgba(102, 126, 234, 0.08);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .kpi-tile:hover {
        transform: translateY(-6px);
        box-shadow: var(--ag-shadow-lg);
    }

    .kpi-tile::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--ag-primary), var(--ag-secondary));
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
        box-shadow: var(--ag-shadow-sm);
    }

    .kpi-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--ag-neutral-700);
        margin-bottom: 8px;
    }

    .kpi-value {
        font-size: 32px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 4px;
    }

    .kpi-change {
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Filter Summary Bar */
    .filter-summary-bar {
        background: linear-gradient(135deg, var(--ag-primary), var(--ag-secondary));
        border-radius: 16px;
        padding: 16px 24px;
        margin-bottom: 30px;
        box-shadow: var(--ag-shadow-md);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-chip {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 500;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-chip i {
        font-size: 10px;
    }

    /* Chart Containers */
    .chart-container {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--ag-shadow-md);
        border: 1px solid rgba(102, 126, 234, 0.08);
        margin-bottom: 24px;
        height: 100%;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--ag-neutral-100);
    }

    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--ag-neutral-900);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chart-title i {
        color: var(--ag-primary);
    }

    /* Custom Widgets */
    .instrument-row {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f3f5;
    }
    .instrument-row:last-child {
        border-bottom: none;
    }
    .instrument-name {
        width: 80px;
        font-weight: 700;
        font-size: 14px;
        color: #333;
        text-transform: uppercase;
    }
    .instrument-bar {
        flex-grow: 1;
        height: 16px;
        background-color: #e9ecef;
        border-radius: 8px;
        display: flex;
        overflow: hidden;
        margin: 0 15px;
    }
    .bar-win {
        background-color: #00e676;
        height: 100%;
    }
    .bar-loss {
        background-color: #ff5252;
        height: 100%;
    }
    .instrument-stats {
        width: 100px;
        text-align: right;
        font-weight: 600;
        font-size: 13px;
        color: #333;
    }

    .session-row {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f3f5;
    }
    .session-row:last-child {
        border-bottom: none;
    }
    .session-name {
        width: 100px;
        font-weight: 700;
        font-size: 14px;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .session-bar-wrapper {
        flex-grow: 1;
        margin: 0 15px;
        position: relative;
        height: 16px;
    }
    .session-bar-bg {
        width: 100%;
        height: 100%;
        background-color: #e9ecef;
        border-radius: 8px;
        position: relative;
    }
    .session-bar-fill {
        height: 100%;
        background-color: #2962ff;
        border-radius: 8px;
    }
    .session-dot {
        width: 12px;
        height: 12px;
        background-color: #000;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
        border: 2px solid #fff;
    }
    .session-stat {
        width: 50px;
        text-align: right;
        font-weight: 600;
        font-size: 13px;
    }

    /* Behavioral Analytics */
    .behavioral-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .behavioral-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--ag-shadow-sm);
        border-left: 4px solid var(--ag-primary);
        transition: all 0.3s ease;
    }

    .behavioral-card:hover {
        transform: translateX(4px);
        box-shadow: var(--ag-shadow-md);
    }

    .behavioral-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--ag-neutral-700);
        margin-bottom: 8px;
    }

    .behavioral-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--ag-neutral-900);
    }

    /* Trade Quality Gauge */
    .quality-gauge {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }

    .gauge-circle {
        transform: rotate(-90deg);
    }

    .gauge-bg {
        fill: none;
        stroke: var(--ag-neutral-100);
        stroke-width: 12;
    }

    .gauge-progress {
        fill: none;
        stroke: url(#gaugeGradient);
        stroke-width: 12;
        stroke-linecap: round;
        transition: stroke-dashoffset 1s ease;
    }

    .gauge-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .gauge-value {
        font-size: 48px;
        font-weight: 700;
        color: var(--ag-primary);
    }

    .gauge-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--ag-neutral-700);
    }

    /* Best/Worst Trade Cards */
    .trade-showcase {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }

    .trade-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--ag-shadow-md);
        border: 1px solid rgba(102, 126, 234, 0.08);
        transition: all 0.3s ease;
    }

    .trade-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--ag-shadow-lg);
    }

    .trade-card-header {
        padding: 20px;
        background: linear-gradient(135deg, var(--ag-primary), var(--ag-secondary));
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .trade-card-header.best {
        background: linear-gradient(135deg, #00e676, #00c853);
    }

    .trade-card-header.worst {
        background: linear-gradient(135deg, #ff5252, #d32f2f);
    }

    .trade-card-body {
        padding: 20px;
    }

    .trade-pair {
        font-size: 24px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .pair-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .trade-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-top: 16px;
    }

    .trade-stat {
        background: var(--ag-neutral-50);
        border-radius: 8px;
        padding: 12px;
    }

    .trade-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--ag-neutral-700);
        margin-bottom: 4px;
    }

    .trade-stat-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--ag-neutral-900);
    }

    /* Section Headers */
    .section-header {
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 3px solid var(--ag-neutral-100);
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--ag-neutral-900);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i {
        color: var(--ag-primary);
        font-size: 28px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }

        .trade-showcase {
            grid-template-columns: 1fr;
        }
    }

    /* Chart Placeholder Styles */
    .chart-placeholder {
        height: 300px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--ag-neutral-700);
        font-weight: 600;
    }

    /* Progress Bars */
    .progress-bar-custom {
        height: 8px;
        border-radius: 4px;
        background: var(--ag-neutral-100);
        overflow: hidden;
        margin-top: 8px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--ag-primary), var(--ag-secondary));
        border-radius: 4px;
        transition: width 1s ease;
    }

    .progress-fill.success {
        background: linear-gradient(90deg, #00e676, #00c853);
    }

    .progress-fill.danger {
        background: linear-gradient(90deg, #ff5252, #d32f2f);
    }

    /* Sidebar Width Override */
    .right-sidebar {
        width: 600px !important;
        right: -600px;
    }
    .right-sidebar.right-sidebar-visible {
        right: 0;
    }
    @media (max-width: 768px) {
        .right-sidebar {
            width: 100% !important;
            right: -100%;
        }
    }
</style>
@endpush

@section('content')

<!-- Filter Sidebar -->
@include('layouts.partials.filter_sidebar')

<!-- Filter Summary Bar -->
<div class="filter-summary-bar">
    <span style="color: #fff; font-weight: 600; margin-right: 8px;">Active Filters:</span>
    <div class="filter-chip" id="filter-date-chip">
        <i class="fa fa-calendar"></i>
        <span id="filter-date-text">All Time</span>
    </div>
    <div class="filter-chip" id="filter-market-chip" style="display:none;">
        <i class="fa fa-chart-line"></i>
        <span id="filter-market-text">All Pairs</span>
    </div>
    <div class="filter-chip" id="filter-session-chip" style="display:none;">
        <i class="fa fa-clock"></i>
        <span id="filter-session-text">All Sessions</span>
    </div>
    <button class="btn btn-sm btn-light ml-auto filter-btn-toggle" style="border-radius: 20px; font-weight: 600; color: var(--ag-primary);">
        <i class="fa fa-filter mr-1"></i> Filters
    </button>
</div>

<!-- KPI Tiles -->
<div class="kpi-grid">
    <!-- Win Rate -->
    <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-start">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #00e676 0%, #00c853 100%); color: #fff; margin-bottom: 0;">
                <i class="fa fa-trophy"></i>
            </div>
            <div class="text-right">
                <div class="kpi-label" style="margin-bottom: 0;">Lifetime</div>
            </div>
        </div>
        <div>
            <div class="kpi-value" id="kpi-win-rate" style="color: var(--ag-success);">{{ $kpis['win_rate'] }}%</div>
            <div class="kpi-label">Win Rate</div>
        </div>
    </div>

    <!-- Monthly/Period Stats -->
    <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-start w-100">
            <div>
                <div class="kpi-label" style="color: var(--ag-primary);">Period P&L</div>
                <div class="kpi-value" id="kpi-pnl" style="font-size: 24px; color: {{ $kpis['monthly_roi'] >= 0 ? 'var(--ag-success)' : 'var(--ag-danger)' }};">
                    ${{ number_format($kpis['monthly_roi'], 2) }}
                </div>
            </div>
            <div class="text-right">
                <div style="font-size: 11px; color: var(--ag-neutral-700); font-weight: 600;">WIN RATE</div>
                <div style="font-size: 18px; font-weight: 700; color: var(--ag-neutral-900);" id="kpi-period-wr">{{ $kpis['monthly_win_rate'] }}%</div>
            </div>
        </div>
        <div class="mt-3 d-flex justify-content-between align-items-end w-100" style="font-size: 13px;">
            <div>
                <span style="color: var(--ag-success); font-weight: 700;" id="kpi-wins">{{ $kpis['monthly_wins'] }}W</span>
                <span style="color: var(--ag-neutral-200);">/</span>
                <span style="color: var(--ag-danger); font-weight: 700;" id="kpi-losses">{{ $kpis['monthly_losses'] }}L</span>
                <div style="font-size: 10px; color: var(--ag-neutral-700); margin-top: 2px;"><span id="kpi-trades">{{ $kpis['monthly_trades'] }}</span> Trades</div>
            </div>
            <div class="text-right">
                <div style="font-size: 10px; color: var(--ag-neutral-700);">TOTAL R:R</div>
                <div style="font-weight: 700; font-size: 16px; color: {{ $kpis['monthly_total_rr'] >= 0 ? 'var(--ag-success)' : 'var(--ag-danger)' }};" id="kpi-rr">{{ $kpis['monthly_total_rr'] }}</div>
            </div>
        </div>
    </div>

    <!-- Streaks (New) -->
    <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-start">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: #fff; margin-bottom: 0;">
                <i class="fa fa-fire"></i>
            </div>
        </div>
        <div>
            <div class="d-flex justify-content-between mt-2">
                <div>
                    <div class="kpi-label">Win Streak</div>
                    <div class="kpi-value" id="streak-win" style="color: var(--ag-success); font-size: 24px;">{{ $streaks['current_win_streak'] }}</div>
                    <div style="font-size: 10px; color: #aaa;">Max: <span id="streak-win-max">{{ $streaks['max_win_streak'] }}</span></div>
                </div>
                <div class="text-right">
                    <div class="kpi-label">Loss Streak</div>
                    <div class="kpi-value" id="streak-loss" style="color: var(--ag-danger); font-size: 24px;">{{ $streaks['current_loss_streak'] }}</div>
                    <div style="font-size: 10px; color: #aaa;">Max: <span id="streak-loss-max">{{ $streaks['max_loss_streak'] }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Day -->
    <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-start">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: #fff; margin-bottom: 0;">
                <i class="fa fa-star"></i>
            </div>
        </div>
        <div>
            <div class="kpi-value" id="kpi-best-day" style="color: var(--ag-warning);">${{ number_format($kpis['best_day'], 2) }}</div>
            <div class="kpi-label">Best Day</div>
        </div>
    </div>

    <!-- Worst Day -->
    <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-start">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #ff5252 0%, #d32f2f 100%); color: #fff; margin-bottom: 0;">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
        </div>
        <div>
            <div class="kpi-value" id="kpi-worst-day" style="color: var(--ag-danger);">${{ number_format($kpis['worst_day'], 2) }}</div>
            <div class="kpi-label">Worst Day</div>
        </div>
    </div>

    <!-- Compliance Score -->
    <div class="kpi-tile">
        <div class="d-flex justify-content-between align-items-start">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%); color: #fff; margin-bottom: 0;">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
        <div>
            <div class="kpi-value" id="kpi-compliance" style="color: var(--ag-secondary);">{{ $kpis['compliance_score'] }}%</div>
            <div class="kpi-label">Compliance</div>
            <div class="progress-bar-custom" style="margin-top: 4px;">
                <div class="progress-fill" id="kpi-compliance-bar" style="width: {{ $kpis['compliance_score'] }}%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Analytics Section -->
<div class="section-header">
    <div class="section-title">
        <i class="fa fa-chart-area"></i>
        <span>Charts & Analytics</span>
    </div>
</div>

<div class="row">
    <!-- Performance Tabs -->
    <div class="col-md-12 mb-4">
        <div class="ag-card">
            <div class="card-header bg-transparent border-0 pd-20">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="section-title mb-0">
                        <i class="fa fa-chart-line text-primary mr-2"></i>
                        <span>Performance</span>
                    </div>
                    <ul class="nav nav-pills card-header-pills mt-2 mt-md-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#tab-equity" role="tab">Equity Curve</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#tab-daily" role="tab">Trading Day</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#tab-monthly" role="tab">Monthly</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body pd-20">
                <div class="tab-content">
                    <!-- Equity Curve Tab -->
                    <div class="tab-pane fade show active" id="tab-equity" role="tabpanel">
                        <div class="chart-container-tab" style="position: relative; height: 350px;">
                            <canvas id="equityCurveChart"></canvas>
                        </div>
                    </div>

                    <!-- Trading Day Tab -->
                    <div class="tab-pane fade" id="tab-daily" role="tabpanel">
                        <div class="chart-container-tab" style="position: relative; height: 350px;">
                            <canvas id="dailyPerformanceChart"></canvas>
                        </div>
                    </div>

                    <!-- Monthly Tab -->
                    <div class="tab-pane fade" id="tab-monthly" role="tabpanel">
                        <div class="chart-container-tab" style="position: relative; height: 350px;">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Most Traded Instruments -->
    <div class="col-md-6 mb-4">
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fa fa-globe"></i>
                    <span>Most Traded 3 Instruments</span>
                </div>
            </div>

            <div class="p-2" id="top-instruments-container">
                @foreach($chartData['top_instruments'] as $instrument)
                <div class="instrument-row">
                    <div class="instrument-name">{{ $instrument['pair'] }}</div>
                    <div class="instrument-bar">
                        <div class="bar-win" style="width: {{ $instrument['win_rate'] }}%"></div>
                        <div class="bar-loss" style="width: {{ 100 - $instrument['win_rate'] }}%"></div>
                    </div>
                    <div class="instrument-stats">{{ $instrument['wins'] }}W / {{ $instrument['losses'] }}L</div>
                </div>
                @endforeach

                @if(count($chartData['top_instruments']) == 0)
                <div class="text-center text-muted py-4">No trade data available</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Session Win Rates -->
    <div class="col-md-6 mb-4">
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">
                    <i class="fa fa-clock"></i>
                    <span>Top 3 Performing Sessions</span>
                </div>
            </div>

            <div class="p-2" id="top-sessions-container">
                @foreach($chartData['session_win_rates'] as $session)
                <div class="session-row">
                    <div class="session-name">{{ $session['session'] }}</div>
                    <div class="session-bar-wrapper">
                        <div class="session-bar-bg">
                            <div class="session-bar-fill" style="width: {{ $session['win_rate'] }}%"></div>
                            <div class="session-dot" style="left: {{ $session['win_rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="session-stat">{{ $session['win_rate'] }}%</div>
                </div>
                @endforeach

                @if(count($chartData['session_win_rates']) == 0)
                <div class="text-center text-muted py-4">No trade data available</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Behavioral & Best/Worst -->
<div class="row">
    <!-- Behavioral Analytics -->
    <div class="col-md-12 mb-4">
        <div class="section-header">
            <div class="section-title">
                <i class="fa fa-brain"></i>
                <span>Behavioral Analytics</span>
            </div>
        </div>

        <div class="behavioral-grid">
            <div class="behavioral-card">
                <div class="behavioral-label">Revenge Trades</div>
                <div class="behavioral-value" style="color: var(--ag-danger);" id="beh-revenge">{{ $behavioral['revenge_trades'] }}</div>
            </div>
            <div class="behavioral-card">
                <div class="behavioral-label">Impulse Trades</div>
                <div class="behavioral-value" style="color: var(--ag-warning);" id="beh-impulse">{{ $behavioral['impulse_percentage'] }}%</div>
            </div>
            <div class="behavioral-card">
                <div class="behavioral-label">Missed Trades</div>
                <div class="behavioral-value" style="color: var(--ag-info);" id="beh-missed">{{ $behavioral['missed_trades'] }}</div>
            </div>
            <div class="behavioral-card">
                <div class="behavioral-label">Patience Score</div>
                <div class="behavioral-value" style="color: var(--ag-success);" id="beh-patience">{{ $behavioral['patience_score'] }}</div>
            </div>
        </div>
    </div>

    <!-- Best & Worst Trades -->
    <div class="col-md-12">
        <div class="section-header">
            <div class="section-title">
                <i class="fa fa-medal"></i>
                <span>Trade Showcase</span>
            </div>
        </div>

        <div class="trade-showcase">
            <!-- Best Trade -->
            @if($bestWorst['best'])
            <div class="trade-card" id="best-trade-card">
                <div class="trade-card-header best">
                    <div style="font-weight: 700;">BEST TRADE</div>
                    <div style="font-size: 18px;" id="best-trade-pnl">+${{ number_format($bestWorst['best']->pnl, 2) }}</div>
                </div>
                <div class="trade-card-body">
                    <div class="trade-pair">
                        <div class="pair-icon"><i class="fa fa-chart-line"></i></div>
                        <span id="best-trade-pair">{{ strtoupper($bestWorst['best']->asset )}}</span>
                        <span class="badge badge-success ml-auto">WIN</span>
                    </div>
                    <div style="color: var(--ag-neutral-700); font-size: 13px; margin-bottom: 12px;">
                        <i class="fa fa-calendar-alt mr-1"></i> <span id="best-trade-date">{{ \Carbon\Carbon::parse($bestWorst['best']->trade_date)->format('d M Y') }}</span>
                    </div>
                    <div class="trade-stats-grid">
                        <div class="trade-stat">
                            <div class="trade-stat-label">Risk/Reward</div>
                            <div class="trade-stat-value" id="best-trade-rr">{{ $bestWorst['best']->rr }}</div>
                        </div>
                        <div class="trade-stat">
                            <div class="trade-stat-label">Session</div>
                            <div class="trade-stat-value" style="font-size: 14px;" id="best-trade-session">{{ strtoupper(str_replace('_', ' ', $bestWorst['best']->session)) }}</div>
                        </div>
                        <div class="trade-stat" style="grid-column: span 2;">
                            <div class="trade-stat-label">Setup</div>
                            <div class="trade-stat-value" style="font-size: 14px;" id="best-trade-setup">{{ $bestWorst['best']->setup ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Worst Trade -->
            @if($bestWorst['worst'])
            <div class="trade-card" id="worst-trade-card">
                <div class="trade-card-header worst">
                    <div style="font-weight: 700;">WORST TRADE</div>
                    <div style="font-size: 18px;" id="worst-trade-pnl">-${{ number_format(abs($bestWorst['worst']->pnl), 2) }}</div>
                </div>
                <div class="trade-card-body">
                    <div class="trade-pair">
                        <div class="pair-icon"><i class="fa fa-chart-line"></i></div>
                        <span id="worst-trade-pair">{{ strtoupper($bestWorst['worst']->asset) }}</span>
                        <span class="badge badge-danger ml-auto">LOSS</span>
                    </div>
                    <div style="color: var(--ag-neutral-700); font-size: 13px; margin-bottom: 12px;">
                        <i class="fa fa-calendar-alt mr-1"></i> <span id="worst-trade-date">{{ \Carbon\Carbon::parse($bestWorst['worst']->trade_date)->format('d M Y') }}</span>
                    </div>
                    <div class="trade-stats-grid">
                        <div class="trade-stat">
                            <div class="trade-stat-label">Risk/Reward</div>
                            <div class="trade-stat-value" id="worst-trade-rr">{{ $bestWorst['worst']->rr }}</div>
                        </div>
                        <div class="trade-stat">
                            <div class="trade-stat-label">Session</div>
                            <div class="trade-stat-value" style="font-size: 14px;" id="worst-trade-session">{{ strtoupper(str_replace('_', ' ', $bestWorst['worst']->session)) }}</div>
                        </div>
                        <div class="trade-stat" style="grid-column: span 2;">
                            <div class="trade-stat-label">Setup</div>
                            <div class="trade-stat-value" style="font-size: 14px;" id="worst-trade-setup">{{ $bestWorst['worst']->setup ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('deskapp/src/plugins/air-datepicker/dist/js/datepicker.min.js') }}"></script>
<script src="{{ asset('deskapp/src/plugins/air-datepicker/dist/js/i18n/datepicker.en.js') }}"></script>

<script>
    $(document).ready(function() {
        // Toggle Filter Sidebar
        $('.filter-btn-toggle, .toggle-sidebar-btn').on('click', function() {
            $('.right-sidebar').toggleClass('right-sidebar-visible');
        });

        $('.close-sidebar').on('click', function() {
            $('.right-sidebar').removeClass('right-sidebar-visible');
        });

        // Initialize Datepicker
        $('#date-picker').datepicker({
            language: 'en',
            range: true,
            multipleDatesSeparator: ' - ',
            autoClose: true,
            onSelect: function(formattedDate, date, inst) {
                if (date.length === 2) {
                    $('#start_date').val(moment(date[0]).format('DD/MM/YYYY'));
                    $('#end_date').val(moment(date[1]).format('DD/MM/YYYY'));
                    $('#filter_form').submit(); // Auto-submit on date selection
                }
            }
        });

        // Set default to current month
        let startOfMonth = moment().startOf('month');
        let endOfMonth = moment().endOf('month');
        $('#date-picker').val(startOfMonth.format('MM/DD/YYYY') + ' - ' + endOfMonth.format('MM/DD/YYYY'));
        $('#start_date').val(startOfMonth.format('DD/MM/YYYY'));
        $('#end_date').val(endOfMonth.format('DD/MM/YYYY'));
        
        // Update filter summary to show current month
        $('#filter-date-text').text(startOfMonth.format('MMM YYYY'));

        // Date Range Buttons
        $('.date-range-btn').on('click', function() {
            let range = $(this).data('range');
            let start, end;

            if (range === 'today') {
                start = moment();
                end = moment();
            } else if (range === 'yesterday') {
                start = moment().subtract(1, 'days');
                end = moment().subtract(1, 'days');
            } else if (range === 'this_week') {
                start = moment().startOf('isoWeek');
                end = moment().endOf('isoWeek');
            } else if (range === 'last_week') {
                start = moment().subtract(1, 'weeks').startOf('isoWeek');
                end = moment().subtract(1, 'weeks').endOf('isoWeek');
            } else if (range === 'this_month') {
                start = moment().startOf('month');
                end = moment().endOf('month');
            } else if (range === 'last_month') {
                start = moment().subtract(1, 'months').startOf('month');
                end = moment().subtract(1, 'months').endOf('month');
            } else if (range === 'this_year') {
                start = moment().startOf('year');
                end = moment().endOf('year');
            } else if (range === 'last_year') {
                start = moment().subtract(1, 'years').startOf('year');
                end = moment().subtract(1, 'years').endOf('year');
            }

            $('#date-picker').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
            $('#start_date').val(start.format('DD/MM/YYYY'));
            $('#end_date').val(end.format('DD/MM/YYYY'));
            $('#filter_form').submit(); // Auto-submit on range click
        });

        // Auto-submit on change
        $('#filter_form input, #filter_form select').on('change', function() {
            $('#filter_form').submit();
        });

        // Handle Filter Submission
        $('#filter_form').on('submit', function(e) {
            e.preventDefault();

            // Show loading indicator if desired

            $.ajax({
                url: "{{ route('analytics.index') }}",
                type: "GET",
                data: $(this).serialize(),
                success: function(response) {
                    updateDashboard(response);
                    // Don't close sidebar on auto-submit, user might want to change more
                    // $('.right-sidebar').removeClass('right-sidebar-visible');
                    updateFilterSummary();
                },
                error: function(xhr) {
                    console.error("Error filtering dashboard", xhr);
                }
            });
        });

        function updateDashboard(data) {
            // Update KPIs
            $('#kpi-win-rate').text(data.kpis.win_rate + '%');

            let pnlColor = data.kpis.monthly_roi >= 0 ? 'var(--ag-success)' : 'var(--ag-danger)';
            $('#kpi-pnl').text('$' + parseFloat(data.kpis.monthly_roi).toFixed(2)).css('color', pnlColor);

            $('#kpi-period-wr').text(data.kpis.monthly_win_rate + '%');
            $('#kpi-wins').text(data.kpis.monthly_wins + 'W');
            $('#kpi-losses').text(data.kpis.monthly_losses + 'L');
            $('#kpi-trades').text(data.kpis.monthly_trades);
            
            let rrColor = data.kpis.monthly_total_rr >= 0 ? 'var(--ag-success)' : 'var(--ag-danger)';
            $('#kpi-rr').text(data.kpis.monthly_total_rr).css('color', rrColor);

            $('#kpi-best-day').text('$' + parseFloat(data.kpis.best_day).toFixed(2));
            $('#kpi-worst-day').text('$' + parseFloat(data.kpis.worst_day).toFixed(2));

            $('#kpi-compliance').text(data.kpis.compliance_score + '%');
            $('#kpi-compliance-bar').css('width', data.kpis.compliance_score + '%');

            // Update Streaks
            $('#streak-win').text(data.streaks.current_win_streak);
            $('#streak-win-max').text(data.streaks.max_win_streak);
            $('#streak-loss').text(data.streaks.current_loss_streak);
            $('#streak-loss-max').text(data.streaks.max_loss_streak);

            // Update Behavioral
            $('#beh-revenge').text(data.behavioral.revenge_trades);
            $('#beh-impulse').text(data.behavioral.impulse_percentage + '%');
            $('#beh-missed').text(data.behavioral.missed_trades);
            $('#beh-patience').text(data.behavioral.patience_score);

            // Update Widgets
            updateTopInstruments(data.chartData.top_instruments);
            updateTopSessions(data.chartData.session_win_rates);

            // Update Charts
            updateCharts(data.chartData);

            // Update Best Trade
            if (data.bestWorst.best) {
                $('#best-trade-card').show();
                $('#best-trade-pnl').text('+$' + parseFloat(data.bestWorst.best.pnl).toFixed(2));
                $('#best-trade-pair').text(data.bestWorst.best.asset.toUpperCase());
                $('#best-trade-date').text(moment(data.bestWorst.best.trade_date).format('DD MMM YYYY'));
                $('#best-trade-rr').text(data.bestWorst.best.rr);
                
                let session = data.bestWorst.best.session || data.bestWorst.best.killzone || '';
                session = session.replace(/_/g, ' ');
                $('#best-trade-session').text(session ? session.charAt(0).toUpperCase() + session.slice(1) : 'N/A');

                $('#best-trade-setup').text(data.bestWorst.best.setup || 'N/A');
            } else {
                $('#best-trade-card').hide();
            }

            // Update Worst Trade
            if (data.bestWorst.worst) {
                $('#worst-trade-card').show();
                $('#worst-trade-pnl').text('-$' + Math.abs(parseFloat(data.bestWorst.worst.pnl)).toFixed(2));
                $('#worst-trade-pair').text(data.bestWorst.worst.asset.toUpperCase());
                $('#worst-trade-date').text(moment(data.bestWorst.worst.trade_date).format('DD MMM YYYY'));
                $('#worst-trade-rr').text(data.bestWorst.worst.rr);
                
                let session = data.bestWorst.worst.session || data.bestWorst.worst.killzone || '';
                session = session.replace(/_/g, ' ');
                $('#worst-trade-session').text(session ? session.charAt(0).toUpperCase() + session.slice(1) : 'N/A');

                $('#worst-trade-setup').text(data.bestWorst.worst.setup || 'N/A');
            } else {
                $('#worst-trade-card').hide();
            }
        }

        function updateTopInstruments(instruments) {
            let html = '';
            if (instruments.length === 0) {
                html = '<div class="text-center text-muted py-4">No trade data available</div>';
            } else {
                instruments.forEach(inst => {
                    html += `
                    <div class="instrument-row">
                        <div class="instrument-name">${inst.pair}</div>
                        <div class="instrument-bar">
                            <div class="bar-win" style="width: ${inst.win_rate}%"></div>
                            <div class="bar-loss" style="width: ${100 - inst.win_rate}%"></div>
                        </div>
                        <div class="instrument-stats">${inst.wins}W / ${inst.losses}L</div>
                    </div>`;
                });
            }
            $('#top-instruments-container').html(html);
        }

        function updateTopSessions(sessions) {
            let html = '';
            if (sessions.length === 0) {
                html = '<div class="text-center text-muted py-4">No trade data available</div>';
            } else {
                sessions.forEach(sess => {
                    let sessionName = sess.session;
                    html += `
                    <div class="session-row">
                        <div class="session-name">${sessionName}</div>
                        <div class="session-bar-wrapper">
                            <div class="session-bar-bg">
                                <div class="session-bar-fill" style="width: ${sess.win_rate}%"></div>
                                <div class="session-dot" style="left: ${sess.win_rate}%"></div>
                            </div>
                        </div>
                        <div class="session-stat">${sess.win_rate}%</div>
                    </div>`;
                });
            }
            $('#top-sessions-container').html(html);
        }

        function updateFilterSummary() {
            // Simple logic to update chips based on form values
            let dateVal = $('#date-picker').val();
            if (dateVal) {
                $('#filter-date-text').text(dateVal);
            } else {
                $('#filter-date-text').text('All Time');
            }

            let market = $('#market option:selected').text();
            if ($('#market').val() !== '0') {
                $('#filter-market-text').text(market);
                $('#filter-market-chip').show();
            } else {
                $('#filter-market-chip').hide();
            }

            let session = $('#session option:selected').text();
            if ($('#session').val() !== '0') {
                $('#filter-session-text').text(session);
                $('#filter-session-chip').show();
            } else {
                $('#filter-session-chip').hide();
            }
        }

        // Chart Defaults
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6c757d';

        // Global Chart Instances
        let equityChart, dailyChart, monthlyChart;

        // Equity Curve Chart
        const equityCtx = document.getElementById('equityCurveChart').getContext('2d');
        const equityGradient = equityCtx.createLinearGradient(0, 0, 0, 400);
        equityGradient.addColorStop(0, 'rgba(102, 126, 234, 0.2)');
        equityGradient.addColorStop(1, 'rgba(102, 126, 234, 0)');

        const equityData = @json($chartData['equity_curve']);

        equityChart = new Chart(equityCtx, {
            type: 'line',
            data: {
                labels: equityData.map(d => d.date),
                datasets: [{
                    label: 'Equity',
                    data: equityData.map(d => d.equity),
                    borderColor: '#667eea',
                    backgroundColor: equityGradient,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#212529',
                        bodyColor: '#212529',
                        borderColor: '#e9ecef',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        grid: { borderDash: [4, 4], color: '#e9ecef' },
                        ticks: { callback: function(value) { return '$' + value; } }
                    }
                }
            }
        });

        // Daily Performance Chart
        const dailyCtx = document.getElementById('dailyPerformanceChart').getContext('2d');
        const dailyData = @json($chartData['daily_performance']);

        function getDailyChartData(data) {
            const days = data.map(d => d.day);
            const profits = data.map(d => d.profit);
            const losses = data.map(d => d.loss);
            const customLabels = data.map(d => {
                const sign = d.net >= 0 ? '+' : '-';
                const val = Math.abs(d.net).toFixed(0);
                return [d.day, `${sign}$${val}`];
            });
            return { days, profits, losses, customLabels };
        }

        let dData = getDailyChartData(dailyData);

        dailyChart = new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: dData.customLabels,
                datasets: [
                    {
                        label: 'Profit',
                        data: dData.profits,
                        backgroundColor: '#00e676',
                        borderRadius: 6,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    },
                    {
                        label: 'Loss',
                        data: dData.losses,
                        backgroundColor: '#ff5252',
                        borderRadius: 6,
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8 }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#212529',
                        bodyColor: '#212529',
                        borderColor: '#e9ecef',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '$' + context.parsed.y.toFixed(2);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    },
                    y: {
                        grid: { borderDash: [4, 4], color: '#e9ecef' },
                        ticks: { callback: function(value) { return '$' + value; } }
                    }
                }
            }
        });

        // Monthly Performance Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = @json($chartData['monthly_performance']);

        function getMonthlyChartData(data) {
            // data is array of objects { label, value, date }
            const labels = data.map(d => d.label);
            const values = data.map(d => d.value);
            const colors = values.map(v => v >= 0 ? '#00E396' : '#FF4560');
            return { labels, values, colors };
        }

        let mData = getMonthlyChartData(monthlyData);

        monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: mData.labels,
                datasets: [{
                    label: 'Net P&L',
                    data: mData.values,
                    backgroundColor: mData.colors,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        grid: { borderDash: [4, 4], color: '#e9ecef' },
                        ticks: { callback: function(value) { return '$' + value; } }
                    }
                }
            }
        });

        function updateCharts(data) {
            // Update Equity
            equityChart.data.labels = data.equity_curve.map(d => d.date);
            equityChart.data.datasets[0].data = data.equity_curve.map(d => d.equity);
            equityChart.update();

            // Update Daily
            let dData = getDailyChartData(data.daily_performance);
            dailyChart.data.labels = dData.customLabels;
            dailyChart.data.datasets[0].data = dData.profits;
            dailyChart.data.datasets[1].data = dData.losses;
            dailyChart.update();

            // Update Monthly
            let mData = getMonthlyChartData(data.monthly_performance);
            monthlyChart.data.labels = mData.labels;
            monthlyChart.data.datasets[0].data = mData.values;
            monthlyChart.data.datasets[0].backgroundColor = mData.colors;
            monthlyChart.update();
        }
        // Chart Tab Resize Fix
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            if (e.target.hash === '#tab-equity' && typeof equityChart !== 'undefined') {
                equityChart.resize();
            }
            if (e.target.hash === '#tab-daily' && typeof dailyChart !== 'undefined') {
                dailyChart.resize();
            }
            if (e.target.hash === '#tab-monthly' && typeof monthlyChart !== 'undefined') {
                monthlyChart.resize();
            }
        });
    });
</script>
@endpush
