<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TradeController extends Controller
{
    // Show trades list
    public function index()
    {
        return view('trades.index');
    }

    // Data for DataTables
    public function getTrades(Request $request)
    {
        $trades = Trade::query();


        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = \Carbon\Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
            $end = \Carbon\Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
            $trades->whereBetween('trade_date', [$start, $end]);
        }

        if ($request->filled('market') && $request->market != '0') {
            $trades->where('asset', $request->market);
        }

        if ($request->filled('direction') && $request->direction != '0') {
            $trades->where('direction', $request->direction);
        }

        
        if ($request->filled('session') && $request->session != '0') {
            $trades->where('session', $request->session);
        }

        
       /*  if ($request->filled('outcome') && $request->outcome != '0') {
            $trades->where('outcome', $request->outcome);
        } */

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

        return DataTables::of($trades)
            // ->addColumn('asset', fn($row) => $row->asset ?? '-') // placeholder
            ->addIndexColumn()
            ->addColumn('asset', fn($row) => strtoupper($row->asset))
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

                // ...existing code...
                if ($row->outcome === 'pending') {
                    return '<span class="badge badge-warning">PENDING</span>';
                } else if ($row->outcome === 'win') {
                    return '<span class="badge badge-success">WIN</span>';
                } else if ($row->outcome === 'loss') {
                    return '<span class="badge badge-danger">LOSS</span>';
                } else {
                    return '<span class="badge badge-secondary">BREAKEVEN</span>';
                }
                // ...existing code...

            })->addColumn('hin_day', function ($row) {

                // ...existing code...
                if ($row->hin_day == true) {
                    return '<span class="badge badge-success">YES</span>';;
                } else {
                    return '<span class="badge badge-danger">NO</span>';
                }
                // ...existing code...

            })
            ->editColumn('status', function ($row) {

                // ...existing code...
                if ($row->status === 'open') {
                    return '<span class="badge badge-success">OPEN</span>';;
                } else {
                    return '<span class="badge badge-danger">CLOSED</span>';
                }
                // ...existing code...

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
            ->addColumn('trade_screenshots',function($row){
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
            ->rawColumns(['actions','hin_day', 'screenshots', 'status','trade_status', 'entry_pd_array', 'plan_followed', 'direction', 'outcome', 'rr', 'session', 'asset'])
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
            $trade = Trade::create($request->all());
            return response()->json(['success' => true, 'message' => 'Trade sucessfully saved!']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    // Update trade
    public function update(Request $request, Trade $trade)
    {
        try {
            $trade->update($request->all());
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
}
