<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccountController extends Controller
{
    /**
     * Display a listing of trading accounts.
     */
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->withCount('trades')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate dynamic metrics for each account card
        foreach ($accounts as $account) {
            $closedTrades = Trade::where('account_id', $account->id)->where('status', 'closed')->get();
            $netPnl = $closedTrades->sum('pnl');
            $account->computed_balance = $account->initial_balance + $netPnl;
            $account->net_rr = round($closedTrades->sum('rr'), 2);
            $account->net_pnl = round($netPnl, 2);
            
            $totalClosed = $closedTrades->count();
            $wins = $closedTrades->where('outcome', 'win')->count();
            $account->win_rate = $totalClosed > 0 ? round(($wins / $totalClosed) * 100, 1) : 0;
            
            // Best Pair / Session
            $bestPair = Trade::where('account_id', $account->id)
                ->where('status', 'closed')
                ->where('outcome', 'win')
                ->with('associatedAsset')
                ->get()
                ->groupBy(function($trade) {
                    return $trade->getAssetName();
                })
                ->sortByDesc->count()
                ->keys()
                ->first();
                
            $account->best_pair = $bestPair ?? '-';
        }

        return view('accounts.index', compact('accounts'));
    }

    /**
     * Store a newly created trading account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'broker' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_size' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'platform' => 'nullable|string|max:255',
            'phase' => 'required|string|in:Challenge,Verification,Funded,Personal,Demo',
            'status' => 'required|string|in:Active,Passed,Failed,Archived',
            'initial_balance' => 'required|numeric|min:0',
            'current_balance' => 'nullable|numeric',
            'profit_target' => 'nullable|numeric',
            'max_daily_loss' => 'nullable|numeric',
            'max_total_loss' => 'nullable|numeric',
            'leverage' => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['currency'] = $validated['currency'] ?? 'USD';
        $validated['color'] = $validated['color'] ?? '#3b82f6';
        if (!isset($validated['current_balance']) || $validated['current_balance'] === null) {
            $validated['current_balance'] = $validated['initial_balance'];
        }

        $account = Account::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Account created successfully!', 'account' => $account]);
        }

        return redirect()->route('accounts.index')->with('success', 'Account created successfully!');
    }

    /**
     * Display the specified account's detailed analytics page.
     */
    public function show(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $trades = Trade::where('account_id', $account->id)
            ->with('associatedAsset')
            ->orderBy('trade_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $closedTrades = $trades->where('status', 'closed');
        $totalClosed = $closedTrades->count();
        $wins = $closedTrades->where('outcome', 'win')->count();
        $losses = $closedTrades->where('outcome', 'loss')->count();
        $breakevens = $closedTrades->where('outcome', 'breakeven')->count();
        $winRate = $totalClosed > 0 ? round(($wins / $totalClosed) * 100, 1) : 0;

        $totalPnl = $closedTrades->sum('pnl');
        $totalRr = $closedTrades->sum('rr');
        $currentBalance = $account->initial_balance + $totalPnl;

        // Challenge Progress metrics
        $profitTarget = $account->profit_target;
        $profitAchieved = max(0, $totalPnl);
        $profitTargetProgress = ($profitTarget && $profitTarget > 0) ? min(100, round(($profitAchieved / $profitTarget) * 100, 1)) : null;
        $profitTargetRemaining = ($profitTarget && $profitTarget > 0) ? max(0, $profitTarget - $totalPnl) : null;

        // Peak balance & max drawdown calculation
        $runningBalance = $account->initial_balance;
        $peakBalance = $account->initial_balance;
        $maxDrawdownAmount = 0;
        $maxDrawdownPercent = 0;
        $equityCurve = [];

        // Initial point
        $equityCurve[] = [
            'date' => $account->start_date ? $account->start_date->format('Y-m-d') : ($trades->first() ? $trades->first()->trade_date : date('Y-m-d')),
            'balance' => $account->initial_balance,
            'pnl' => 0
        ];

        foreach ($closedTrades as $trade) {
            $runningBalance += $trade->pnl;
            if ($runningBalance > $peakBalance) {
                $peakBalance = $runningBalance;
            }
            $drawdown = $peakBalance - $runningBalance;
            if ($drawdown > $maxDrawdownAmount) {
                $maxDrawdownAmount = $drawdown;
                $maxDrawdownPercent = $peakBalance > 0 ? ($drawdown / $peakBalance) * 100 : 0;
            }

            $equityCurve[] = [
                'date' => $trade->trade_date,
                'balance' => round($runningBalance, 2),
                'pnl' => round($trade->pnl, 2)
            ];
        }

        // Today's PnL for Max Daily Loss calculation
        $todayStr = Carbon::now()->format('Y-m-d');
        $todayPnl = $closedTrades->filter(function($t) use ($todayStr) {
            return Carbon::parse($t->trade_date)->format('Y-m-d') === $todayStr;
        })->sum('pnl');

        $dailyLossRemaining = $account->max_daily_loss ? max(0, $account->max_daily_loss + $todayPnl) : null; // If todayPnl is -100, loss remaining = max_daily - 100
        $overallLossRemaining = $account->max_total_loss ? max(0, $account->max_total_loss + $totalPnl) : null;

        // Best / Worst Session
        $sessionStats = $closedTrades->groupBy('session')->map(function($grp, $sess) {
            $sessWins = $grp->where('outcome', 'win')->count();
            $sessTotal = $grp->count();
            $sessWinRate = $sessTotal > 0 ? ($sessWins / $sessTotal) * 100 : 0;
            return [
                'session' => $sess ? ucwords(str_replace('_', ' ', $sess)) : 'Unknown',
                'pnl' => $grp->sum('pnl'),
                'wins' => $sessWins,
                'win_rate' => round($sessWinRate, 1),
                'count' => $sessTotal
            ];
        })->sortByDesc('pnl');

        $bestSession = $sessionStats->first();
        $worstSession = $sessionStats->last();

        // Best / Worst Instrument
        $instrumentStats = $closedTrades->groupBy(function($t) {
            return $t->getAssetName();
        })->map(function($grp, $inst) {
            $instWins = $grp->where('outcome', 'win')->count();
            $instTotal = $grp->count();
            $instWinRate = $instTotal > 0 ? ($instWins / $instTotal) * 100 : 0;
            return [
                'instrument' => strtoupper($inst),
                'pnl' => $grp->sum('pnl'),
                'wins' => $instWins,
                'win_rate' => round($instWinRate, 1),
                'count' => $instTotal
            ];
        })->sortByDesc('pnl');

        $bestInstrument = $instrumentStats->first();
        $worstInstrument = $instrumentStats->last();

        // Rule Compliance Score
        $planFollowedCount = $trades->where('plan_followed', 1)->count();
        $complianceScore = $trades->count() > 0 ? round(($planFollowedCount / $trades->count()) * 100, 1) : 100;

        $stats = [
            'total_trades' => $closedTrades->count(),
            'win_count' => $wins,
            'loss_count' => $losses,
            'breakeven_count' => $breakevens,
            'win_rate' => $winRate,
            'net_pnl' => round($totalPnl, 2),
            'net_rr' => round($totalRr, 2),
            'current_balance' => round($currentBalance, 2),
            'max_drawdown' => round($maxDrawdownAmount, 2),
            'equity_curve' => $equityCurve,
            'sessions' => $sessionStats,
            'assets' => $instrumentStats,
            'best_session' => $bestSession,
            'worst_session' => $worstSession,
            'best_instrument' => $bestInstrument,
            'worst_instrument' => $worstInstrument,
            'compliance_score' => $complianceScore,
        ];

        return view('accounts.show', compact('account', 'trades', 'stats'));
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'broker' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_size' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'platform' => 'nullable|string|max:255',
            'phase' => 'required|string|in:Challenge,Verification,Funded,Personal,Demo',
            'status' => 'required|string|in:Active,Passed,Failed,Archived',
            'initial_balance' => 'required|numeric|min:0',
            'current_balance' => 'nullable|numeric',
            'profit_target' => 'nullable|numeric',
            'max_daily_loss' => 'nullable|numeric',
            'max_total_loss' => 'nullable|numeric',
            'leverage' => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $account->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Account updated successfully!', 'account' => $account]);
        }

        return redirect()->back()->with('success', 'Account updated successfully!');
    }

    /**
     * Toggle status to Archived / Active.
     */
    public function archive(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $newStatus = $account->status === 'Archived' ? 'Active' : 'Archived';
        $account->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Account {$newStatus} successfully!",
            'status' => $newStatus
        ]);
    }

    /**
     * Delete the specified account.
     */
    public function destroy(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $account->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Account deleted successfully!']);
        }

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully!');
    }
}
