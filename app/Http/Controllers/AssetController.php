<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('assetType')
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->get();
            
        $assetTypes = AssetType::all();
        
        return view('assets.index', compact('assets', 'assetTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'asset_type_id' => 'required|exists:asset_types,id',
        ]);

        Asset::create([
            'user_id' => Auth::id(),
            'asset_type_id' => $request->asset_type_id,
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return response()->json(['success' => true, 'message' => 'Asset created successfully']);
    }

    public function update(Request $request, Asset $asset)
    {
        if ($asset->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20',
            'asset_type_id' => 'required|exists:asset_types,id',
        ]);

        $asset->update([
            'name' => $request->name,
            'code' => $request->code,
            'asset_type_id' => $request->asset_type_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Asset updated successfully']);
    }

    public function destroy(Asset $asset)
    {
        if ($asset->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $asset->delete();

        return response()->json(['success' => true, 'message' => 'Asset deleted successfully']);
    }
}
