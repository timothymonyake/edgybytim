<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reminder;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('reminders.index', compact('reminders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'remind_at' => 'nullable',
            'frequency' => 'required|in:once,daily,weekly,custom,custom_days',
            'recurrence_days' => 'nullable|array',
            'daily_time' => 'nullable|string',
            'expires_at' => 'nullable',
        ]);

        $frequency = ($validated['frequency'] === 'custom_days') ? 'custom' : $validated['frequency'];
        $recurrenceDays = is_array($validated['recurrence_days'] ?? null) ? $validated['recurrence_days'] : [];

        if ($frequency === 'daily') {
            if (!empty($request->daily_time) && !in_array($request->daily_time, $recurrenceDays)) {
                $recurrenceDays[] = $request->daily_time;
            }
            $recurrenceDays = !empty($recurrenceDays) ? array_values(array_unique(array_filter($recurrenceDays))) : null;
        } elseif ($validated['frequency'] === 'custom' && empty($recurrenceDays)) {
            // Trading weekdays Mon-Fri
            $recurrenceDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        } else {
            $recurrenceDays = !empty($recurrenceDays) ? array_values(array_unique(array_filter($recurrenceDays))) : null;
        }

        $remindAt = null;
        if (!empty($validated['remind_at'])) {
            try { $remindAt = \Carbon\Carbon::parse($validated['remind_at']); } catch (\Exception $e) {}
        }

        $expiresAt = null;
        if (!empty($validated['expires_at'])) {
            try { $expiresAt = \Carbon\Carbon::parse($validated['expires_at']); } catch (\Exception $e) {}
        }

        Reminder::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'] ?? '',
            'remind_at' => $remindAt,
            'frequency' => $frequency,
            'recurrence_days' => $recurrenceDays,
            'is_active' => true,
            'expires_at' => $expiresAt,
        ]);

        return back()->with('success', 'Reminder saved.');
    }

    public function update(Request $request, Reminder $reminder)
    {
        $this->authorize('update', $reminder);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'remind_at' => 'nullable|date',
            'frequency' => 'required|in:once,daily,weekly,custom',
            'recurrence_days' => 'nullable|array',
            'is_active' => 'required|boolean',
            'expires_at' => 'nullable|date',
        ]);

        $reminder->update([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? '',
            'remind_at' => $validated['remind_at'] ?? null,
            'frequency' => $validated['frequency'],
            'recurrence_days' => $validated['recurrence_days'] ?? null,
            'is_active' => $validated['is_active'],
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return back()->with('success', 'Reminder updated.');
    }

    public function destroy(Reminder $reminder)
    {
        $this->authorize('delete', $reminder);
        $reminder->delete();
        return back()->with('success', 'Reminder deleted.');
    }

    public function upcoming()
    {
        $now = now();
        $buffer = now()->addMinutes(2);

        $reminders = Reminder::where('user_id', auth()->id())
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', $now);
            })
            ->where(function ($query) use ($now, $buffer) {
                // Return one-off reminders due soon OR all active recurring reminders
                $query->whereBetween('remind_at', [$now->copy()->subMinutes(5), $buffer])
                    ->orWhere('frequency', '!=', 'once');
            })
            ->get();

        return response()->json($reminders);
    }

    public function notified(Reminder $reminder)
    {
        $this->authorize('update', $reminder);

        $reminder->update([
            'last_reminded_at' => now()
        ]);

        return response()->json(['success' => true]);
    }
}
