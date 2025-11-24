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
        if ($trade->user_id !== auth()->id()) {
            return response()->json(['error' => ['message' => 'Unauthorized']], 403);
        }

        try {
            $request->validate([
                'url' => 'required|url',
                'notes' => 'nullable|string',
            ]);
            TradeScreenshot::create([
                'trade_id' => $trade->id,
                'url' => $request->url,
                'notes' => $request->notes,
            ]);

            return response()->json(['success' => true, 'message' => 'Screenshot added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => ['message' => $e->getMessage()]], 422);
        }
    }

    public function edit(TradeScreenshot $screenshot)
    {
        if ($screenshot->trade->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($screenshot);
    }

    // Update screenshot
    public function update(Request $request, TradeScreenshot $screenshot)
    {
        if ($screenshot->trade->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        if ($screenshot->trade->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $screenshot->delete();
        return response()->json(['success' => true, 'message' => 'Screenshot deleted successfully.']);
    }
}
