@php
    $startOfMonth = $carbonMonth->copy()->startOfMonth();
    $endOfMonth = $carbonMonth->copy()->endOfMonth();
    $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 for Sun
    $daysInMonth = $carbonMonth->daysInMonth;

    $currentDateIter = $startOfMonth->copy()->subDays($firstDayOfWeek);
    $totalCells = ceil(($firstDayOfWeek + $daysInMonth) / 7) * 7;
    $activitiesCount = count($plan->monthly_activities ?? []);
@endphp

@for ($i = 0; $i < $totalCells; $i++)
    @if ($i % 7 == 0)
        <tr>
    @endif

    @php
        $dateStr = $currentDateIter->format('Y-m-d');
        $isCurrentMonthDay = ($currentDateIter->month == $carbonMonth->month);
        $isToday = ($dateStr == $todayDate);

        $log = $dailyLogs[$dateStr] ?? null;
        $completedCount = $log ? count($log->completed_activities ?? []) : 0;
        $tradeRow = $tradesDaily[$dateStr] ?? null;
    @endphp

    <td class="day-tile {{ !$isCurrentMonthDay ? 'other-month' : '' }} {{ $isToday ? 'is-today' : '' }}"
        onclick="openDayModal('{{ $dateStr }}')">
        <div class="d-flex justify-content-between align-items-center">
            <span class="day-number">{{ $currentDateIter->format('d') }}</span>
            @if($log && $completedCount > 0)
                <span class="discipline-progress-ring {{ $completedCount >= $activitiesCount ? 'ring-complete' : 'ring-partial' }}">
                    {{ $completedCount }}/{{ $activitiesCount }}
                </span>
            @endif
        </div>

        @if($activitiesCount > 0)
            <div class="discipline-bar-mini">
                <div class="discipline-bar-fill" style="width: {{ $activitiesCount > 0 ? round(($completedCount / $activitiesCount) * 100) : 0 }}%;"></div>
            </div>
        @endif

        @if($tradeRow)
            @php
                $pnl = $tradeRow->pnl;
                $pnlFormatted = ($pnl >= 0 ? '+$' : '-$') . number_format(abs($pnl), 0);
            @endphp
            <span class="trade-tag-subtle">
                {{ $pnlFormatted }} ({{ $tradeRow->total_trades }}T)
            </span>
        @endif
    </td>

    @php
        $currentDateIter->addDay();
    @endphp

    @if ($i % 7 == 6)
        </tr>
    @endif
@endfor
