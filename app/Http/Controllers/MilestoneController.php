<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    public function index()
    {
        $milestones = Milestone::where('user_id', Auth::id())->orderBy('date', 'desc')->get();
        return view('milestones.index', compact('milestones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'img_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text' => 'required|string',
        ]);

        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();

            if ($request->hasFile('img_path')) {
                $path = $request->file('img_path')->store('milestones', 'public');
                $data['img_path'] = asset('storage/' . $path);
            }

            Milestone::create($data);

            return response()->json(['success' => true, 'message' => 'Milestone added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Milestone $milestone)
    {
        if ($milestone->user_id !== Auth::id()) {
            abort(403);
        }

        $milestone->delete();

        return redirect()->route('milestones.index')->with('success', 'Milestone deleted successfully!');
    }
}
