<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Account;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class TradeController extends Controller
{
    // Show trades list
    public function index()
    {
        $assets = Asset::where('user_id', Auth::id())->orderBy('name')->get();
        $assetTypes = AssetType::all();
        $accounts = Account::where('user_id', Auth::id())->orderBy('name')->get();
        return view('trades.index', compact('assets', 'assetTypes', 'accounts'));
    }

    // Data for DataTables
    public function getTrades(Request $request)
    {
        $trades = Trade::with(['associatedAsset.assetType', 'account'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $start = \Carbon\Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                $end = \Carbon\Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                $trades->whereBetween('trade_date', [$start, $end]);
            } catch (\Exception $e) {
                // If date parsing fails, don't apply date filter
                \Log::warning('Date parsing failed in getTrades: ' . $e->getMessage());
            }
        }

        if ($request->filled('account_id') && $request->account_id != '0') {
            $trades->where('account_id', $request->account_id);
        }

        if ($request->filled('market') && $request->market != '0') {
            $trades->where('asset_id', $request->market);
        }

        if ($request->filled('asset_type') && $request->asset_type != '0') {
            $trades->whereHas('associatedAsset', function($q) use ($request) {
                $q->where('asset_type_id', $request->asset_type);
            });
        }

        if ($request->filled('direction') && $request->direction != '0') {
            $trades->where('direction', $request->direction);
        }

        if ($request->filled('session') && $request->session != '0') {
            $trades->where('session', $request->session);
        }

        if ($request->filled('outcome') && $request->outcome != '0') {
            $trades->where('outcome', $request->outcome);
        }

        if ($request->filled('hin_day') && $request->hin_day != '0') {
            $trades->where('hin_day', $request->hin_day == 'yes' ? 1 : 0);
        }

        if ($request->filled('plan_followed') && $request->plan_followed != '0') {
            $trades->where('plan_followed', $request->plan_followed == 'yes' ? 1 : 0);
        }

        if ($request->filled('entry_type') && $request->entry_type != '0') {
            $trades->where('entry_type', $request->entry_type);
        }

        if ($request->filled('entry_pd') && $request->entry_pd != '0') {
            $trades->where('entry_pd_array', 'like', '%' . $request->entry_pd . '%');
        }

        if ($request->filled('has_emotions') && $request->has_emotions != '0') {
            if ($request->has_emotions == 'yes') {
                $trades->whereNotNull('emotions')->where('emotions', '!=', '');
            } else {
                $trades->where(function ($q) {
                    $q->whereNull('emotions')->orWhere('emotions', '');
                });
            }
        }

        if ($request->filled('has_news') && $request->has_news != '0') {
            if ($request->has_news == 'yes') {
                $trades->whereNotNull('news')->where('news', '!=', '');
            } else {
                $trades->where(function ($q) {
                    $q->whereNull('news')->orWhere('news', '');
                });
            }
        }

        if ($request->filled('trade_id')) {
            $trades->where('id', $request->trade_id);
        }

        return DataTables::of($trades)
            ->addColumn('account', function ($row) {
                if ($row->account) {
                    return '<span class="badge font-12" style="background-color: ' . e($row->account->color) . '; color: #fff; padding: 5px 8px; border-radius: 4px;">'
                        . e($row->account->name) . '</span>';
                }
                return '<span class="badge badge-light">-</span>';
            })
            ->addColumn('asset', function ($row) {
                return strtoupper($row->getAssetName());
            })
            ->addColumn('session', function ($row) {
                if ($row->session === 'london_open') {
                    return ' <span class="badge badge-primary" style="background:#2664ff">
                        <i class="dw dw-wall-clock1"></i> London Open
                    </span>';
                } else if ($row->session === 'ny_open') {
                    return '<span class="badge badge-info" style="background:#f53540">
                        <i class="dw dw-wall-clock1"></i> New York Open
                    </span>';
                } else if ($row->session === 'london_close') {
                    return '<span class="badge badge-secondary" style="background:#5aafb0">
                        <i class="dw dw-wall-clock1"></i> London Close
                    </span>';
                } else {
                    return '<span class="badge badge-warning" style="background:#ff9501">
                        <i class="dw dw-wall-clock1"></i> Asian
                    </span>';
                }
            })
            ->addColumn('plan_followed', function ($row) {
                if ($row->plan_followed == 1) {
                    return '<i class="dw dw-checked text-green" style="font-size: 18px;font-weight:bold;"></i>';
                } else {
                    return '<i class=" dw dw-cancel text-danger" style="font-size: 18px;font-weight:bold;"></i>';
                }
            })
            ->editColumn('entry_pd_array', function ($row) {
                if (!$row->entry_pd_array) return '';
                $items = is_array($row->entry_pd_array) ? $row->entry_pd_array : json_decode($row->entry_pd_array, true);
                return collect($items)->map(function ($item) {
                    return '<span class="badge badge-primary mr-1">' . $item . '</span>';
                })->implode('');
            })
            ->addColumn('outcome', function ($row) {
                if ($row->outcome === 'pending') {
                    return '<span class="badge badge-warning">PENDING</span>';
                } else if ($row->outcome === 'win') {
                    return '<span class="badge badge-success">WIN</span>';
                } else if ($row->outcome === 'loss') {
                    return '<span class="badge badge-danger">LOSS</span>';
                } else {
                    return '<span class="badge badge-secondary">BREAKEVEN</span>';
                }
            })
            ->addColumn('hin_day', function ($row) {
                if ($row->hin_day == true) {
                    return '<span class="badge badge-success">YES</span>';
                } else {
                    return '<span class="badge badge-danger">NO</span>';
                }
            })
            ->editColumn('status', function ($row) {
                if ($row->status === 'open') {
                    return '<span class="badge badge-success">OPEN</span>';
                } else {
                    return '<span class="badge badge-danger">CLOSED</span>';
                }
            })
            ->addColumn('trade_status', function ($row) {
                return $row->status;
            })
            ->addColumn('entry_type', fn($row) => strtoupper($row->entry_type))
            ->addColumn('rr', fn($row) => number_format($row->rr, 1))
            ->addColumn('pnl', fn($row) => number_format($row->pnl, 2))
            ->addColumn('direction', function ($trade) {
                if ($trade->direction == 'long') {
                    return ' <span class="badge badge-success">
                        <i class="dw dw-up-arrow2"></i> Long
                    </span>';
                }
                return '<span class="badge badge-danger">
                        <i class="dw dw-down-arrow2"></i> Short
                    </span>';
            })
            ->addColumn('screenshots', function ($row) {
                $count = $row->screenshots()->count();
                if ($count > 0) {
                    return '<button class="btn btn-sm btn-info view-screenshots" data-trade-id="' . $row->id . '">
                        <i class="dw dw-image1"></i> <span class="badge badge-light">' . $count . '</span>
                    </button>';
                }
                return '<button class="btn btn-sm btn-secondary" disabled>
                        <i class="dw dw-image1"></i> <span class="badge badge-light">0</span>
                    </button>';
            })
            ->addColumn('trade_screenshots', function($row){
                $screenshots = $row->screenshots()->orderBy('created_at', 'desc')->get();
                return json_encode($screenshots);
            })
            ->addColumn('actions', function ($row) {
                if ($row->status == 'closed') {
                    return '';
                }
                return '
                    <button class="btn btn-sm btn-warning edit-trade" data-id="' . $row->id . '"><i class="dw dw-edit2"></i></button>
                    <button class="btn btn-sm btn-danger delete-trade" data-id="' . $row->id . '"><i class="dw dw-delete-3"></i></button>
                ';
            })
            ->rawColumns(['actions', 'account', 'hin_day', 'screenshots', 'status', 'trade_status', 'entry_pd_array', 'plan_followed', 'direction', 'outcome', 'rr', 'session', 'asset'])
            ->make(true);
    }

    public function edit(Trade $trade)
    {
        return response()->json($trade);
    }

    // Store trade
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();

            // If account_id is empty or not set, clean it to null
            if (empty($data['account_id']) || $data['account_id'] == '0') {
                $data['account_id'] = null;
            }

            $trade = Trade::create($data);
            return response()->json(['success' => true, 'message' => 'Trade successfully saved!', 'trade' => $trade]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    // Update trade
    public function update(Request $request, Trade $trade)
    {
        try {
            $data = $request->all();

            if (empty($data['account_id']) || $data['account_id'] == '0') {
                $data['account_id'] = null;
            }

            $trade->update($data);
            return response()->json(['success' => true, 'message' => 'Trade updated saved!']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    // Delete trade
    public function destroy(Trade $trade)
    {
        try {
            $trade->delete();
            return response()->json(['success' => true, 'message' => 'Trade deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete trade']);
        }
    }

    // Export trades optimized for AI Agents / LLMs
    public function exportAi(Request $request)
    {
        $query = Trade::with(['associatedAsset.assetType', 'account', 'screenshots'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $start = \Carbon\Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                $end = \Carbon\Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                $query->whereBetween('trade_date', [$start, $end]);
            } catch (\Exception $e) {
                \Log::warning('Date parsing failed in exportAi: ' . $e->getMessage());
            }
        }

        if ($request->filled('account_id') && $request->account_id != '0') {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('market') && $request->market != '0') {
            $query->where('asset_id', $request->market);
        }

        if ($request->filled('asset_type') && $request->asset_type != '0') {
            $query->whereHas('associatedAsset', function($q) use ($request) {
                $q->where('asset_type_id', $request->asset_type);
            });
        }

        if ($request->filled('direction') && $request->direction != '0') {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('session') && $request->session != '0') {
            $query->where('session', $request->session);
        }

        if ($request->filled('outcome') && $request->outcome != '0') {
            $query->where('outcome', $request->outcome);
        }

        if ($request->filled('hin_day') && $request->hin_day != '0') {
            $query->where('hin_day', $request->hin_day == 'yes' ? 1 : 0);
        }

        if ($request->filled('plan_followed') && $request->plan_followed != '0') {
            $query->where('plan_followed', $request->plan_followed == 'yes' ? 1 : 0);
        }

        if ($request->filled('entry_type') && $request->entry_type != '0') {
            $query->where('entry_type', $request->entry_type);
        }

        if ($request->filled('entry_pd') && $request->entry_pd != '0') {
            $query->where('entry_pd_array', 'like', '%' . $request->entry_pd . '%');
        }

        if ($request->filled('has_emotions') && $request->has_emotions != '0') {
            if ($request->has_emotions == 'yes') {
                $query->whereNotNull('emotions')->where('emotions', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('emotions')->orWhere('emotions', '');
                });
            }
        }

        if ($request->filled('has_news') && $request->has_news != '0') {
            if ($request->has_news == 'yes') {
                $query->whereNotNull('news')->where('news', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('news')->orWhere('news', '');
                });
            }
        }

        if ($request->filled('trade_id')) {
            $query->where('id', $request->trade_id);
        }

        $trades = $query->get();

        $accounts = Account::where('user_id', Auth::id())->get()->map(function($acc) {
            return [
                'account_id' => $acc->id,
                'name' => $acc->name,
                'broker' => $acc->broker ?? 'N/A',
                'account_size' => (float)$acc->account_size,
                'currency' => $acc->currency ?? 'USD',
                'initial_balance' => (float)$acc->initial_balance,
                'current_balance' => (float)$acc->calculated_balance,
                'phase' => $acc->phase ?? 'N/A',
                'status' => $acc->status ?? 'active',
                'leverage' => $acc->leverage ?? 'N/A',
                'notes' => $acc->notes ?? '',
            ];
        });

        $formattedTrades = $trades->map(function($t) {
            $pdArrays = is_array($t->entry_pd_array) ? $t->entry_pd_array : json_decode($t->entry_pd_array ?? '[]', true);

            return [
                'trade_id' => $t->id,
                'date' => $t->trade_date,
                'status' => $t->status,
                'outcome' => strtoupper($t->outcome ?? 'PENDING'),
                'asset' => [
                    'symbol' => strtoupper($t->getAssetName()),
                    'type' => $t->associatedAsset->assetType->name ?? 'Unknown',
                ],
                'direction' => strtoupper($t->direction ?? ''),
                'session' => $t->session ? ucwords(str_replace('_', ' ', $t->session)) : 'N/A',
                'risk_reward_ratio' => (float)($t->rr ?? 0),
                'profit_loss_usd' => (float)($t->pnl ?? 0),
                'pips' => $t->pips !== null ? (float)$t->pips : null,
                'lot_size' => $t->lot_size !== null ? (float)$t->lot_size : null,
                'risk_percent' => $t->risk_percent !== null ? (float)$t->risk_percent : null,
                'execution_type' => strtoupper($t->entry_type ?? 'N/A'),
                'institutional_pd_arrays' => $pdArrays ?? [],
                'followed_plan' => (bool)$t->plan_followed,
                'high_impact_news_day' => (bool)$t->hin_day,
                'news_context' => $t->news ?? '',
                'trader_emotions' => $t->emotions ?? '',
                'entry_narrative' => $t->entry_narrative ?? '',
                'notes' => $t->notes ?? '',
                'setup_description' => $t->setup ?? '',
                'daily_log_url' => $t->daily_log_url ?? '',
                'account' => $t->account ? [
                    'account_id' => $t->account->id,
                    'name' => $t->account->name,
                    'account_size' => (float)$t->account->account_size,
                    'currency' => $t->account->currency ?? 'USD',
                    'broker' => $t->account->broker ?? 'N/A',
                    'current_balance' => (float)$t->account->calculated_balance,
                ] : null,
                'screenshots' => $t->screenshots->map(function($s) {
                    return [
                        'screenshot_id' => $s->id,
                        'url' => $s->url,
                        'stage' => $s->when ?? 'general',
                        'notes' => $s->notes ?? '',
                        'created_at' => $s->created_at ? $s->created_at->toIso8601String() : null
                    ];
                })->values()->all(),
                'created_at' => $t->created_at ? $t->created_at->toIso8601String() : null,
            ];
        });

        $totalTradesCount = $formattedTrades->count();
        $closedTrades = $formattedTrades->where('status', 'closed');
        $wins = $closedTrades->where('outcome', 'WIN')->count();
        $losses = $closedTrades->where('outcome', 'LOSS')->count();
        $closedCount = $closedTrades->count();
        $winRate = $closedCount > 0 ? round(($wins / $closedCount) * 100, 2) : 0;
        $totalPnl = $closedTrades->sum('profit_loss_usd');
        $avgRr = $closedCount > 0 ? round($closedTrades->avg('risk_reward_ratio'), 2) : 0;
        $planFollowedCount = $formattedTrades->where('followed_plan', true)->count();
        $planFollowedRate = $totalTradesCount > 0 ? round(($planFollowedCount / $totalTradesCount) * 100, 2) : 0;

        $exportData = [
            'meta' => [
                'system' => 'Edgy Trading Journal',
                'version' => '1.0',
                'exported_at' => \Carbon\Carbon::now()->toIso8601String(),
                'user_id' => Auth::id(),
                'total_trades_exported' => $totalTradesCount,
                'ai_instructions' => 'This JSON dataset contains structured trading journal records including execution metrics, market contexts, institutional PD arrays, trader psychological notes, rule compliance, account metrics, and chart screenshot URLs. Use this dataset to analyze trading behavior, calculate win rates, evaluate risk management, critique trade execution quality, and provide actionable advice to improve trader performance.'
            ],
            'performance_summary' => [
                'total_trades' => $totalTradesCount,
                'closed_trades' => $closedCount,
                'wins' => $wins,
                'losses' => $losses,
                'win_rate_percent' => $winRate,
                'total_pnl_usd' => round($totalPnl, 2),
                'average_rr' => $avgRr,
                'plan_followed_rate_percent' => $planFollowedRate,
            ],
            'accounts' => $accounts->values()->all(),
            'trades' => $formattedTrades->values()->all(),
        ];

        if ($request->query('format') === 'markdown') {
            $md = "# Edgy Trading Journal Export for AI Analysis\n\n";
            $md .= "**Exported At:** " . $exportData['meta']['exported_at'] . "\n";
            $md .= "**Total Trades Exported:** " . $totalTradesCount . "\n";
            $md .= "**Overall Win Rate:** " . $winRate . "%\n";
            $md .= "**Total Net PnL:** $" . number_format($totalPnl, 2) . "\n";
            $md .= "**Average R:R:** " . $avgRr . "\n";
            $md .= "**Plan Compliance:** " . $planFollowedRate . "%\n\n";

            $md .= "## Accounts Overview\n\n";
            foreach ($accounts as $acc) {
                $md .= "- **" . $acc['name'] . "** (" . ($acc['broker'] ?? 'N/A') . "): Size $" . number_format($acc['account_size']) . " " . $acc['currency'] . " | Balance: $" . number_format($acc['current_balance'], 2) . " | Phase: " . $acc['phase'] . "\n";
            }
            $md .= "\n---\n\n## Detailed Trades List\n\n";

            foreach ($formattedTrades as $t) {
                $md .= "### Trade #" . $t['trade_id'] . " - " . $t['date'] . " | " . $t['asset']['symbol'] . " (" . $t['direction'] . ")\n";
                $md .= "- **Account:** " . ($t['account']['name'] ?? 'N/A') . "\n";
                $md .= "- **Session:** " . $t['session'] . " | **Execution Type:** " . $t['execution_type'] . "\n";
                $md .= "- **Outcome:** " . $t['outcome'] . " | **PnL:** $" . number_format($t['profit_loss_usd'], 2) . " | **R:R:** " . $t['risk_reward_ratio'] . "\n";
                $md .= "- **Institutional Arrays (PD):** " . (empty($t['institutional_pd_arrays']) ? 'None' : implode(', ', $t['institutional_pd_arrays'])) . "\n";
                $md .= "- **Plan Followed:** " . ($t['followed_plan'] ? 'Yes' : 'No') . " | **High Impact News Day:** " . ($t['high_impact_news_day'] ? 'Yes' : 'No') . "\n";
                if (!empty($t['setup_description'])) $md .= "- **Setup:** " . $t['setup_description'] . "\n";
                if (!empty($t['news_context'])) $md .= "- **News Context:** " . $t['news_context'] . "\n";
                if (!empty($t['trader_emotions'])) $md .= "- **Emotions:** " . $t['trader_emotions'] . "\n";
                if (!empty($t['entry_narrative'])) $md .= "- **Entry Narrative:** " . $t['entry_narrative'] . "\n";
                if (!empty($t['notes'])) $md .= "- **Notes:** " . $t['notes'] . "\n";
                if (!empty($t['daily_log_url'])) $md .= "- **Daily Log URL:** " . $t['daily_log_url'] . "\n";

                if (!empty($t['screenshots'])) {
                    $md .= "- **Screenshots (" . count($t['screenshots']) . "):**\n";
                    foreach ($t['screenshots'] as $sc) {
                        $md .= "  - [" . ($sc['stage'] ? ucfirst($sc['stage']) : 'View') . "] " . $sc['url'] . ($sc['notes'] ? " (" . $sc['notes'] . ")" : "") . "\n";
                    }
                }
                $md .= "\n---\n\n";
            }

            if ($request->query('download') === 'true') {
                $filename = 'edgy_trades_export_' . date('Y_m_d_His') . '.md';
                return response($md, 200, [
                    'Content-Type' => 'text/markdown',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            }

            return response()->json(['markdown' => $md, 'json' => $exportData]);
        }

        $filename = 'edgy_trades_export_' . date('Y_m_d_His') . '.json';

        return response()->streamDownload(function() use ($exportData) {
            echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }, $filename, ['Content-Type' => 'application/json']);
    }
}
