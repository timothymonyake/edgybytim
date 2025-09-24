<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;

class CalendarController extends Controller
{
    public function index()
    {
        return view('trades.calendar');
    }


    public function events(Request $request)
    {
        $trades = Trade::selectRaw('trade_date, SUM(pips) as pnl, COUNT(*) as trades')
            ->groupBy('trade_date')
            ->where('outcome', '!=', 'pending')
            ->where('status', 'closed')
            //->where('pnl', '!=', )
            ->get();

        $events = $trades->map(function ($trade) {
            return [
                'title' => $trade->pnl . ' pips / ' . $trade->trades . ' trades',
                'start' => $trade->trade_date,
                'color' => $trade->outcome == 0 ? '#2ecc71' : '#e74c3c',
                'extendedProps' => [
                    'pnl' => $trade->pnl,
                    'trades' => $trade->trades,
                ]
            ];
        });

        return response()->json($events);
    }

}
