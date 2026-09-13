<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsistencyCalculatorController extends Controller
{
    /**
     * Display the consistency compliance calculator.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $accounts = Account::where('user_id', $userId)
            ->withCount('trades')
            ->orderBy('created_at', 'desc')
            ->get();

        // Selected account (defaults to null / custom sandbox unless explicitly passed via query param)
        $selectedAccount = null;
        if ($request->filled('account_id')) {
            $selectedAccount = $accounts->firstWhere('id', $request->account_id);
        }

        $selectedAccountStats = $selectedAccount ? $selectedAccount->consistency_stats : null;

        // Accounts summary for selector
        $accountsData = $accounts->map(function ($acc) {
            return [
                'id' => $acc->id,
                'name' => $acc->name,
                'broker' => $acc->broker,
                'currency' => $acc->currency ?: 'USD',
                'account_size' => (float) $acc->account_size,
                'profit_target' => (float) ($acc->profit_target ?: 0),
                'has_consistency_rule' => (bool) $acc->has_consistency_rule,
                'consistency_rule_percent' => (float) ($acc->consistency_rule_percent ?: 50.0),
                'consistency_rule_type' => $acc->consistency_rule_type ?: 'day',
                'stats' => $acc->consistency_stats,
            ];
        });

        return view('consistency.index', compact('accounts', 'selectedAccount', 'selectedAccountStats', 'accountsData'));
    }

    /**
     * Get live JSON data for a specific account.
     */
    public function getAccountData(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $account->id,
            'name' => $account->name,
            'broker' => $account->broker,
            'currency' => $account->currency ?: 'USD',
            'account_size' => (float) $account->account_size,
            'profit_target' => (float) ($account->profit_target ?: 0),
            'has_consistency_rule' => (bool) $account->has_consistency_rule,
            'consistency_rule_percent' => (float) ($account->consistency_rule_percent ?: 50.0),
            'consistency_rule_type' => $account->consistency_rule_type ?: 'day',
            'stats' => $account->consistency_stats,
        ]);
    }
}
