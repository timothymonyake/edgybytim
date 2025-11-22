<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index()
    {
        return view('trades.calendar');
    }

    public function events(Request $request)
    {
        // Daily data
        $daily = Trade::selectRaw('
                trade_date as date,
                SUM(pnl) as pnl,
                COUNT(id) as trades,
                SUM(CASE WHEN outcome = "win" THEN 1 ELSE 0 END) as won_trades,
                SUM(CASE WHEN outcome = "loss" THEN 1 ELSE 0 END) as lost_trades
            ')
            ->groupBy('trade_date')
            ->get();

        // Monthly summary
        $monthly = Trade::selectRaw('
                DATE_FORMAT(trade_date, "%Y-%m") as month,
                SUM(pnl) as pnl,
                COUNT(id) as trades,
                SUM(CASE WHEN outcome = "win" THEN 1 ELSE 0 END) as won_trades,
                SUM(CASE WHEN outcome = "loss" THEN 1 ELSE 0 END) as lost_trades,
                SUM(rr) as total_rr
            ')
            ->groupBy('month')
            ->get()
            ->mapWithKeys(function ($row) {
                $completedTrades = $row->won_trades + $row->lost_trades;
                $winRate = $completedTrades > 0 ? ($row->won_trades / $completedTrades) * 100 : 0;
                
                return [
                    $row->month => [
                        'net_pnl' => round($row->pnl, 2),
                        'trades' => $row->trades,
                        'win_rate' => round($winRate, 1),
                        'total_rr' => round($row->total_rr, 2)
                    ]
                ];
            });

        $events = [];

        foreach ($daily as $row) {
            $isProfit = $row->pnl > 0;
            $isLoss = $row->pnl < 0;
            
            $completedTrades = $row->won_trades + $row->lost_trades;
            $winRate = $completedTrades > 0 ? ($row->won_trades / $completedTrades) * 100 : 0;
            
            // Determine background color
            if ($row->pnl == 0) {
                $color = '#d3d3d3';
            } elseif ($isProfit) {
                $color = '#126C65'; // Green for profit
            } else {
                $color = '#9B3E4A'; // Red for loss
            }

            // Background event
            $events[] = [
                'start' => $row->date,
                'rendering' => 'background',
                'color' => $color,
            ];

            // Foreground event with text
            $events[] = [
                'title' => '
                    <div class="net_profit">' . ($isProfit ? '+' : '') . number_format($row->pnl, 2) . '</div>
                    <div class="trade_count">' . $row->trades . ' Trade' . ($row->trades > 1 ? 's' : '') . '</div>
                    <div class="win_rate">WR: ' . number_format($winRate, 1) . '%</div>
                ',
                'start' => $row->date,
                'color' => 'transparent',
                'textColor' => '#fff',
            ];
        }

        // Weekly summary - group by year and week number
        $weekly = Trade::selectRaw('
                YEARWEEK(trade_date, 1) as year_week,
                MIN(trade_date) as week_start,
                SUM(pnl) as pnl,
                COUNT(id) as trades,
                SUM(CASE WHEN outcome = "win" THEN 1 ELSE 0 END) as won_trades,
                SUM(CASE WHEN outcome = "loss" THEN 1 ELSE 0 END) as lost_trades
            ')
            ->groupBy('year_week')
            ->get()
            ->mapWithKeys(function ($row) {
                $completedTrades = $row->won_trades + $row->lost_trades;
                $winRate = $completedTrades > 0 ? ($row->won_trades / $completedTrades) * 100 : 0;
                
                return [
                    $row->year_week => [
                        'week_start' => $row->week_start,
                        'pnl' => round($row->pnl, 2),
                        'trades' => $row->trades,
                        'win_rate' => round($winRate, 1)
                    ]
                ];
            });

        return response()->json([
            'events' => $events,
            'monthly_summary' => $monthly,
            'weekly_summary' => $weekly,
        ]);
    }

    public function getTradesByDate($date)
    {
        $trades = Trade::where('trade_date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate summary statistics
        $totalPnl = $trades->sum('pips');
        $totalTrades = $trades->count();
        $wonTrades = $trades->where('outcome', 'win')->count();
        $lostTrades = $trades->where('outcome', 'loss')->count();
        $completedTrades = $wonTrades + $lostTrades;
        
        $winRate = $completedTrades > 0 
            ? round(($wonTrades / $completedTrades) * 100, 1) . '%' 
            : 'N/A';
        
        $avgRr = $trades->where('rr', '>', 0)->avg('rr');
        $avgRr = $avgRr ? round($avgRr, 2) : 'N/A';

        // Format trades data
        $formattedTrades = $trades->map(function($trade) {
            // Parse emotions from entry_pd_array if it exists
            $emotions = [];
            if ($trade->entry_pd_array) {
                $emotions = json_decode($trade->entry_pd_array, true) ?? [];
            }

            return [
                'id' => $trade->id,
                'symbol' => $trade->symbol,
                'type' => ucfirst($trade->type),
                'outcome' => $trade->outcome,
                'pips' => round($trade->pips, 2),
                'rr' => $trade->rr ?? 'N/A',
                'time' => $trade->created_at->format('H:i'),
                'notes' => $trade->notes,
                'emotions' => $emotions,
            ];
        });

        return response()->json([
            'summary' => [
                'total_pnl' => round($totalPnl, 2),
                'total_trades' => $totalTrades,
                'win_rate' => $winRate,
                'avg_rr' => $avgRr,
                'won_trades' => $wonTrades,
                'lost_trades' => $lostTrades,
            ],
            'trades' => $formattedTrades,
        ]);
    }
}
