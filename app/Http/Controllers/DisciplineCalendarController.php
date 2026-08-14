<?php

namespace App\Http\Controllers;

use App\Models\DisciplineDailyAnalysis;
use App\Models\DisciplineDailyLog;
use App\Models\DisciplineMonthPlan;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DisciplineCalendarController extends Controller
{
    /**
     * Display the Commitment Board view.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $yearMonth = $request->query('month', date('Y-m'));

        try {
            $carbonMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
        } catch (\Exception $e) {
            $carbonMonth = Carbon::now()->startOfMonth();
            $yearMonth = $carbonMonth->format('Y-m');
        }

        $plan = $this->getOrCreateMonthPlan($userId, $yearMonth);

        $now = Carbon::now();
        $isCurrentMonth = ($yearMonth === $now->format('Y-m'));
        $dayOfMonth = (int) $now->format('j');
        
        $canEditLayout = false;
        if ($isCurrentMonth) {
            if ($dayOfMonth <= 5 || !$plan->is_locked || $yearMonth === '2026-08') {
                $canEditLayout = true;
            }
        }

        $monthData = $this->fetchMonthDetails($userId, $carbonMonth);

        return view('discipline.index', [
            'plan' => $plan,
            'dailyLogs' => $monthData['dailyLogs'],
            'tradesDaily' => $monthData['tradesDaily'],
            'canEditLayout' => $canEditLayout,
            'yearMonth' => $yearMonth,
            'carbonMonth' => $carbonMonth,
            'todayDate' => date('Y-m-d'),
        ]);
    }

    /**
     * AJAX Endpoint: Fetch month data for month navigation without page reload.
     */
    public function getMonthData(Request $request)
    {
        $userId = auth()->id();
        $yearMonth = $request->query('month', date('Y-m'));

        try {
            $carbonMonth = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
        } catch (\Exception $e) {
            $carbonMonth = Carbon::now()->startOfMonth();
            $yearMonth = $carbonMonth->format('Y-m');
        }

        $plan = $this->getOrCreateMonthPlan($userId, $yearMonth);
        $monthData = $this->fetchMonthDetails($userId, $carbonMonth);

        return response()->json([
            'yearMonth' => $yearMonth,
            'monthTitle' => strtoupper($carbonMonth->format('F Y')),
            'prevMonth' => $carbonMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $carbonMonth->copy()->addMonth()->format('Y-m'),
            'plan' => $plan,
            'dailyLogs' => $monthData['dailyLogs'],
            'tradesDaily' => $monthData['tradesDaily'],
            'todayDate' => date('Y-m-d'),
            'startOfMonth' => $carbonMonth->copy()->startOfMonth()->format('Y-m-d'),
            'firstDayOfWeek' => $carbonMonth->copy()->startOfMonth()->dayOfWeek,
            'daysInMonth' => $carbonMonth->daysInMonth,
        ]);
    }

    /**
     * Helper to retrieve or automatically seed a month plan from previous month.
     */
    private function getOrCreateMonthPlan($userId, $yearMonth)
    {
        $plan = DisciplineMonthPlan::where('user_id', $userId)
            ->where('year_month', $yearMonth)
            ->first();

        if (!$plan) {
            // Find most recent previous month plan to clone layout, background & quotes from
            $prevPlan = DisciplineMonthPlan::where('user_id', $userId)
                ->where('year_month', '<', $yearMonth)
                ->orderBy('year_month', 'desc')
                ->first();

            $plan = DisciplineMonthPlan::create([
                'user_id' => $userId,
                'year_month' => $yearMonth,
                'background_image' => $prevPlan ? $prevPlan->background_image : null,
                'layout_config' => $prevPlan ? $prevPlan->layout_config : $this->getDefaultLayoutConfig(),
                'monthly_activities' => $prevPlan ? $prevPlan->monthly_activities : $this->getDefaultActivities(),
                'is_locked' => false,
            ]);
        }

        return $plan;
    }

    /**
     * Save/update monthly plan layout, background, positions, quotes, fonts & activities.
     */
    public function savePlan(Request $request)
    {
        $request->validate([
            'year_month' => 'required|string|size:7',
            'background_image' => 'nullable|string',
            'layout_config' => 'nullable|array',
            'monthly_activities' => 'nullable|array',
            'is_locked' => 'nullable|boolean',
        ]);

        $userId = auth()->id();
        $yearMonth = $request->year_month;

        $plan = DisciplineMonthPlan::where('user_id', $userId)
            ->where('year_month', $yearMonth)
            ->firstOrFail();

        $existingConfig = $plan->layout_config ?? $this->getDefaultLayoutConfig();
        $newConfig = $request->layout_config ?? [];
        $mergedConfig = array_merge($existingConfig, $newConfig);

        $updateData = ['layout_config' => $mergedConfig];

        if ($request->has('background_image')) {
            $updateData['background_image'] = $request->background_image;
        }

        if ($request->has('monthly_activities')) {
            $updateData['monthly_activities'] = array_values(array_filter($request->monthly_activities));
        }

        if ($request->has('is_locked')) {
            $updateData['is_locked'] = $request->boolean('is_locked');
        }

        $plan->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Commitment Board saved successfully!',
            'plan' => $plan,
        ]);
    }

    /**
     * Upload an image file for board background or collages.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('discipline-collages', 'public');
            $url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /**
     * Get day details for modal (activities checklist, log, and trade stats).
     */
    public function getDayDetails($date)
    {
        $userId = auth()->id();
        $today = date('Y-m-d');
        $isToday = ($date === $today);
        $isPast = ($date < $today);

        $yearMonth = substr($date, 0, 7);
        $plan = DisciplineMonthPlan::where('user_id', $userId)
            ->where('year_month', $yearMonth)
            ->first();

        $activities = $plan ? ($plan->monthly_activities ?? []) : $this->getDefaultActivities();

        $dailyLog = DisciplineDailyLog::where('user_id', $userId)
            ->where('log_date', $date)
            ->first();

        $completedActivities = $dailyLog ? ($dailyLog->completed_activities ?? []) : [];
        $notes = $dailyLog ? $dailyLog->notes : '';

        $trades = Trade::with('associatedAsset')
            ->where('user_id', $userId)
            ->where('trade_date', $date)
            ->orderBy('created_at', 'asc')
            ->get();

        $tradeSummary = [
            'count' => $trades->count(),
            'total_pnl' => round($trades->sum('pnl'), 2),
            'won_trades' => $trades->where('outcome', 'win')->count(),
            'lost_trades' => $trades->where('outcome', 'loss')->count(),
            'plan_followed_count' => $trades->where('plan_followed', 'Yes')->count(),
        ];

        $formattedTrades = $trades->map(function ($t) {
            $emotions = [];
            if ($t->entry_pd_array) {
                $emotions = is_array($t->entry_pd_array) ? $t->entry_pd_array : json_decode($t->entry_pd_array, true) ?? [];
            } elseif ($t->emotions) {
                $emotions = [$t->emotions];
            }

            return [
                'id' => $t->id,
                'symbol' => $t->getAssetName(),
                'direction' => ucfirst($t->direction),
                'outcome' => $t->outcome,
                'pnl' => round($t->pnl, 2),
                'plan_followed' => $t->plan_followed ?? 'N/A',
                'emotions' => $emotions,
                'notes' => $t->notes,
            ];
        });

        return response()->json([
            'date' => $date,
            'formatted_date' => Carbon::parse($date)->format('l, F j, Y'),
            'is_today' => $isToday,
            'is_past' => $isPast,
            'can_edit_log' => $isToday,
            'activities' => $activities,
            'completed_activities' => $completedActivities,
            'notes' => $notes,
            'trade_summary' => $tradeSummary,
            'trades' => $formattedTrades,
        ]);
    }

    /**
     * Save daily execution log (Strictly allowed ONLY if log_date is TODAY).
     */
    public function saveDailyLog(Request $request)
    {
        $request->validate([
            'log_date' => 'required|date_format:Y-m-d',
            'completed_activities' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $userId = auth()->id();
        $logDate = $request->log_date;
        $todayStr = now()->format('Y-m-d');

        if ($logDate !== $todayStr) {
            return response()->json([
                'success' => false,
                'message' => 'Saving logs for previous or future dates is prohibited. Logs can only be updated for today.'
            ], 422);
        }
        
        $completed = $request->completed_activities ?? [];
        
        $yearMonth = substr($logDate, 0, 7);
        $plan = DisciplineMonthPlan::where('user_id', $userId)
            ->where('year_month', $yearMonth)
            ->first();
            
        $totalTasks = count($plan->monthly_activities ?? $this->getDefaultActivities());
        $score = $totalTasks > 0 ? round((count($completed) / $totalTasks) * 100) : 100;

        $log = DisciplineDailyLog::updateOrCreate(
            ['user_id' => $userId, 'log_date' => $logDate],
            [
                'completed_activities' => array_values($completed),
                'notes' => $request->notes,
                'score' => $score,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Daily discipline checklist & reflection notes saved!',
            'log' => $log,
        ]);
    }

    /**
     * Helper to fetch month daily logs and trade summaries.
     */
    private function fetchMonthDetails($userId, Carbon $carbonMonth)
    {
        $dailyLogs = DisciplineDailyLog::where('user_id', $userId)
            ->whereYear('log_date', $carbonMonth->year)
            ->whereMonth('log_date', $carbonMonth->month)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->log_date)->format('Y-m-d');
            });

        $tradesDaily = Trade::where('user_id', $userId)
            ->whereYear('trade_date', $carbonMonth->year)
            ->whereMonth('trade_date', $carbonMonth->month)
            ->selectRaw('
                trade_date as date,
                SUM(pnl) as pnl,
                COUNT(id) as total_trades,
                SUM(CASE WHEN outcome = "win" THEN 1 ELSE 0 END) as won_trades,
                SUM(CASE WHEN outcome = "loss" THEN 1 ELSE 0 END) as lost_trades,
                SUM(CASE WHEN plan_followed = "Yes" THEN 1 ELSE 0 END) as plan_followed_count
            ')
            ->groupBy('trade_date')
            ->get()
            ->keyBy(function ($row) {
                return Carbon::parse($row->date)->format('Y-m-d');
            });

        return [
            'dailyLogs' => $dailyLogs,
            'tradesDaily' => $tradesDaily,
        ];
    }

    /**
     * Default layout configuration matching user preferences.
     */
    private function getDefaultLayoutConfig()
    {
        return [
            'grid_template' => 'hero_top',
            'bg_opacity' => 0.40,
            'font_family' => 'Outfit',
            'quote_top' => "First you create the process. Then the process creates you. Eventually, you and the process become one.",
            'quote_top_font' => 'Outfit',
            'banner_left_title' => "You're the GREATEST",
            'banner_left_body' => "Greatness begins as a belief before it becomes a reality. You must see it, believe it, and commit yourself to becoming it.",
            'quote_box' => "Talent may open doors, but discipline, commitment, and relentless work are what keep you in the room",
            'quote_box_font' => 'Courier Prime',
            'sticky_title' => "IN GOD WE TRUST",
            'sticky_signature' => "Segolame Timothy Bush Monyake",
            'sticky_font' => 'Caveat',
            'collage_img_1' => asset('deskapp/vendors/images/photo1.jpg'),
            'collage_img_2' => asset('deskapp/vendors/images/photo2.jpg'),
            'collage_img_3' => asset('deskapp/vendors/images/photo3.jpg'),
            'collage_img_4' => asset('deskapp/vendors/images/photo4.jpg'),
            'widget_positions' => [],
            'custom_widgets' => [],
        ];
    }

    /**
     * Default monthly activities / discipline routine.
     */
    private function getDefaultActivities()
    {
        return [
            "Early Morning Read / Mindset Prep",
            "Physical Exercise & Hydration",
            "Followed Trading Plan Executions",
            "No Revenge or Impulse Trading",
            "Evening Trade Review & Journaling",
        ];
    }

    /* ═══════════════════════════════════════════════════════
     *  DAILY ANALYSIS JOURNAL  — Pre / Market / Post-market
     * ═══════════════════════════════════════════════════════ */

    /**
     * GET  /commitment-board/analysis/{date}
     * Returns all analysis entries for a given date, grouped by phase.
     */
    public function getAnalyses($date)
    {
        $userId = auth()->id();
        $entries = DisciplineDailyAnalysis::where('user_id', $userId)
            ->where('analysis_date', $date)
            ->orderBy('phase')
            ->orderBy('sort_order')
            ->get();

        // Resolve Forex Factory week URL pointing to the preceding Sunday
        $carbon = Carbon::parse($date);
        $sunday = $carbon->copy()->startOfWeek(Carbon::SUNDAY);
        $ffUrl  = 'https://www.forexfactory.com/calendar?week=' .
                  strtolower($sunday->format('M')) . $sunday->format('j') . '.' . $sunday->format('Y');

        $entries->transform(function($e) {
            $e->time_formatted = $e->created_at ? $e->created_at->format('H:i') : '';
            return $e;
        });

        $firstDoc = $entries->first()?->narrative ?? '';

        return response()->json([
            'date'          => $date,
            'formatted'     => $carbon->format('l, F j, Y'),
            'forex_ff_url'  => $ffUrl,
            'entries'       => $entries,
            'document_html' => $firstDoc,
            'grouped'       => [
                'premarket'   => $entries->where('phase', 'premarket')->values(),
                'market'      => $entries->where('phase', 'market')->values(),
                'postmarket'  => $entries->where('phase', 'postmarket')->values(),
            ],
        ]);
    }

    /**
     * POST /commitment-board/analysis/save-doc
     * Save full Notion-style document content for a date.
     */
    public function saveDocument(Request $request)
    {
        $request->validate([
            'analysis_date' => 'required|date_format:Y-m-d',
            'html_content'  => 'nullable|string',
        ]);

        $userId = auth()->id();
        $entry  = DisciplineDailyAnalysis::where('user_id', $userId)
            ->where('analysis_date', $request->analysis_date)
            ->first();

        if ($entry) {
            $entry->update(['narrative' => $request->html_content ?? '']);
        } else {
            $entry = DisciplineDailyAnalysis::create([
                'user_id'       => $userId,
                'analysis_date' => $request->analysis_date,
                'phase'         => 'market',
                'narrative'     => $request->html_content ?? '',
                'sort_order'    => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Journal document saved.',
            'entry'   => $entry,
        ]);
    }

    /**
     * POST /commitment-board/analysis
     * Create a new chart entry for a date + phase.
     */
    public function storeAnalysis(Request $request)
    {
        $request->validate([
            'analysis_date' => 'required|date_format:Y-m-d',
            'phase'         => 'nullable|in:premarket,market,postmarket',
            'chart_url'     => 'nullable|url',
            'timeframe'     => 'nullable|string|max:10',
            'symbol'        => 'nullable|string|max:20',
            'bias'          => 'nullable|in:bullish,bearish,neutral',
            'key_levels'    => 'nullable|string',
            'narrative'     => 'nullable|string',
        ]);

        $userId   = auth()->id();
        $phase    = $request->phase ?? 'market';
        $chartUrl = $request->chart_url;
        $previewUrl = DisciplineDailyAnalysis::resolveTradingViewPreviewUrl($chartUrl);

        $maxOrder = DisciplineDailyAnalysis::where('user_id', $userId)
            ->where('analysis_date', $request->analysis_date)
            ->where('phase', $phase)
            ->max('sort_order') ?? -1;

        $keyLevels = $request->key_levels
            ? array_filter(array_map('trim', explode(',', $request->key_levels)))
            : [];

        $entry = DisciplineDailyAnalysis::create([
            'user_id'           => $userId,
            'analysis_date'     => $request->analysis_date,
            'phase'             => $phase,
            'chart_url'         => $chartUrl,
            'chart_preview_url' => $previewUrl,
            'timeframe'         => $request->timeframe,
            'symbol'            => $request->symbol ? strtoupper($request->symbol) : null,
            'bias'              => $request->bias,
            'key_levels'        => array_values($keyLevels),
            'narrative'         => $request->narrative,
            'sort_order'        => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Analysis entry saved.',
            'entry'   => $entry,
        ]);
    }

    /**
     * PUT  /commitment-board/analysis/{analysis}
     * Update an existing chart entry.
     */
    public function updateAnalysis(Request $request, DisciplineDailyAnalysis $analysis)
    {
        if ($analysis->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'chart_url'  => 'nullable|url',
            'timeframe'  => 'nullable|string|max:10',
            'symbol'     => 'nullable|string|max:20',
            'bias'       => 'nullable|in:bullish,bearish,neutral',
            'key_levels' => 'nullable|string',
            'narrative'  => 'nullable|string',
        ]);

        $chartUrl   = $request->chart_url;
        $previewUrl = DisciplineDailyAnalysis::resolveTradingViewPreviewUrl($chartUrl);

        $keyLevels = $request->key_levels
            ? array_filter(array_map('trim', explode(',', $request->key_levels)))
            : [];

        $analysis->update([
            'chart_url'         => $chartUrl,
            'chart_preview_url' => $previewUrl,
            'timeframe'         => $request->timeframe,
            'symbol'            => $request->symbol ? strtoupper($request->symbol) : null,
            'bias'              => $request->bias,
            'key_levels'        => array_values($keyLevels),
            'narrative'         => $request->narrative,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Analysis entry updated.',
            'entry'   => $analysis->fresh(),
        ]);
    }

    /**
     * DELETE /commitment-board/analysis/{analysis}
     */
    public function deleteAnalysis(DisciplineDailyAnalysis $analysis)
    {
        if ($analysis->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $analysis->delete();
        return response()->json(['success' => true, 'message' => 'Entry deleted.']);
    }

    /**
     * GET  /commitment-board/analysis/{date}/page
     * Full-page Daily Analysis Journal view (can be opened in new tab / fullscreen).
     */
    public function analysisPage($date)
    {
        $userId = auth()->id();

        try { $carbon = Carbon::parse($date); }
        catch (\Exception $e) { abort(404); }

        $entries = DisciplineDailyAnalysis::where('user_id', $userId)
            ->where('analysis_date', $date)
            ->orderBy('phase')->orderBy('sort_order')
            ->get();

        $sunday = $carbon->copy()->startOfWeek(Carbon::SUNDAY);
        $ffUrl  = 'https://www.forexfactory.com/calendar?week=' .
                  strtolower($sunday->format('M')) . $sunday->format('j') . '.' . $sunday->format('Y');

        $plan = DisciplineMonthPlan::where('user_id', $userId)
            ->where('year_month', $carbon->format('Y-m'))
            ->first();

        $activities = $plan ? ($plan->monthly_activities ?? $this->getDefaultActivities()) : $this->getDefaultActivities();

        $dailyLog = DisciplineDailyLog::where('user_id', $userId)
            ->where('log_date', $date)
            ->first();

        $completedActivities = $dailyLog ? ($dailyLog->completed_activities ?? []) : [];
        $dailyNotes = $dailyLog ? ($dailyLog->notes ?? '') : '';

        $todayStr = now()->format('Y-m-d');
        $isToday  = ($date === $todayStr);

        return view('discipline.analysis_page', [
            'date'                => $date,
            'carbon'              => $carbon,
            'isToday'             => $isToday,
            'entries'             => $entries,
            'grouped'             => [
                'premarket'  => $entries->where('phase', 'premarket')->values(),
                'market'     => $entries->where('phase', 'market')->values(),
                'postmarket' => $entries->where('phase', 'postmarket')->values(),
            ],
            'ffUrl'               => $ffUrl,
            'plan'                => $plan,
            'activities'          => $activities,
            'completedActivities' => $completedActivities,
            'dailyNotes'          => $dailyNotes,
            'dailyLog'            => $dailyLog,
        ]);
    }
}
