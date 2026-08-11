<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deriv Trading Dashboard | Edgy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #111827; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4b5563; }
        .glass-card { background: rgba(31, 41, 55, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(75, 85, 99, 0.3); }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 font-sans antialiased" x-data="{ showModal: false }">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar / Navigation -->
    <aside class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col">
        <div class="p-6">
            <h1 class="text-2xl font-black text-indigo-500 tracking-tighter italic">EDGY<span class="text-white not-italic">DERIV</span></h1>
        </div>
        <nav class="flex-1 px-4 space-y-2 overflow-y-auto">
            <p class="text-xs font-semibold text-gray-500 uppercase px-3 py-2">Accounts</p>
            @foreach($accounts as $acc)
                <a href="?account_id={{ $acc->account_id }}" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ $activeAccount->id == $acc->id ? 'bg-indigo-600 shadow-lg shadow-indigo-500/20 text-white' : 'hover:bg-gray-800 text-gray-400' }}">
                    <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-xs font-bold ring-2 ring-gray-700">
                        {{ substr($acc->account_id, 0, 2) }}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-medium truncate">{{ $acc->account_id }}</p>
                        <p class="text-[10px] opacity-60">{{ $acc->currency }} Account</p>
                    </div>
                </a>
            @endforeach
        </nav>
        <div class="p-4 border-t border-gray-800">
            <button @click="showModal = true" class="w-full flex items-center justify-center space-x-2 py-2 border border-dashed border-gray-700 rounded-lg text-gray-500 hover:border-indigo-500 hover:text-indigo-500 transition-all text-sm">
                <span>+ Connect New</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-8 space-y-8">
        <!-- Header -->
        <header class="flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-bold">Analytics Overview</h2>
                <p class="text-gray-400">Trading performance for <span class="text-indigo-400 font-mono">{{ $activeAccount->account_id }}</span></p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase">Current Equity</p>
                    <p class="text-2xl font-mono font-bold text-green-400">${{ number_format($activeAccount->equity, 2) }}</p>
                </div>
                <button class="p-2 bg-gray-800 hover:bg-gray-700 rounded-lg border border-gray-700 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </header>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="glass-card p-6 rounded-2xl">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Balance</p>
                <h3 class="text-2xl font-mono font-bold">${{ number_format($activeAccount->balance, 2) }}</h3>
                <div class="mt-2 text-[10px] text-gray-500">Last sync: Just now</div>
            </div>
            <div class="glass-card p-6 rounded-2xl relative overflow-hidden group">
                <div class="absolute -right-2 -top-2 w-16 h-16 bg-indigo-500/10 rounded-full group-hover:bg-indigo-500/20 transition-all"></div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Prop Win Rate</p>
                <h3 class="text-2xl font-mono font-bold {{ $activeAccount->metrics->win_rate >= 50 ? 'text-green-400' : 'text-orange-400' }}">
                    {{ number_format($activeAccount->metrics->win_rate ?? 0, 1) }}%
                </h3>
                <div class="mt-2 flex items-center space-x-1">
                    <div class="h-1 flex-1 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500" style="width: {{ $activeAccount->metrics->win_rate ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="glass-card p-6 rounded-2xl">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Profit Factor</p>
                <h3 class="text-2xl font-mono font-bold {{ $activeAccount->metrics->profit_factor >= 1.5 ? 'text-green-400' : 'text-gray-300' }}">
                    {{ number_format($activeAccount->metrics->profit_factor ?? 0, 2) }}
                </h3>
                <p class="text-[10px] text-gray-500 mt-2">Gross P/L Ratio</p>
            </div>
            <div class="glass-card p-6 rounded-2xl">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Avg. Risk:Reward</p>
                <h3 class="text-2xl font-mono font-bold">1:{{ number_format($activeAccount->metrics->risk_reward_ratio ?? 0, 1) }}</h3>
                <p class="text-[10px] text-gray-500 mt-2">Closed trades avg</p>
            </div>
            <div class="glass-card p-6 rounded-2xl border-l-4 border-indigo-500">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Total PnL</p>
                @php $totalPnl = $activeAccount->trades->sum('profit_loss'); @endphp
                <h3 class="text-2xl font-mono font-bold {{ $totalPnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $totalPnl >= 0 ? '+' : '' }}${{ number_format($totalPnl, 2) }}
                </h3>
                <div class="mt-2 text-[10px] {{ $totalPnl >= 0 ? 'text-green-500/60' : 'text-red-500/60' }}">
                    {{ $activeAccount->metrics->total_trades }} total trades
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 glass-card p-6 rounded-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="font-bold text-gray-300">Equity Growth Curve</h4>
                    <div class="flex space-x-2">
                        <button class="text-[10px] px-2 py-1 bg-gray-800 rounded border border-gray-700 text-gray-400">1D</button>
                        <button class="text-[10px] px-2 py-1 bg-indigo-600 rounded border border-indigo-500 text-white shadow-lg shadow-indigo-500/20">ALL</button>
                    </div>
                </div>
                <div class="h-80 w-full">
                    <canvas id="equityChart"></canvas>
                </div>
            </div>
            <div class="glass-card p-6 rounded-2xl">
                <h4 class="font-bold text-gray-300 mb-6">Daily PnL Distribution</h4>
                <div class="h-80 w-full">
                    <canvas id="pnlBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Lower Analytics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Win/Loss Breakdown -->
            <div class="glass-card p-6 rounded-2xl space-y-6">
                <h4 class="font-bold text-gray-300">Detailed Metrics</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm border-b border-gray-800 pb-2">
                        <span class="text-gray-500">Total Wins</span>
                        <span class="text-green-400 font-mono">{{ $activeAccount->metrics->wins }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-gray-800 pb-2">
                        <span class="text-gray-500">Total Losses</span>
                        <span class="text-red-400 font-mono">{{ $activeAccount->metrics->losses }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-gray-800 pb-2">
                        <span class="text-gray-500">Avg. Win</span>
                        <span class="text-green-400 font-mono">${{ number_format($activeAccount->metrics->avg_win, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-gray-800 pb-2">
                        <span class="text-gray-500">Avg. Loss</span>
                        <span class="text-red-400 font-mono">-${{ number_format(abs($activeAccount->metrics->avg_loss), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Expectancy</span>
                        <span class="font-mono bg-indigo-500/10 text-indigo-400 px-2 rounded">${{ number_format($activeAccount->metrics->expectancy, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Trade History Table -->
            <div class="lg:col-span-3 glass-card rounded-2xl overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                    <h4 class="font-bold text-gray-300">Recent Transactions</h4>
                    <a href="#" class="text-xs text-indigo-400 hover:text-indigo-300">View Full History →</a>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] text-gray-500 border-b border-gray-800 uppercase tracking-widest">
                                <th class="px-6 py-4">Symbol</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4 text-center">Stake</th>
                                <th class="px-6 py-4 text-center">Entry/Exit</th>
                                <th class="px-6 py-4 text-right">Profit/Loss</th>
                                <th class="px-6 py-4 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-800/50">
                            @foreach($recentTrades as $trade)
                            <tr class="hover:bg-gray-800/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-200 group-hover:text-white">{{ str_replace('R_', '', $trade->symbol) }}</div>
                                    <div class="text-[10px] text-gray-600">ID: {{ $trade->id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $trade->contract_type == 'BUY' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }}">
                                        {{ $trade->contract_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-mono text-gray-400">${{ number_format($trade->stake, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-[11px] font-mono whitespace-nowrap">
                                        {{ $trade->entry_price }} <span class="text-gray-600 mx-1">→</span> {{ $trade->exit_price ?? 'Open' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-mono font-bold transition-all group-hover:scale-105 {{ $trade->profit_loss >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $trade->profit_loss >= 0 ? '+' : '' }}${{ number_format($trade->profit_loss, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-gray-500 text-[11px]">{{ $trade->opened_at->format('M d, H:i') }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 bg-gray-900/50 border-t border-gray-800">
                    {{ $recentTrades->links() }}
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Connection Modal -->
<div x-show="showModal" 
     x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100">
    
    <div @click.away="showModal = false" class="bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl border border-gray-700 overflow-hidden text-gray-100">
        <div class="p-6 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-xl font-bold">Connect Deriv Account</h3>
            <button @click="showModal = false" class="text-gray-500 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form action="{{ route('deriv.accounts.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Account ID</label>
                <input type="text" name="account_id" required placeholder="e.g. CR123456" 
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500 transition-colors">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">API Token</label>
                <input type="password" name="api_token" required placeholder="Paste your API token here" 
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-indigo-500 transition-colors">
            </div>
            
            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-lg shadow-indigo-500/20 transition-all">
                    Validate & Connect
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxEquity = document.getElementById('equityChart').getContext('2d');
        const ctxPnl = document.getElementById('pnlBarChart').getContext('2d');

        // Equity Chart
        new Chart(ctxEquity, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyPerformance->pluck('date')->map(fn($d) => $d->format('M d'))->reverse()) !!},
                datasets: [{
                    label: 'Equity',
                    data: {!! json_encode($dailyPerformance->pluck('ending_balance')->reverse()) !!},
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#6b7280', font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { color: '#6b7280', font: { size: 10 } } }
                }
            }
        });

        // PnL Bar Chart
        new Chart(ctxPnl, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyPerformance->pluck('date')->map(fn($d) => $d->format('M d'))->reverse()) !!},
                datasets: [{
                    label: 'Daily PnL',
                    data: {!! json_encode($dailyPerformance->pluck('pnl')->reverse()) !!},
                    backgroundColor: {!! json_encode($dailyPerformance->pluck('pnl')->reverse()->map(fn($v) => $v >= 0 ? 'rgba(74, 222, 128, 0.5)' : 'rgba(248, 113, 113, 0.5)')) !!},
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#6b7280', font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { color: '#6b7280', font: { size: 10 } } }
                }
            }
        });
    });
</script>

</body>
</html>
