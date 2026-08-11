@extends('layouts.app')

@section('title', $account->name . ' - Account Analytics')

@section('content')
<div class="page-header mb-20">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title d-flex align-items-center">
                <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary btn-sm mr-3">
                    <i class="dw dw-left-arrow1"></i> Back to Accounts
                </a>
                <h4 class="mb-0">{{ $account->name }}</h4>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 text-right">
            <span class="badge font-14 mr-2" style="background-color: {{ $account->color }}; color: #fff; padding: 8px 14px; border-radius: 6px;">
                {{ $account->broker }} ({{ $account->account_number ?? 'N/A' }})
            </span>
            <span class="badge badge-outline-secondary font-14 mr-2" style="padding: 8px 12px;">
                Phase: {{ $account->phase }}
            </span>
            <span class="badge badge-{{ $account->status === 'Active' ? 'success' : ($account->status === 'Passed' ? 'info' : ($account->status === 'Failed' ? 'danger' : 'secondary')) }} font-14" style="padding: 8px 12px;">
                {{ $account->status }}
            </span>
        </div>
    </div>
</div>

<!-- KPIs Overview Row -->
<div class="row mb-30">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
        <div class="card-box pd-20 height-100-p" style="border-left: 4px solid {{ $account->color }};">
            <div class="font-14 text-muted weight-500 uppercase">Account Size</div>
            <div class="font-24 weight-700 text-dark mt-2">${{ number_format($account->account_size, 2) }}</div>
            <div class="font-12 text-muted mt-1">Platform: {{ $account->platform }} | {{ $account->currency }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
        <div class="card-box pd-20 height-100-p" style="border-left: 4px solid {{ $stats['net_pnl'] >= 0 ? '#10b981' : '#ef4444' }};">
            <div class="font-14 text-muted weight-500 uppercase">Current Equity / Balance</div>
            <div class="font-24 weight-700 text-{{ $stats['net_pnl'] >= 0 ? 'success' : 'danger' }} mt-2">
                ${{ number_format($stats['current_balance'], 2) }}
            </div>
            <div class="font-12 text-muted mt-1">
                Net PnL: {{ $stats['net_pnl'] >= 0 ? '+' : '' }}${{ number_format($stats['net_pnl'], 2) }} 
                ({{ number_format(($stats['net_pnl'] / ($account->initial_balance ?: 1)) * 100, 2) }}%)
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
        <div class="card-box pd-20 height-100-p" style="border-left: 4px solid #3b82f6;">
            <div class="font-14 text-muted weight-500 uppercase">Net Return (RR)</div>
            <div class="font-24 weight-700 text-blue mt-2">{{ $stats['net_rr'] >= 0 ? '+' : '' }}{{ $stats['net_rr'] }}R</div>
            <div class="font-12 text-muted mt-1">Win Rate: <strong>{{ $stats['win_rate'] }}%</strong> ({{ $stats['win_count'] }}W / {{ $stats['loss_count'] }}L)</div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
        <div class="card-box pd-20 height-100-p" style="border-left: 4px solid #8b5cf6;">
            <div class="font-14 text-muted weight-500 uppercase">Max Peak Drawdown</div>
            <div class="font-24 weight-700 text-danger mt-2">-${{ number_format($stats['max_drawdown'], 2) }}</div>
            <div class="font-12 text-muted mt-1">Total Closed Trades: {{ $stats['total_trades'] }}</div>
        </div>
    </div>
</div>

<!-- Account Progress Targets & Rules -->
@if($account->profit_target || $account->max_daily_loss || $account->max_total_loss)
<div class="card-box pd-20 mb-30">
    <h5 class="h5 mb-20 text-blue"><i class="dw dw-target mr-2"></i> Account Rules & Objectives Progress</h5>
    <div class="row">
        @if($account->profit_target)
            @php
                $targetPct = min(100, max(0, ($stats['net_pnl'] / $account->profit_target) * 100));
            @endphp
            <div class="col-md-4 mb-20">
                <div class="p-3 border rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="weight-600">Profit Target</span>
                        <span class="weight-700 text-success">${{ number_format($stats['net_pnl'], 2) }} / ${{ number_format($account->profit_target, 2) }}</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $targetPct }}%" aria-valuenow="{{ $targetPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-right font-12 text-muted mt-1">{{ number_format($targetPct, 1) }}% Achieved</div>
                </div>
            </div>
        @endif

        @if($account->max_daily_loss)
            @php
                $todayPnL = $trades->where('trade_date', '>=', \Carbon\Carbon::today()->format('Y-m-d'))->sum('pnl');
                $dailyLossUsed = max(0, -$todayPnL);
                $dailyLossPct = min(100, ($dailyLossUsed / $account->max_daily_loss) * 100);
            @endphp
            <div class="col-md-4 mb-20">
                <div class="p-3 border rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="weight-600">Max Daily Loss Limit</span>
                        <span class="weight-700 text-danger">${{ number_format($dailyLossUsed, 2) }} / ${{ number_format($account->max_daily_loss, 2) }}</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-{{ $dailyLossPct > 70 ? 'danger' : 'warning' }}" role="progressbar" style="width: {{ $dailyLossPct }}%"></div>
                    </div>
                    <div class="text-right font-12 text-muted mt-1">{{ number_format(100 - $dailyLossPct, 1) }}% Daily Buffer Remaining</div>
                </div>
            </div>
        @endif

        @if($account->max_total_loss)
            @php
                $drawdownPct = min(100, ($stats['max_drawdown'] / $account->max_total_loss) * 100);
            @endphp
            <div class="col-md-4 mb-20">
                <div class="p-3 border rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="weight-600">Max Total Drawdown Limit</span>
                        <span class="weight-700 text-danger">${{ number_format($stats['max_drawdown'], 2) }} / ${{ number_format($account->max_total_loss, 2) }}</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-{{ $drawdownPct > 70 ? 'danger' : 'info' }}" role="progressbar" style="width: {{ $drawdownPct }}%"></div>
                    </div>
                    <div class="text-right font-12 text-muted mt-1">${{ number_format(max(0, $account->max_total_loss - $stats['max_drawdown']), 2) }} Max Loss Buffer Left</div>
                </div>
            </div>
        @endif
    </div>
</div>
@endif

<!-- Equity Curve Chart -->
<div class="card-box pd-20 mb-30">
    <div class="d-flex justify-content-between align-items-center mb-20">
        <h5 class="h5 mb-0 text-blue"><i class="dw dw-line-chart mr-2"></i> Account Equity Growth Curve</h5>
        <span class="font-12 text-muted">Initial Capital: ${{ number_format($account->initial_balance, 2) }}</span>
    </div>
    <div style="height: 350px;">
        <canvas id="equityChart"></canvas>
    </div>
</div>

<!-- Sessions & Assets Performance Breakdown -->
<div class="row mb-30">
    <div class="col-md-6 mb-20">
        <div class="card-box pd-20 height-100-p">
            <h5 class="h5 mb-15 text-blue"><i class="dw dw-wall-clock1 mr-2"></i> Session Performance</h5>
            <div class="table-responsive">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th class="text-left">Session</th>
                            <th>Trades</th>
                            <th>Win Rate</th>
                            <th>Net PnL ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['sessions'] as $sessionName => $sData)
                            <tr>
                                <td class="text-left font-weight-bold">{{ strtoupper(str_replace('_', ' ', $sessionName)) }}</td>
                                <td>{{ $sData['count'] }}</td>
                                <td>
                                    @php $sWr = $sData['count'] > 0 ? round(($sData['wins'] / $sData['count']) * 100, 1) : 0; @endphp
                                    <span class="badge badge-{{ $sWr >= 50 ? 'success' : 'danger' }}">{{ $sWr }}%</span>
                                </td>
                                <td class="font-weight-bold text-{{ $sData['pnl'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $sData['pnl'] >= 0 ? '+' : '' }}${{ number_format($sData['pnl'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted">No closed trade data for sessions.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-20">
        <div class="card-box pd-20 height-100-p">
            <h5 class="h5 mb-15 text-blue"><i class="dw dw-vector mr-2"></i> Top Traded Assets</h5>
            <div class="table-responsive">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th class="text-left">Asset</th>
                            <th>Trades</th>
                            <th>Win Rate</th>
                            <th>Net PnL ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['assets'] as $assetName => $aData)
                            <tr>
                                <td class="text-left font-weight-bold">{{ strtoupper($assetName) }}</td>
                                <td>{{ $aData['count'] }}</td>
                                <td>
                                    @php $aWr = $aData['count'] > 0 ? round(($aData['wins'] / $aData['count']) * 100, 1) : 0; @endphp
                                    <span class="badge badge-{{ $aWr >= 50 ? 'success' : 'danger' }}">{{ $aWr }}%</span>
                                </td>
                                <td class="font-weight-bold text-{{ $aData['pnl'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $aData['pnl'] >= 0 ? '+' : '' }}${{ number_format($aData['pnl'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted">No closed trade data for assets.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Account Trade History -->
<div class="card-box pd-20 mb-30">
    <h5 class="h5 mb-20 text-blue"><i class="dw dw-list mr-2"></i> Account Trades History</h5>
    <div class="table-responsive">
        <table class="table stripe hover nowrap" id="account_trades_table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Asset</th>
                    <th>Direction</th>
                    <th>Session</th>
                    <th>Outcome</th>
                    <th>RR</th>
                    <th>PNL ($)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trades as $tr)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($tr->trade_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ strtoupper($tr->getAssetName()) }}</strong></td>
                        <td>
                            <span class="badge badge-{{ $tr->direction === 'long' ? 'success' : 'danger' }}">
                                {{ strtoupper($tr->direction) }}
                            </span>
                        </td>
                        <td>{{ strtoupper(str_replace('_', ' ', $tr->session)) }}</td>
                        <td>
                            <span class="badge badge-{{ $tr->outcome === 'win' ? 'success' : ($tr->outcome === 'loss' ? 'danger' : 'secondary') }}">
                                {{ strtoupper($tr->outcome) }}
                            </span>
                        </td>
                        <td>{{ number_format($tr->rr, 1) }}R</td>
                        <td class="font-weight-bold text-{{ $tr->pnl >= 0 ? 'success' : 'danger' }}">
                            {{ $tr->pnl >= 0 ? '+' : '' }}${{ number_format($tr->pnl, 2) }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $tr->status === 'open' ? 'success' : 'danger' }}">
                                {{ strtoupper($tr->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No trades assigned to this account yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Equity Chart setup
    let equityData = @json($stats['equity_curve']);
    let ctx = document.getElementById('equityChart').getContext('2d');
    
    let labels = equityData.map(item => item.date);
    let balances = equityData.map(item => item.balance);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Account Equity ($)',
                data: balances,
                borderColor: '{{ $account->color ?: "#3b82f6" }}',
                backgroundColor: 'rgba(59, 130, 246, 0.08)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6
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
                            return 'Balance: $' + context.raw.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Account Trades DataTable
    $('#account_trades_table').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10
    });
});
</script>
@endpush
