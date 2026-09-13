@extends('layouts.app')

@section('title', 'Consistency Compliance Calculator')

@push('styles')
<style>
    /* ── Apple-Inspired Design System ── */
    :root {
        --apple-font: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Helvetica Neue", Arial, sans-serif;
        --apple-bg: #f5f5f7;
        --apple-card-bg: #ffffff;
        --apple-dark-bg: #141416;
        --apple-text-primary: #1d1d1f;
        --apple-text-secondary: #86868b;
        --apple-text-tertiary: #a1a1a6;
        --apple-blue: #0071e3;
        --apple-blue-hover: #0077ed;
        --apple-blue-soft: rgba(0, 113, 227, 0.08);
        --apple-green: #34c759;
        --apple-green-soft: rgba(52, 199, 89, 0.12);
        --apple-orange: #ff9500;
        --apple-orange-soft: rgba(255, 149, 0, 0.12);
        --apple-red: #ff3b30;
        --apple-red-soft: rgba(255, 59, 48, 0.1);
        --apple-purple: #af52de;
        --apple-purple-soft: rgba(175, 82, 222, 0.1);
        --apple-border: rgba(0, 0, 0, 0.06);
        --apple-border-strong: #d2d2d7;
        --apple-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
        --apple-shadow-md: 0 8px 24px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
        --apple-shadow-lg: 0 16px 40px rgba(0, 0, 0, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: var(--apple-font) !important;
        background-color: var(--apple-bg) !important;
        color: var(--apple-text-primary) !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .apple-wrap {
        font-family: var(--apple-font);
        max-width: 1340px;
        margin: 0 auto;
        padding-bottom: 50px;
    }

    /* ── Floating Left Sidebar Navigation ── */
    .apple-floating-nav {
        position: sticky;
        top: 90px;
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid var(--apple-border);
        box-shadow: var(--apple-shadow-md);
        padding: 16px 12px;
        z-index: 100;
    }

    .apple-nav-header {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--apple-text-secondary);
        padding: 6px 12px 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .apple-nav-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--apple-text-secondary);
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        margin-bottom: 3px;
        position: relative;
    }

    .apple-nav-link:hover {
        background: #f5f5f7;
        color: var(--apple-text-primary);
    }

    .apple-nav-link.active {
        background: var(--apple-blue-soft);
        color: var(--apple-blue);
        font-weight: 600;
    }

    .apple-nav-link.active::before {
        content: '';
        position: absolute;
        left: 4px;
        width: 4px;
        height: 18px;
        background: var(--apple-blue);
        border-radius: 999px;
    }

    .apple-nav-icon {
        font-size: 15px;
        width: 20px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* ── Apple Glass Card ── */
    .apple-card {
        background: var(--apple-card-bg);
        border-radius: 20px;
        border: 1px solid var(--apple-border);
        box-shadow: var(--apple-shadow-md);
        padding: 28px;
        margin-bottom: 24px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    /* ── Apple Segmented Control ── */
    .apple-segmented-control {
        background: #e5e5ea;
        padding: 3px;
        border-radius: 999px;
        display: inline-flex;
        gap: 2px;
        border: 1px solid rgba(0, 0, 0, 0.04);
    }
    .apple-segment-btn {
        background: transparent;
        border: none;
        outline: none;
        padding: 6px 18px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        color: var(--apple-text-secondary);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .apple-segment-btn:hover {
        color: var(--apple-text-primary);
    }
    .apple-segment-btn.active {
        background: #ffffff;
        color: var(--apple-text-primary);
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* ── Form Controls & Inputs ── */
    .apple-label {
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--apple-text-secondary);
        margin-bottom: 8px;
        display: block;
    }
    .apple-input, .apple-select {
        height: 44px;
        background: #fbfbfd;
        border: 1px solid var(--apple-border-strong);
        border-radius: 12px;
        padding: 0 14px;
        font-size: 14px;
        font-weight: 500;
        color: var(--apple-text-primary);
        width: 100%;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
    }
    .apple-input:focus, .apple-select:focus {
        background: #ffffff;
        border-color: var(--apple-blue);
        outline: none;
        box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.15);
    }

    .apple-pill-btn {
        background: #f5f5f7;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 11.5px;
        font-weight: 500;
        color: var(--apple-text-secondary);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .apple-pill-btn:hover {
        background: #e8e8ed;
        color: var(--apple-text-primary);
    }

    /* ── Math Proof Box Inside Input Div ── */
    .apple-math-container {
        background: #fafafc;
        border: 1px solid var(--apple-border);
        border-radius: 16px;
        padding: 22px;
        margin-top: 24px;
    }
    .apple-math-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 12px;
        padding: 16px;
        height: 100%;
        box-shadow: var(--apple-shadow-sm);
    }
    .apple-math-equation {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: var(--apple-text-primary);
        margin-top: 8px;
        flex-wrap: wrap;
    }
    .apple-math-fraction {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        vertical-align: middle;
        text-align: center;
        font-size: 12.5px;
        padding: 0 4px;
    }
    .apple-math-fraction .numerator {
        border-bottom: 1.5px solid #1d1d1f;
        padding-bottom: 2px;
        width: 100%;
        font-weight: 600;
    }
    .apple-math-fraction .denominator {
        padding-top: 2px;
        font-weight: 600;
    }

    /* ── Hero Status Widget ── */
    .apple-hero {
        border-radius: 24px;
        padding: 32px;
        box-shadow: var(--apple-shadow-lg);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(20px);
    }

    .apple-hero-compliant {
        background: linear-gradient(135deg, #182e20 0%, #0f1c14 100%);
        color: #ffffff;
    }
    .apple-hero-accumulating {
        background: linear-gradient(135deg, #182136 0%, #0d1320 100%);
        color: #ffffff;
    }

    .apple-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }
    .apple-pill-green {
        background: rgba(52, 199, 89, 0.18);
        color: #4cd964;
        border: 1px solid rgba(52, 199, 89, 0.3);
    }
    .apple-pill-amber {
        background: rgba(255, 149, 0, 0.18);
        color: #ffb340;
        border: 1px solid rgba(255, 149, 0, 0.3);
    }

    .apple-hero-stat-box {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 18px;
        padding: 20px;
        text-align: center;
        backdrop-filter: blur(12px);
    }

    /* ── Progress Track ── */
    .apple-progress-track {
        height: 8px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 999px;
        overflow: hidden;
        position: relative;
    }
    .apple-progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* ── Metric Tiles ── */
    .apple-metric-tile {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid var(--apple-border);
        box-shadow: var(--apple-shadow-sm);
        padding: 22px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .apple-metric-tile:hover {
        transform: translateY(-2px);
        box-shadow: var(--apple-shadow-md);
    }

    .apple-tile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .apple-tile-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--apple-text-secondary);
        letter-spacing: -0.01em;
    }
    .apple-tile-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .apple-tile-value {
        font-size: 27px;
        font-weight: 700;
        letter-spacing: -0.03em;
        line-height: 1.1;
        margin-bottom: 6px;
        color: var(--apple-text-primary);
    }
    .apple-tile-subtitle {
        font-size: 12px;
        color: var(--apple-text-tertiary);
        line-height: 1.4;
    }

    /* ── Apple Tables ── */
    .apple-table-wrap {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--apple-border);
    }
    .apple-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .apple-table thead th {
        background: #fafafc;
        color: var(--apple-text-secondary);
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 14px 18px;
        border-bottom: 1px solid var(--apple-border);
        border-top: none;
    }
    .apple-table tbody td {
        padding: 15px 18px;
        font-size: 13.5px;
        color: var(--apple-text-primary);
        border-bottom: 1px solid var(--apple-border);
        vertical-align: middle;
        background: #ffffff;
        transition: background 0.15s ease;
    }
    .apple-table tbody tr:last-child td {
        border-bottom: none;
    }
    .apple-table tbody tr:hover td {
        background: #fbfbfd;
    }

    /* ── Keynote Box ── */
    .apple-keynote-card {
        background: var(--apple-dark-bg);
        border-radius: 20px;
        padding: 30px 32px;
        color: #ffffff;
        box-shadow: var(--apple-shadow-lg);
        position: relative;
    }
</style>
@endpush

@section('content')
<div class="apple-wrap">
    
    <!-- ── Page Top Header ── -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 16px;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--apple-blue);"></span>
                <span class="font-12 uppercase weight-700 text-muted" style="letter-spacing: 0.06em;">Risk & Prop Compliance Suite</span>
            </div>
            <h2 class="weight-700" style="font-size: 28px; letter-spacing: -0.03em; margin: 0; color: #1d1d1f;">Consistency Compliance</h2>
        </div>

        <!-- Mode Toggle Segmented Control (Defaults to Custom Sandbox) -->
        <div class="apple-segmented-control">
            <button type="button" class="apple-segment-btn" id="btn_mode_account">
                <i class="dw dw-layers"></i> Account Sync
            </button>
            <button type="button" class="apple-segment-btn active" id="btn_mode_sandbox">
                <i class="dw dw-edit-2"></i> Custom Sandbox
            </button>
        </div>
    </div>

    <!-- ── Two-Column Layout with Floating Left Navigation ── -->
    <div class="row">
        
        <!-- Left Column: Floating Sticky Navigation -->
        <div class="col-xl-3 col-lg-3 col-md-4 d-none d-md-block">
            <div class="apple-floating-nav">
                <div class="apple-nav-header">
                    <i class="dw dw-menu"></i> Quick Navigation
                </div>
                <a href="#section-config" class="apple-nav-link active">
                    <span class="apple-nav-icon"><i class="dw dw-settings"></i></span> Parameters & Math
                </a>
                <a href="#section-overview" class="apple-nav-link">
                    <span class="apple-nav-icon"><i class="dw dw-analytics-8"></i></span> Compliance Status
                </a>
                <a href="#section-metrics" class="apple-nav-link">
                    <span class="apple-nav-icon"><i class="dw dw-table"></i></span> Breakdown Metrics
                </a>
                <a href="#section-scenarios" class="apple-nav-link">
                    <span class="apple-nav-icon"><i class="dw dw-chart"></i></span> Daily Scenarios
                </a>
                <a href="#section-simulator" class="apple-nav-link">
                    <span class="apple-nav-icon"><i class="dw dw-calendar-1"></i></span> Day-by-Day Planner
                </a>
                <a href="#section-mindset" class="apple-nav-link">
                    <span class="apple-nav-icon"><i class="dw dw-idea"></i></span> Execution Mindset
                </a>
            </div>
        </div>

        <!-- Right Column: Main Content Sections -->
        <div class="col-xl-9 col-lg-9 col-md-8 col-12">

            <!-- ── SECTION 1: Parameters, Inputs & Math Proof (Same Div) ── -->
            <div class="apple-card" id="section-config">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="font-14 weight-700 text-dark">
                        <i class="dw dw-settings mr-1 text-primary"></i> Evaluation & Consistency Parameters
                    </div>
                    <span class="badge badge-light font-12" id="active_mode_badge" style="border-radius: 20px; padding: 4px 10px;">
                        Sandbox Simulation
                    </span>
                </div>

                <div class="row align-items-end">
                    <!-- Account Selector (Hidden in Sandbox mode or visible in Account sync mode) -->
                    <div class="col-lg-4 col-md-6 mb-3" id="account_selector_col" style="display: none;">
                        <label class="apple-label">Trading Account</label>
                        <select class="apple-select" id="account_select">
                            <option value="sandbox" selected>🛠️ Custom Sandbox / Manual Input</option>
                            @if($accounts->isNotEmpty())
                                @php
                                    $ruleAccounts = $accounts->where('has_consistency_rule', true);
                                    $otherAccounts = $accounts->where('has_consistency_rule', false);
                                @endphp
                                @if($ruleAccounts->isNotEmpty())
                                    <optgroup label="⭐ Consistency Rule Accounts">
                                        @foreach($ruleAccounts as $acc)
                                            <option value="{{ $acc->id }}">
                                                {{ $acc->name }} ({{ $acc->broker }}) — {{ $acc->consistency_rule_percent }}% {{ ucfirst($acc->consistency_rule_type ?? 'day') }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if($otherAccounts->isNotEmpty())
                                    <optgroup label="Other Accounts">
                                        @foreach($otherAccounts as $acc)
                                            <option value="{{ $acc->id }}">
                                                {{ $acc->name }} ({{ $acc->broker }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endif
                        </select>
                    </div>

                    <!-- Consistency Rule % -->
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="apple-label mb-0">Consistency Limit</label>
                            <span class="font-12 weight-600 text-primary" id="rule_percent_display">50%</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" step="1" min="1" max="100" class="apple-input text-center weight-600" id="input_rule_percent" value="50" style="width: 80px;">
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="apple-pill-btn rule-preset-btn" data-val="50">50% High Stakes</button>
                                <button type="button" class="apple-pill-btn rule-preset-btn" data-val="40">40% Bootcamp</button>
                                <button type="button" class="apple-pill-btn rule-preset-btn" data-val="33">33%</button>
                            </div>
                        </div>
                    </div>

                    <!-- Rule Type (Day vs Trade) -->
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label class="apple-label">Calculated On</label>
                        <select class="apple-select" id="select_rule_type">
                            <option value="day" selected>Best Trading Day</option>
                            <option value="trade">Best Single Trade</option>
                        </select>
                    </div>

                    <!-- Profit Target ($) -->
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label class="apple-label">Phase Profit Goal ($)</label>
                        <input type="number" step="100" min="0" class="apple-input" id="input_profit_target" value="10000" placeholder="e.g. 10000">
                    </div>
                </div>

                <!-- Sandbox Manual Inputs (Displayed by default) -->
                <div id="sandbox_inputs_row" class="row mt-2 pt-3" style="border-top: 1px solid var(--apple-border);">
                    <div class="col-md-6 mb-2">
                        <label class="apple-label">Current Total Net Profit ($)</label>
                        <input type="number" step="0.01" class="apple-input" id="manual_total_profit" value="2082.52" placeholder="e.g. 2082.52">
                        <div class="font-12 text-muted mt-1">Total closed net PnL across all trades.</div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="apple-label" id="manual_outlier_label">Highest Single Day Profit ($)</label>
                        <input type="number" step="0.01" class="apple-input" id="manual_outlier_profit" value="2538.38" placeholder="e.g. 2538.38">
                        <div class="font-12 text-muted mt-1">Your largest winning day or trade so far.</div>
                    </div>
                </div>

                <!-- Non-consistency account alert (Only shown on Account Mode when rule is disabled) -->
                <div id="no_rule_account_alert" class="p-3 mt-3 d-flex justify-content-between align-items-center flex-wrap" style="display: none; background: #fff9e6; border: 1px solid #ffe58f; border-radius: 14px;">
                    <div class="d-flex align-items-center gap-3">
                        <span style="font-size: 18px;">💡</span>
                        <div>
                            <div class="font-13 weight-600 text-dark">Consistency rule is currently disabled for this account</div>
                            <div class="font-12 text-muted">Calculating using standard default limit. You can enable consistency rules in Accounts.</div>
                        </div>
                    </div>
                    <a href="{{ route('accounts.index') }}" class="apple-pill-btn mt-2 mt-md-0" style="background: #ffffff; border-color: #ffd666; font-weight: 600; text-decoration: none;">
                        Configure Account Rules
                    </a>
                </div>

                <!-- ── Step-by-Step Mathematical Proof (Inside Same Div, only shows when numbers are present) ── -->
                <div id="math_proof_container" class="apple-math-container">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span style="font-size: 16px;">📐</span>
                        <div class="weight-700 font-14 text-dark">Step-by-Step Mathematical Proof & Breakdown</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="apple-math-card">
                                <div class="weight-600 font-12 text-secondary mb-1">1. Current Consistency Ratio</div>
                                <div class="apple-math-equation">
                                    <div class="apple-math-fraction">
                                        <span class="numerator">Best Day ($<span id="m_best_day">2,538.38</span>)</span>
                                        <span class="denominator">Total Profit ($<span id="m_total_pnl">2,082.52</span>)</span>
                                    </div>
                                    <span>× 100 = </span>
                                    <span class="weight-700 font-16 text-danger" id="m_result_pct">121.89%</span>
                                </div>
                                <div class="font-12 text-muted mt-2">
                                    Your single best day represents <strong id="m_result_pct_2">121.89%</strong> of your total closed profits.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="apple-math-card">
                                <div class="weight-600 font-12 text-secondary mb-1">2. Target Compliance Formula</div>
                                <div class="apple-math-equation">
                                    <div class="apple-math-fraction">
                                        <span class="numerator">Best Day ($<span id="m_best_day_3">2,538.38</span>)</span>
                                        <span class="denominator"><span class="rule-pct-text">50%</span> (<span id="m_rule_dec">0.50</span>)</span>
                                    </div>
                                    <span>= </span>
                                    <span class="weight-700 font-16 text-success" id="m_req_total">$5,076.76</span>
                                </div>
                                <div class="font-12 text-muted mt-2">
                                    Additional profit needed: $<span id="m_req_total_2">5,076.76</span> − $<span id="m_total_pnl_2">2,082.52</span> = <strong class="text-danger" id="m_diff">$2,994.24</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── SECTION 2: Hero Status Widget ── -->
            <div id="section-overview" class="apple-hero apple-hero-accumulating mb-4">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-12 mb-3 mb-lg-0">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span id="compliance_badge" class="apple-status-pill apple-pill-amber">
                                <i class="dw dw-alert"></i> ACCUMULATING (121.89% > 50%)
                            </span>
                            <span class="font-12 opacity-60 text-white" id="account_display_name">Mode: Custom Sandbox Simulation</span>
                        </div>
                        <h3 class="text-white weight-700 mb-2" id="hero_status_heading" style="letter-spacing: -0.02em;">
                            You Haven't Failed — Keep Building Normal Profits
                        </h3>
                        <p class="font-14 mb-0 opacity-80" id="hero_status_message" style="line-height: 1.6; max-width: 640px;">
                            Your biggest day represents <strong id="hero_pct_text">121.89%</strong> of your profits. Accumulate normal winning trades to bring consistency under the <strong id="hero_limit_text">50%</strong> threshold.
                        </p>
                    </div>
                    
                    <div class="col-lg-4 col-md-12">
                        <div class="apple-hero-stat-box">
                            <div class="font-11 uppercase weight-700 opacity-60" style="letter-spacing: 0.05em;">Current Consistency Ratio</div>
                            <div class="font-36 weight-800 my-1" id="hero_consistency_pct" style="letter-spacing: -0.04em; color: #ffffff;">121.89%</div>
                            <div class="font-12 opacity-80" id="hero_subtext">Requirement: ≤ 50.0%</div>
                        </div>
                    </div>
                </div>

                <!-- Dual Progress Bars -->
                <div class="row mt-4 pt-3" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="d-flex justify-content-between font-12 opacity-80 text-white mb-2">
                            <span>Consistency Ratio Gauge (Lower is Better)</span>
                            <strong id="bar_consistency_label">121.9% / 50% max</strong>
                        </div>
                        <div class="apple-progress-track">
                            <div id="bar_consistency" class="apple-progress-fill" style="width: 80%; background: #ff9500;"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between font-12 opacity-80 text-white mb-2">
                            <span>Compliance Profit Progress (<span id="bar_profit_curr">$2,083</span> / <span id="bar_profit_target">$5,077</span>)</span>
                            <strong id="bar_profit_label">41.0%</strong>
                        </div>
                        <div class="apple-progress-track">
                            <div id="bar_profit" class="apple-progress-fill" style="width: 41%; background: #34c759;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SECTION 3: 6 Core Calculation Tiles ── -->
            <div id="section-metrics" class="row mb-4">
                <!-- Best Day / Trade -->
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="apple-metric-tile">
                        <div class="apple-tile-header">
                            <span class="apple-tile-title" id="card_outlier_title">Best Winning Day</span>
                            <div class="apple-tile-icon" style="background: var(--apple-blue-soft); color: var(--apple-blue);">
                                <i class="dw dw-trophy"></i>
                            </div>
                        </div>
                        <div class="apple-tile-value" id="val_best_outlier">$2,538.38</div>
                        <div class="apple-tile-subtitle" id="card_outlier_sub">Single largest winning day recorded</div>
                    </div>
                </div>

                <!-- Current Total Net Profit -->
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="apple-metric-tile">
                        <div class="apple-tile-header">
                            <span class="apple-tile-title">Current Total Net Profit</span>
                            <div class="apple-tile-icon" style="background: var(--apple-green-soft); color: var(--apple-green);">
                                <i class="dw dw-money-2"></i>
                            </div>
                        </div>
                        <div class="apple-tile-value text-success" id="val_total_profit">$2,082.52</div>
                        <div class="apple-tile-subtitle">Cumulative closed PnL on account</div>
                    </div>
                </div>

                <!-- Current Consistency % -->
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="apple-metric-tile">
                        <div class="apple-tile-header">
                            <span class="apple-tile-title">Current Consistency %</span>
                            <div class="apple-tile-icon" style="background: var(--apple-purple-soft); color: var(--apple-purple);">
                                <i class="dw dw-percentage"></i>
                            </div>
                        </div>
                        <div class="apple-tile-value" id="val_curr_consistency">121.89%</div>
                        <div class="apple-tile-subtitle">Formula: (Best Day ÷ Total Profit) × 100</div>
                    </div>
                </div>

                <!-- Target Total Profit for Compliance -->
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="apple-metric-tile">
                        <div class="apple-tile-header">
                            <span class="apple-tile-title">Target Profit for Compliance</span>
                            <div class="apple-tile-icon" style="background: var(--apple-orange-soft); color: var(--apple-orange);">
                                <i class="dw dw-target"></i>
                            </div>
                        </div>
                        <div class="apple-tile-value" id="val_target_total_profit" style="color: var(--apple-orange);">$5,076.76</div>
                        <div class="apple-tile-subtitle">Formula: Best Day ÷ <span class="rule-pct-text">50%</span></div>
                    </div>
                </div>

                <!-- Additional Profit Needed -->
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="apple-metric-tile">
                        <div class="apple-tile-header">
                            <span class="apple-tile-title">Additional Profit Needed</span>
                            <div class="apple-tile-icon" style="background: var(--apple-red-soft); color: var(--apple-red);">
                                <i class="dw dw-chart"></i>
                            </div>
                        </div>
                        <div class="apple-tile-value" id="val_additional_needed" style="color: var(--apple-red);">$2,994.24</div>
                        <div class="apple-tile-subtitle">Profit to bring consistency ≤ <span class="rule-pct-text">50%</span></div>
                    </div>
                </div>

                <!-- Remaining to Phase Target -->
                <div class="col-xl-4 col-md-6 mb-3">
                    <div class="apple-metric-tile">
                        <div class="apple-tile-header">
                            <span class="apple-tile-title">Remaining to Final Target</span>
                            <div class="apple-tile-icon" style="background: var(--apple-blue-soft); color: var(--apple-blue);">
                                <i class="dw dw-flag"></i>
                            </div>
                        </div>
                        <div class="apple-tile-value text-primary" id="val_remaining_to_target">$7,917.48</div>
                        <div class="apple-tile-subtitle">Distance to $<span id="label_account_target">10,000</span> goal</div>
                    </div>
                </div>
            </div>

            <!-- ── SECTION 4: Daily Target Scenarios Matrix ── -->
            <div class="apple-card" id="section-scenarios">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 12px;">
                    <div>
                        <h5 class="weight-700 m-0" style="font-size: 17px; letter-spacing: -0.01em;">Daily Profit Scenarios & Reward Breakdown</h5>
                        <div class="font-13 text-muted">Estimated trading days to compliance based on average daily performance.</div>
                    </div>
                    <span class="apple-pill-btn" style="background: #f0f0f5; color: var(--apple-text-primary); font-weight: 600;">
                        Target Buffer: $<span class="val-needed-text">2,994.24</span>
                    </span>
                </div>

                <div class="apple-table-wrap">
                    <table class="apple-table">
                        <thead>
                            <tr>
                                <th>Daily Average</th>
                                <th>Days to Compliance</th>
                                <th>Days to Full Target</th>
                                <th>Projected Total PnL</th>
                                <th>Final Consistency</th>
                                <th>Mindset / Risk Level</th>
                            </tr>
                        </thead>
                        <tbody id="scenario_matrix_body">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── SECTION 5: Interactive Day-by-Day Simulator ── -->
            <div class="apple-card" id="section-simulator">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 12px;">
                    <div>
                        <h5 class="weight-700 m-0" style="font-size: 17px; letter-spacing: -0.01em;">Interactive Day-by-Day Scenario Planner</h5>
                        <div class="font-13 text-muted">Simulate upcoming winning or losing days to preview real-time consistency dilution.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="apple-pill-btn" id="btn_add_sim_day" style="background: var(--apple-blue-soft); color: var(--apple-blue); font-weight: 600;">
                            <i class="dw dw-add mr-1"></i> Add Day
                        </button>
                        <button type="button" class="apple-pill-btn" id="btn_reset_sim">
                            <i class="dw dw-refresh mr-1"></i> Reset
                        </button>
                    </div>
                </div>

                <div class="apple-table-wrap mb-2">
                    <table class="apple-table text-center">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Step</th>
                                <th style="width: 25%;">Simulated Day PnL ($)</th>
                                <th style="width: 20%;">Running Total PnL</th>
                                <th style="width: 15%;">Largest Day</th>
                                <th style="width: 15%;">Live Consistency</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="sim_days_tbody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
                <div class="font-12 text-muted text-right">
                    * Values update dynamically on every keystroke.
                </div>
            </div>

            <!-- ── SECTION 6: Keynote Discipline Card ── -->
            <div class="apple-keynote-card" id="section-mindset">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-12 mb-3 mb-lg-0">
                        <div class="font-11 uppercase weight-700 text-warning mb-1" style="letter-spacing: 0.06em;">Execution Mindset</div>
                        <h4 class="text-white weight-700 mb-3" style="font-size: 22px; letter-spacing: -0.02em;">
                            "PROTECT THE ACCOUNT. DON'T CHASE THE CONSISTENCY."
                        </h4>
                        <div class="row font-13" style="line-height: 1.8; color: #a1a1a6;">
                            <div class="col-md-6">
                                <div>✓ <strong>$500 – $1,000 is plenty:</strong> Let normal setups compound.</div>
                                <div>✓ <strong>Standard risk:</strong> Never oversize lots to rush targets.</div>
                            </div>
                            <div class="col-md-6">
                                <div>✓ <strong>Accept red days:</strong> Stop at daily limit without revenge.</div>
                                <div>✓ <strong>Time dilutes outliers:</strong> Math will take care of the rest.</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="p-3 rounded-xl text-center" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px;">
                            <div class="font-11 uppercase opacity-60 mb-2">Daily Execution Guide</div>
                            <div class="font-13 weight-600" style="line-height: 1.8;">
                                <span class="text-success">+$750 – $1,000</span> = Done for the day 🟢<br>
                                <span style="color: #64d2ff;">+$500</span> = Great winning day 🟢<br>
                                <span class="text-warning">$0</span> = Preserved capital 🟡<br>
                                <span class="text-danger">-$500</span> = Stop, review tomorrow 🔴
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const allAccounts = @json($accountsData);
    let currentAccount = @json($selectedAccount);
    let isSandboxMode = true; // MUST default to Custom Sandbox Mode

    // Simulation Data
    let simDays = [
        { label: 'Day 1', pnl: 750 },
        { label: 'Day 2', pnl: 750 },
        { label: 'Day 3', pnl: 750 },
        { label: 'Day 4', pnl: 750 },
        { label: 'Day 5', pnl: 750 }
    ];

    function init() {
        if (currentAccount) {
            applyAccount(currentAccount.id);
        } else {
            enableSandboxMode();
        }
        setupScrollSpy();
    }

    function applyAccount(accountId) {
        if (accountId === 'sandbox') {
            enableSandboxMode();
            return;
        }

        const found = allAccounts.find(a => a.id == accountId);
        if (!found) {
            enableSandboxMode();
            return;
        }

        isSandboxMode = false;
        $('#btn_mode_account').addClass('active');
        $('#btn_mode_sandbox').removeClass('active');
        $('#account_selector_col').slideDown();
        $('#sandbox_inputs_row').slideUp();
        $('#account_select').val(found.id);
        $('#active_mode_badge').text('Account Synced: ' + found.name);

        if (!found.has_consistency_rule) {
            $('#no_rule_account_alert').slideDown();
        } else {
            $('#no_rule_account_alert').slideUp();
        }

        $('#input_rule_percent').val(found.consistency_rule_percent || 50);
        $('#select_rule_type').val(found.consistency_rule_type || 'day');
        $('#input_profit_target').val(found.profit_target || 10000);

        recalculate();
    }

    function enableSandboxMode() {
        isSandboxMode = true;
        $('#btn_mode_sandbox').addClass('active');
        $('#btn_mode_account').removeClass('active');
        $('#account_selector_col').slideUp();
        $('#sandbox_inputs_row').slideDown();
        $('#no_rule_account_alert').slideUp(); // Always hide disabled alert on sandbox
        $('#account_select').val('sandbox');
        $('#active_mode_badge').text('Custom Sandbox Simulation');
        $('#account_display_name').text('Mode: Custom Sandbox Simulation');
        recalculate();
    }

    $('#btn_mode_account').click(function() {
        if (allAccounts.length > 0) {
            const firstAcc = allAccounts.find(a => a.has_consistency_rule) || allAccounts[0];
            applyAccount(firstAcc.id);
        } else {
            iziToastNotify('info', 'No trading accounts found. Create an account in Accounts menu or use Sandbox mode.');
        }
    });

    $('#btn_mode_sandbox').click(function() {
        enableSandboxMode();
    });

    $('#account_select').change(function() {
        applyAccount($(this).val());
    });

    $('.rule-preset-btn').click(function() {
        $('#input_rule_percent').val($(this).data('val'));
        recalculate();
    });

    $('#input_rule_percent, #select_rule_type, #input_profit_target, #manual_total_profit, #manual_outlier_profit').on('input change', function() {
        recalculate();
    });

    function recalculate() {
        const rulePercent = parseFloat($('#input_rule_percent').val()) || 50.0;
        const ruleType = $('#select_rule_type').val();
        const profitTarget = parseFloat($('#input_profit_target').val()) || 0.0;

        $('#rule_percent_display').text(rulePercent + '%');
        $('.rule-pct-text').text(rulePercent + '%');
        $('#card_outlier_title').text(ruleType === 'trade' ? 'Best Single Trade' : 'Best Winning Day');
        $('#manual_outlier_label').text(ruleType === 'trade' ? 'Highest Single Trade Profit ($)' : 'Highest Single Day Profit ($)');
        $('#card_outlier_sub').text(ruleType === 'trade' ? 'Highest profit on a single closed trade' : 'Highest profit generated on a single trading day');

        let totalProfit = 0;
        let outlierProfit = 0;
        let accountName = 'Custom Sandbox Simulation';

        if (isSandboxMode || $('#account_select').val() === 'sandbox') {
            totalProfit = parseFloat($('#manual_total_profit').val()) || 0.0;
            outlierProfit = parseFloat($('#manual_outlier_profit').val()) || 0.0;
            accountName = 'Custom Sandbox Simulation';
        } else {
            const accountId = $('#account_select').val();
            const acc = allAccounts.find(a => a.id == accountId);
            if (acc && acc.stats) {
                totalProfit = parseFloat(acc.stats.total_pnl) || 0.0;
                outlierProfit = ruleType === 'trade' ? (parseFloat(acc.stats.best_trade_profit) || 0.0) : (parseFloat(acc.stats.best_day_profit) || 0.0);
                accountName = `${acc.name} (${acc.broker})`;
            }
        }

        $('#account_display_name').text(`Account: ${accountName}`);

        // Conditional display of Mathematical Proof: only show when numbers are present
        if (outlierProfit > 0 && totalProfit !== 0) {
            $('#math_proof_container').slideDown(200);
        } else {
            $('#math_proof_container').slideUp(200);
        }

        // Consistency Percentage calculation
        let consistencyPct = 0;
        if (totalProfit > 0 && outlierProfit > 0) {
            consistencyPct = (outlierProfit / totalProfit) * 100;
        } else if (outlierProfit > 0 && totalProfit <= 0) {
            consistencyPct = 100.0;
        }

        // Compliance Target Total Profit
        let targetTotalProfit = 0;
        if (outlierProfit > 0 && rulePercent > 0) {
            targetTotalProfit = outlierProfit / (rulePercent / 100.0);
        }

        // Additional Profit Needed
        let additionalNeeded = 0;
        if (targetTotalProfit > totalProfit) {
            additionalNeeded = targetTotalProfit - totalProfit;
        }

        const isCompliant = (totalProfit > 0 && outlierProfit > 0 && consistencyPct <= rulePercent) || (outlierProfit === 0 && totalProfit >= 0);
        const remainingToTarget = Math.max(0, profitTarget - totalProfit);

        // Update Hero Banner
        $('#hero_consistency_pct').text(consistencyPct.toFixed(2) + '%');
        $('#hero_subtext').text(`Requirement: ≤ ${rulePercent.toFixed(1)}%`);
        $('#hero_pct_text').text(consistencyPct.toFixed(2) + '%');
        $('#hero_limit_text').text(rulePercent.toFixed(1) + '%');

        if (isCompliant) {
            $('#hero_status_card').removeClass('apple-hero-accumulating').addClass('apple-hero-compliant');
            $('#compliance_badge')
                .removeClass('apple-pill-amber')
                .addClass('apple-pill-green')
                .html(`<i class="dw dw-checked mr-1"></i> COMPLIANT (≤ ${rulePercent}%)`);
            $('#hero_status_heading').text('Your Account is Currently Compliant');
            $('#hero_status_message').html(`Great execution! Your largest ${ruleType} ($${outlierProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}) represents <strong>${consistencyPct.toFixed(2)}%</strong> of profits, which is comfortably within the <strong>${rulePercent}%</strong> rule.`);
        } else {
            $('#hero_status_card').removeClass('apple-hero-compliant').addClass('apple-hero-accumulating');
            $('#compliance_badge')
                .removeClass('apple-pill-green')
                .addClass('apple-pill-amber')
                .html(`<i class="dw dw-alert mr-1"></i> ACCUMULATING (${consistencyPct.toFixed(2)}% > ${rulePercent}%)`);
            $('#hero_status_heading').text("You Haven't Failed — Keep Building Normal Profits");
            $('#hero_status_message').html(`Your biggest ${ruleType} ($${outlierProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}) is <strong>${consistencyPct.toFixed(2)}%</strong> of total profit. Accumulate <strong>$${additionalNeeded.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong> in normal trades to bring consistency into full compliance!`);
        }

        // Progress Bars
        $('#bar_consistency')
            .css('width', Math.min(100, consistencyPct) + '%')
            .css('background', consistencyPct <= rulePercent ? '#34c759' : '#ff9500');
        $('#bar_consistency_label').text(`${consistencyPct.toFixed(1)}% / ${rulePercent}% max`);

        const profitRatio = targetTotalProfit > 0 ? Math.min(100, (totalProfit / targetTotalProfit) * 100) : 100;
        $('#bar_profit').css('width', Math.max(0, profitRatio) + '%');
        $('#bar_profit_curr').text('$' + totalProfit.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}));
        $('#bar_profit_target').text('$' + targetTotalProfit.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}));
        $('#bar_profit_label').text(`${profitRatio.toFixed(1)}% of Target`);

        // Stat Tiles
        $('#val_best_outlier').text('$' + outlierProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#val_total_profit').text('$' + totalProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#val_curr_consistency').text(consistencyPct.toFixed(2) + '%');
        $('#val_target_total_profit').text('$' + targetTotalProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#val_additional_needed').text('$' + additionalNeeded.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#val_remaining_to_target').text('$' + remainingToTarget.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#label_account_target').text(profitTarget.toLocaleString());
        $('.val-needed-text').text(additionalNeeded.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        // Math Logic Box (Inside Parameters Card)
        $('#m_best_day, #m_best_day_3').text(outlierProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#m_total_pnl, #m_total_pnl_2').text(totalProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#m_result_pct, #m_result_pct_2').text(consistencyPct.toFixed(2) + '%').removeClass('text-success text-danger').addClass(isCompliant ? 'text-success' : 'text-danger');
        $('#m_rule_dec').text((rulePercent / 100).toFixed(2));
        $('#m_req_total, #m_req_total_2').text('$' + targetTotalProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#m_diff').text('$' + additionalNeeded.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        // Render Tables
        renderScenarioMatrix(outlierProfit, totalProfit, additionalNeeded, remainingToTarget, profitTarget, rulePercent);
        renderSimulationTable(outlierProfit, totalProfit, rulePercent);
    }

    function renderScenarioMatrix(outlierProfit, totalProfit, additionalNeeded, remainingToTarget, profitTarget, rulePercent) {
        const scenarios = [
            { target: 500, label: '+$500 / day', tag: 'Steady & Safe', color: '#86868b', mindset: 'Zero pressure, protects capital.' },
            { target: 750, label: '+$750 / day', tag: 'Recommended', color: '#34c759', mindset: 'Optimal speed without forcing setups.' },
            { target: 1000, label: '+$1,000 / day', tag: 'High Target', color: '#0071e3', mindset: 'Stop after hitting this target!' },
            { target: 1500, label: '+$1,500 / day', tag: 'Outlier Risk', color: '#ff9500', mindset: 'Take if natural; avoid oversizing lots.' }
        ];

        let rowsHtml = '';
        scenarios.forEach(sc => {
            const daysToComply = additionalNeeded > 0 ? Math.ceil(additionalNeeded / sc.target) : 0;
            const daysToTarget = remainingToTarget > 0 ? Math.ceil(remainingToTarget / sc.target) : 0;
            const finalProfit = totalProfit + (daysToTarget * sc.target);
            const finalConsistency = finalProfit > 0 ? ((outlierProfit / finalProfit) * 100) : 0;

            rowsHtml += `
                <tr>
                    <td>
                        <strong class="font-14">${sc.label}</strong>
                        <span class="apple-pill-btn ml-2" style="background: rgba(0,0,0,0.04); color: ${sc.color}; font-weight: 600;">${sc.tag}</span>
                    </td>
                    <td>
                        <span class="weight-600 ${daysToComply === 0 ? 'text-success' : ''}">
                            ${daysToComply === 0 ? 'Compliant ✅' : `${daysToComply} Days`}
                        </span>
                    </td>
                    <td>
                        <strong>${daysToTarget} Days</strong>
                    </td>
                    <td>
                        <span class="text-muted">$${finalProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    </td>
                    <td>
                        <span class="weight-600 ${finalConsistency <= rulePercent ? 'text-success' : 'text-danger'}">
                            ${finalConsistency.toFixed(2)}%
                        </span>
                    </td>
                    <td class="font-12 text-muted">
                        ${sc.mindset}
                    </td>
                </tr>
            `;
        });

        $('#scenario_matrix_body').html(rowsHtml);
    }

    function renderSimulationTable(currentOutlier, currentTotalProfit, rulePercent) {
        let runningTotal = currentTotalProfit;
        let runningOutlier = currentOutlier;
        let tbodyHtml = '';

        const initialConsistency = runningTotal > 0 ? ((runningOutlier / runningTotal) * 100) : (runningOutlier > 0 ? 100 : 0);
        const initialCompliant = (runningTotal > 0 && runningOutlier > 0 && initialConsistency <= rulePercent) || (runningOutlier === 0);

        tbodyHtml += `
            <tr style="background: #fafafc;">
                <td><span class="apple-pill-btn" style="background: #1d1d1f; color: #fff;">Current</span></td>
                <td class="text-muted">—</td>
                <td class="weight-600">$${runningTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="text-muted">$${runningOutlier.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="${initialCompliant ? 'text-success' : 'text-danger'} weight-600">
                    ${initialConsistency.toFixed(2)}%
                </td>
                <td>
                    <span class="apple-pill-btn" style="background: ${initialCompliant ? 'var(--apple-green-soft)' : 'var(--apple-orange-soft)'}; color: ${initialCompliant ? 'var(--apple-green)' : 'var(--apple-orange)'}; font-weight: 600;">
                        ${initialCompliant ? 'Compliant' : 'Accumulating'}
                    </span>
                </td>
                <td></td>
            </tr>
        `;

        simDays.forEach((day, idx) => {
            runningTotal += day.pnl;
            if (day.pnl > runningOutlier) {
                runningOutlier = day.pnl;
            }

            const dayConsistency = runningTotal > 0 ? ((runningOutlier / runningTotal) * 100) : 100;
            const dayCompliant = (runningTotal > 0 && runningOutlier > 0 && dayConsistency <= rulePercent);

            tbodyHtml += `
                <tr>
                    <td class="weight-600 text-secondary">${day.label}</td>
                    <td>
                        <input type="number" step="50" class="apple-input text-center sim-day-input" data-index="${idx}" value="${day.pnl}" style="height: 36px; max-width: 140px; margin: 0 auto;">
                    </td>
                    <td class="weight-700">
                        $${runningTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </td>
                    <td class="text-muted">
                        $${runningOutlier.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                    </td>
                    <td class="weight-700 ${dayCompliant ? 'text-success' : 'text-danger'}">
                        ${dayConsistency.toFixed(2)}%
                    </td>
                    <td>
                        <span class="apple-pill-btn" style="background: ${dayCompliant ? 'var(--apple-green-soft)' : 'var(--apple-orange-soft)'}; color: ${dayCompliant ? 'var(--apple-green)' : 'var(--apple-orange)'}; font-weight: 600;">
                            ${dayCompliant ? '✓ Compliant' : 'Accumulating'}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-link text-muted remove-sim-day-btn p-0" data-index="${idx}" style="font-size: 16px;">
                            &times;
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#sim_days_tbody').html(tbodyHtml);
    }

    $(document).on('input', '.sim-day-input', function() {
        const index = $(this).data('index');
        const val = parseFloat($(this).val()) || 0;
        simDays[index].pnl = val;
        recalculate();
    });

    $(document).on('click', '.remove-sim-day-btn', function() {
        const index = $(this).data('index');
        simDays.splice(index, 1);
        recalculate();
    });

    $('#btn_add_sim_day').click(function() {
        const nextNum = simDays.length + 1;
        simDays.push({ label: `Day ${nextNum}`, pnl: 750 });
        recalculate();
    });

    $('#btn_reset_sim').click(function() {
        simDays = [
            { label: 'Day 1', pnl: 750 },
            { label: 'Day 2', pnl: 750 },
            { label: 'Day 3', pnl: 750 },
            { label: 'Day 4', pnl: 750 },
            { label: 'Day 5', pnl: 750 }
        ];
        recalculate();
    });

    // ── Floating Left Nav ScrollSpy & Smooth Scrolling ──
    function setupScrollSpy() {
        const sections = document.querySelectorAll('.apple-card, .apple-hero, .apple-keynote-card, #section-metrics');
        const navLinks = document.querySelectorAll('.apple-nav-link');

        window.addEventListener('scroll', function() {
            let current = '';
            const scrollY = window.pageYOffset;

            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                const sectionHeight = section.offsetHeight;
                if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElem = document.querySelector(targetId);
                if (targetElem) {
                    const yOffset = -85;
                    const y = targetElem.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            });
        });
    }

    init();
});
</script>
@endpush
