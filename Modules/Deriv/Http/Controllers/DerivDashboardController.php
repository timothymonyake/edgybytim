<?php

namespace Modules\Deriv\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Deriv\Models\DerivAccount;
use Illuminate\Support\Facades\Auth;

class DerivDashboardController extends Controller
{
    /**
     * Show the trading dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $accounts = DerivAccount::where('user_id', $user->id)->with(['metrics', 'trades'])->get();
        $activeAccount = $accounts->firstWhere('account_id', $request->query('account_id')) ?? $accounts->first();

        if (!$activeAccount) {
            return view('deriv::connect_guide'); // Placeholder for when no accounts are connected
        }

        $recentTrades = $activeAccount->trades()->latest()->paginate(10);
        $dailyPerformance = $activeAccount->dailyPerformances()->orderBy('date', 'desc')->take(30)->get();

        return view('deriv::dashboard', compact('activeAccount', 'accounts', 'recentTrades', 'dailyPerformance'));
    }

    /**
     * Store a new Deriv account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|string|unique:deriv_accounts,account_id',
            'api_token' => 'required|string',
        ]);

        $account = DerivAccount::create([
            'user_id' => Auth::id(),
            'account_id' => $validated['account_id'],
            'api_token' => $validated['api_token'],
            'balance' => 0,
            'equity' => 0,
        ]);

        return redirect()->route('deriv.dashboard', ['account_id' => $account->account_id])
            ->with('success', 'Deriv account connected successfully.');
    }

    /**
     * API: Get equity curve data for charts.
     */
    public function getEquityData(DerivAccount $account)
    {
        $data = $account->dailyPerformances()
            ->orderBy('date', 'asc')
            ->get(['date', 'ending_balance']);

        return response()->json($data);
    }
}
