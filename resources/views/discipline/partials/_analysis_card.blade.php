@php
    $previewUrl = $entry->chart_preview_url;
    $chartUrl   = $entry->chart_url;
    $timeStr    = $entry->created_at ? $entry->created_at->format('H:i') : '';
@endphp

<div class="entry-card" id="card-{{ $entry->id }}">
    <div class="entry-meta">
        <div class="entry-time-tag">
            @if($timeStr)
                <span class="time-badge">⏰ {{ $timeStr }}</span>
            @endif
        </div>
        <div class="entry-actions">
            <button class="entry-act-btn" onclick='openEditModal(@json($entry))' title="Edit">Edit</button>
            <button class="entry-act-btn del" onclick="deleteEntry({{ $entry->id }})" title="Delete">✕</button>
        </div>
    </div>

    <div class="entry-chart-wrap" style="position: relative;">
        @if($previewUrl)
            @if($timeStr)
                <div class="chart-corner-time">{{ $timeStr }}</div>
            @endif
            <img class="entry-chart-img" src="{{ $previewUrl }}" alt="Chart"
                 onclick="openLightbox('{{ $previewUrl }}')"
                 onerror="this.parentElement.innerHTML='<div class=\'entry-chart-placeholder\'><svg width=48 height=48 viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1\'><polyline points=\'22 12 18 12 15 21 9 3 6 12 2 12\'/></svg><span>Chart unavailable</span></div>'">
            @if($chartUrl)
                <a href="{{ $chartUrl }}" target="_blank" class="entry-open-link">Open in TradingView ↗</a>
            @endif
        @else
            <div class="entry-chart-placeholder">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                <span>{{ $chartUrl ? 'No preview' : 'No chart attached' }}</span>
                @if($chartUrl)
                    <a href="{{ $chartUrl }}" target="_blank" style="color:#60a5fa;font-size:12px;">Open chart ↗</a>
                @endif
            </div>
        @endif
    </div>

    @if($entry->narrative)
        <div class="entry-narrative" id="nar-{{ $entry->id }}">{!! strip_tags($entry->narrative, '<h1><h2><h3><h4><b><i><u><strong><em><ul><ol><li><p><br><div><span>') !!}</div>
        @if(strlen(strip_tags($entry->narrative)) > 180)
            <button class="read-more-btn">Read more</button>
        @endif
    @endif

    @if(!empty($entry->key_levels))
        <div class="entry-levels">
            @foreach($entry->key_levels as $lvl)
                <span class="level-pill">{{ $lvl }}</span>
            @endforeach
        </div>
    @endif
</div>
