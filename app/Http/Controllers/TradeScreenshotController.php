<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\TradeScreenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TradeScreenshotController extends Controller
{
    public function store(Request $request, Trade $trade)
    {
        //place in try catch
        try {
            $request->validate([
               // 'when' => 'required|in:before,during,after',
                'url' => 'required|url',
                'notes' => 'nullable|string',
            ]);
            TradeScreenshot::create([
                'trade_id' => $trade->id,
              //  'when' => $request->when,
                'url' => $request->url,
                'notes' => $request->notes,
            ]);

            return response()->json(['success' => true, 'message' => 'Screenshot added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => ['message' => $e->getMessage()]], 422);
        }
    }

    public function edit(Trade $trade, TradeScreenshot $screenshot)
    {
        return response()->json($screenshot);
    }

    // Update screenshot
    public function update(Request $request, Trade $trade, TradeScreenshot $screenshot)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'url' => 'required|url',
        ]);

        $screenshot->update([
            'url' => $request->url,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Screenshot updated successfully']);
    }

    public function destroy(TradeScreenshot $screenshot)
    {
        $screenshot->delete();
        return response()->json(['success' => true, 'message' => 'Screenshot deleted successfully.']);
    }
}
