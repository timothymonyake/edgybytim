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
        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $path = $file->store('screenshots', 'public');

                TradeScreenshot::create([
                    'trade_id' => $trade->id,
                    'type' => $request->type ?? 'other',
                    'file_path' => $path,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy(TradeScreenshot $screenshot)
    {
        Storage::disk('public')->delete($screenshot->file_path);
        $screenshot->delete();
        return response()->json(['success' => true]);
    }
}
