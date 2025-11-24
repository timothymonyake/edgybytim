<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reminder;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::where('user_id', auth()->id())->get();
        return view('reminders.index', compact('reminders'));
    }

    public function store(Request $request)
    {
        Reminder::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Reminder saved.');
    }

    public function destroy(Reminder $reminder)
    {
        $reminder->delete();
        return back()->with('success', 'Reminder deleted.');
    }
}
