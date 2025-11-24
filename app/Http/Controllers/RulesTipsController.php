<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rule;
use App\Models\PsychologicalTip;
use App\Models\EntryChecklist;

use Illuminate\Support\Facades\Auth;

class RulesTipsController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $rules = Rule::where('user_id', $user_id)->get();
        $tips = PsychologicalTip::where('user_id', $user_id)->get();
        $checklists = EntryChecklist::where('user_id', $user_id)->get();
        return view('rules_tips.index', compact('rules', 'tips', 'checklists'));
    }

    // ... (existing Rule and Tip methods) ...

    public function storeChecklist(Request $request)
    {
        try {
            $request->validate(['content' => 'required']);
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $checklist = EntryChecklist::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Checklist item added successfully',
                'data' => $checklist
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add checklist item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateChecklist(Request $request, EntryChecklist $checklist)
    {
        try {
            $request->validate(['content' => 'required']);
            $checklist->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Checklist item updated successfully',
                'data' => $checklist
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update checklist item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyChecklist(EntryChecklist $checklist)
    {
        try {
            $checklist->delete();
            return response()->json([
                'success' => true,
                'message' => 'Checklist item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete checklist item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeRule(Request $request)
    {
        try {
            $request->validate(['content' => 'required']);
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $rule = Rule::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Rule added successfully',
                'data' => $rule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add rule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeTip(Request $request)
    {
        try {
            $request->validate(['content' => 'required']);
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $tip = PsychologicalTip::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Tip added successfully',
                'data' => $tip
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add tip: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRule(Request $request, Rule $rule)
    {
        try {
            $request->validate(['content' => 'required']);
            $rule->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Rule updated successfully',
                'data' => $rule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update rule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateTip(Request $request, PsychologicalTip $tip)
    {
        try {
            $request->validate(['content' => 'required']);
            $tip->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Tip updated successfully',
                'data' => $tip
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tip: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyRule(Rule $rule)
    {
        try {
            $rule->delete();
            return response()->json([
                'success' => true,
                'message' => 'Rule deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete rule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyTip(PsychologicalTip $tip)
    {
        try {
            $tip->delete();
            return response()->json([
                'success' => true,
                'message' => 'Tip deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tip: ' . $e->getMessage()
            ], 500);
        }
    }
}
