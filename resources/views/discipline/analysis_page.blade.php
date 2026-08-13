@extends('layouts.app')
@section('title', 'Daily Journal – ' . $carbon->format('M j, Y'))

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;600;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
    --bg:    #0b0d12;
    --card:  #121620;
    --card2: #1a202c;
    --border:#232d3f;
    --text:  #e2e8f0;
    --muted: #64748b;
}

html, body {
    height: 100%; margin: 0; padding: 0;
    overflow: hidden; /* Prevent window scrollbar */
    background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif;
}

/* ── 1. NON-SCROLLABLE FIXED TOP SECTION ── */
.fixed-top-header {
    position: fixed; top: 0; left: 0; right: 0; z-index: 1000; height: 116px;
    background: #0b0d12; border-bottom: 1px solid var(--border);
    padding: 12px 24px 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.6);
    box-sizing: border-box;
}

.aj-topbar {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-bottom: 8px;
}
.aj-topbar-left { display: flex; align-items: center; gap: 14px; }
.aj-date-badge {
    font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 20px;
    color: #fff; letter-spacing: -0.5px;
}

.date-nav-group {
    display: flex; align-items: center; gap: 6px;
    background: #121620; padding: 2px 8px; border-radius: 10px; border: 1px solid var(--border);
}
.date-nav-btn {
    font-size: 15px; font-weight: 800; padding: 2px 8px; line-height: 1; color: #94a3b8;
    background: transparent; border: none; cursor: pointer; text-decoration: none; transition: color .15s;
}
.date-nav-btn:hover { color: #38bdf8; }

.aj-ff-pill {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(251,191,36,.12); border: 1px solid rgba(251,191,36,.3);
    color: #fbbf24; font-size: 11px; font-weight: 600;
    padding: 3px 10px; border-radius: 20px; text-decoration: none;
    transition: all .2s;
}
.aj-ff-pill:hover { background: rgba(251,191,36,.22); color: #fde68a; }
.aj-topbar-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.aj-btn {
    font-family: 'Inter', sans-serif; font-weight: 600; font-size: 12px;
    padding: 5px 12px; border-radius: 8px; border: 1px solid var(--border);
    background: var(--card2); color: var(--text); cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; gap: 6px;
}
.aj-btn:hover { border-color: #475569; background: #222b3a; }
.aj-btn-primary { background: #1b00ff; border-color: #1b00ff; color: #fff; }
.aj-btn-primary:hover { background: #1400d4; }

/* Notion Document Toolbar */
.doc-toolbar {
    background: rgba(18,22,32,0.95); backdrop-filter: blur(10px);
    border: 1px solid var(--border); border-radius: 9px;
    padding: 4px 10px; display: flex; align-items: center; justify-content: space-between;
    gap: 8px; flex-wrap: wrap;
}
.toolbar-group { display: flex; align-items: center; gap: 4px; }
.t-btn {
    background: transparent; border: 1px solid transparent; color: #94a3b8;
    font-size: 12px; font-weight: 700; padding: 4px 9px; border-radius: 5px;
    cursor: pointer; transition: all .15s; display: inline-flex; align-items: center; gap: 4px;
}
.t-btn:hover { background: #1e293b; color: #fff; border-color: #334155; }
.t-btn.heading-btn { font-family: 'Outfit', sans-serif; font-size: 12.5px; text-decoration: underline; font-weight: 800; text-transform: uppercase; }

.save-status-badge {
    font-size: 11.5px; font-weight: 600; color: #10b981; display: inline-flex; align-items: center; gap: 4px;
    font-family: 'JetBrains Mono', monospace;
}

/* ── 2. FIXED WORKSPACE CONTAINER & SCROLLABLE CANVAS ── */
.fixed-workspace-container {
    margin-top: 116px;
    height: calc(100vh - 116px);
    overflow: hidden; /* Outer window never scrolls! */
    padding: 16px 20px 20px;
    box-sizing: border-box;
}

.workspace-grid {
    display: grid;
    grid-template-columns: 260px 1fr 260px;
    gap: 18px;
    height: calc(100vh - 152px); /* Explicit height ensures inner overflow-y works! */
    align-items: stretch;
    max-width: 1560px;
    margin: 0 auto;
}

/* ── MIDDLE COLUMN WRAPPER & NOTION CANVAS ── */
.paper-wrapper-relative {
    position: relative;
    height: 100%;
    min-height: 0;
    overflow: visible;
}

.notion-paper {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 16px; padding: 32px 42px;
    height: 100%; max-height: calc(100vh - 152px);
    overflow-y: auto !important; /* CRITICAL: Enables scrolling on notionPaper! */
    box-shadow: 0 16px 40px rgba(0,0,0,0.35); outline: none;
    font-size: 15px; line-height: 1.75; color: #e2e8f0;
    box-sizing: border-box;
}
.notion-paper:focus { border-color: #3b82f6; }

/* FULLSCREEN OVERLAY MODE FOR NOTION PAPER ONLY */
.notion-paper.fullscreen-mode {
    position: fixed !important; inset: 0 !important; z-index: 999998 !important;
    width: 100vw !important; height: 100vh !important; max-width: 100vw !important;
    max-height: 100vh !important; border-radius: 0 !important; padding: 50px 120px !important;
    background: #0b0d12 !important; border: none !important; overflow-y: auto !important;
}

.fullscreen-exit-btn {
    position: fixed; top: 20px; right: 30px; z-index: 999999;
    background: rgba(255,255,255,0.1); color: #fff; border: 1px solid #334155;
    padding: 8px 18px; border-radius: 8px; font-weight: 700; font-size: 13px;
    cursor: pointer; backdrop-filter: blur(10px); display: none; transition: all 0.2s;
}
.fullscreen-exit-btn:hover { background: #ef4444; border-color: #ef4444; }

/* Notion Typography inside Document */
.notion-paper h1 {
    font-family: 'Outfit', sans-serif; font-size: 25px; font-weight: 800;
    color: #ffffff; margin: 24px 0 12px; letter-spacing: -0.5px;
}
.notion-paper h2 {
    font-family: 'Outfit', sans-serif; font-size: 18.5px; font-weight: 800;
    color: #ffffff; text-transform: uppercase; text-decoration: underline;
    text-underline-offset: 6px; text-decoration-color: #f43f5e;
    margin: 28px 0 14px; letter-spacing: 0.5px;
}
.notion-paper p { margin-bottom: 16px; color: #cbd5e1; }
.notion-paper ul { margin-bottom: 16px; padding-left: 24px; color: #cbd5e1; }
.notion-paper li { margin-bottom: 4px; }
.notion-paper u { text-decoration-color: #f43f5e; text-underline-offset: 4px; }

/* ── LEFT TIMELINE VIEW FOR CHART ENTRIES ── */
.tv-timeline-entry {
    display: flex; gap: 16px; margin: 24px 0; align-items: stretch; position: relative;
}
.timeline-left-gutter {
    display: flex; flex-direction: column; align-items: center; width: 68px; flex-shrink: 0;
}
.timeline-time-tag {
    font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 800;
    color: #38bdf8; background: #0f172a; border: 1px solid rgba(56,189,248,0.35);
    padding: 3px 6px; border-radius: 6px; text-align: center; white-space: nowrap;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
.timeline-v-line {
    width: 2px; background: linear-gradient(180deg, rgba(56,189,248,0.4) 0%, rgba(35,45,63,0.3) 100%);
    flex-grow: 1; margin-top: 6px; border-radius: 2px;
}
.timeline-body-content {
    flex-grow: 1; min-width: 0;
}

/* Embedded TradingView Chart Block */
.tv-embed-block {
    position: relative; border-radius: 12px; overflow: hidden;
    background: #090b0e; border: 1px solid var(--border); box-shadow: 0 8px 24px rgba(0,0,0,0.3);
}
.tv-embed-img {
    width: 100%; max-height: 600px; object-fit: contain; background: #000;
    display: block; cursor: pointer; transition: opacity .2s;
}
.tv-embed-img:hover { opacity: 0.95; }
.tv-open-link {
    position: absolute; bottom: 10px; right: 10px; z-index: 5;
    background: rgba(15,23,42,0.85); color: #e2e8f0; font-size: 10.5px; font-weight: 600;
    padding: 4px 10px; border-radius: 5px; text-decoration: none;
    backdrop-filter: blur(4px); border: 1px solid #334155; transition: all .2s;
}
.tv-open-link:hover { background: #1b00ff; color: #fff; border-color: #1b00ff; }

/* Greyed-out Image Caption */
.tv-caption {
    font-size: 13px; font-style: italic; color: #94a3b8; text-align: center;
    margin-top: 8px; padding: 6px 12px; border-radius: 6px; outline: none;
    transition: all 0.2s; min-height: 24px;
}
.tv-caption:focus, .tv-caption:hover {
    background: rgba(255,255,255,0.04); color: #cbd5e1;
}

/* ── SIDEBAR PANELS ── */
.sidebar-panel {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 16px; padding: 18px; height: 100%; overflow-y: auto;
    box-shadow: 0 16px 40px rgba(0,0,0,0.35); box-sizing: border-box;
}
.sidebar-section-title {
    font-family: 'Outfit', sans-serif; font-size: 13.5px; font-weight: 800;
    color: #fff; text-transform: uppercase; letter-spacing: 0.5px;
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.06);
}
.progress-pill {
    font-size: 10.5px; font-weight: 700; color: #10b981;
    background: rgba(16,185,129,0.12); padding: 2px 7px; border-radius: 10px;
}

/* ── FLOATING OUTLINE NAV BAR (ON RIGHT INTERIOR EDGE OF NOTION PAPER) ── */
.floating-outline-nav {
    position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
    z-index: 990; background: rgba(15, 20, 32, 0.94); backdrop-filter: blur(12px);
    border: 1px solid #334155; border-radius: 20px; padding: 12px 6px;
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.6); max-height: 380px; overflow-y: auto;
}
.outline-line-node {
    height: 4px; border-radius: 4px; background: #334155; cursor: pointer;
    transition: all 0.2s ease; position: relative;
}
.outline-line-node.level-h1 { width: 24px; background: #38bdf8; }
.outline-line-node.level-h2 { width: 16px; background: #f43f5e; }
.outline-line-node.level-h3 { width: 10px; background: #94a3b8; }

.outline-line-node:hover, .outline-line-node.active {
    background: #00f0ff !important; box-shadow: 0 0 10px rgba(0,240,255,0.8);
    transform: scaleX(1.3);
}

/* Floating Smart Tooltip Card (Matching Screenshot 2) */
.outline-tooltip-card {
    position: fixed; display: none; z-index: 100000;
    background: rgba(15, 20, 30, 0.96); backdrop-filter: blur(14px);
    border: 1px solid #334155; border-radius: 12px; padding: 10px 14px;
    box-shadow: 0 14px 35px rgba(0,0,0,0.6); pointer-events: none; min-width: 170px; max-width: 240px;
}
.outline-tooltip-title {
    font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 800;
    color: #38bdf8; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;
}
.outline-tooltip-sub {
    font-size: 11.5px; color: #cbd5e1; line-height: 1.4; font-family: 'Inter', sans-serif;
}

/* Custom Context Menu */
.custom-ctx-menu {
    position: fixed; display: none; z-index: 999999;
    background: rgba(18, 24, 38, 0.96); backdrop-filter: blur(12px);
    border: 1px solid #334155; border-radius: 10px; padding: 6px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.6); min-width: 170px;
}
.ctx-item {
    font-size: 12.5px; font-weight: 600; color: #e2e8f0; padding: 7px 12px;
    border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;
    transition: all 0.15s;
}
.ctx-item:hover { background: #1e293b; color: #38bdf8; }
.ctx-divider { height: 1px; background: #2d3748; margin: 4px 0; }

/* ── RIGHT COLUMN: ROUTINE CHECKLIST & REFLECTION ── */
.routine-list { display: flex; flex-direction: column; gap: 7px; margin-bottom: 16px; }
.routine-item {
    display: flex; align-items: flex-start; gap: 8px;
    background: #1a202c; border: 1px solid #2d3748; padding: 7px 9px;
    border-radius: 8px; cursor: pointer; transition: all .2s; margin: 0;
}
.routine-item:hover { border-color: #4a5568; background: #222a3a; }
.routine-item.checked { border-color: rgba(16,185,129,0.4); background: rgba(16,185,129,0.06); }
.routine-item input[type="checkbox"] { display: none; }
.custom-chk {
    width: 16px; height: 16px; border-radius: 4px; border: 2px solid #4a5568;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 1px; transition: all .2s;
}
.routine-item input[type="checkbox"]:checked + .custom-chk {
    background: #10b981; border-color: #10b981;
}
.routine-item input[type="checkbox"]:checked + .custom-chk::after {
    content: "✓"; color: #fff; font-size: 10px; font-weight: 900;
}
.routine-text { font-size: 12px; font-weight: 600; color: #e2e8f0; line-height: 1.35; }
.routine-item.checked .routine-text { color: #94a3b8; text-decoration: line-through; }

.sidebar-textarea {
    width: 100%; background: #1a202c; border: 1px solid #2d3748;
    border-radius: 9px; padding: 9px; color: #e2e8f0; font-size: 12px;
    font-family: 'Inter', sans-serif; line-height: 1.5; resize: vertical; min-height: 100px;
    outline: none; transition: border-color .2s; box-sizing: border-box;
}
.sidebar-textarea:focus { border-color: #3b82f6; }

.sidebar-save-btn {
    width: 100%; justify-content: center; padding: 8px; font-size: 12px;
    font-weight: 700; margin-top: 10px;
}

/* Lightbox */
.lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.93); z-index: 99999; align-items: center; justify-content: center; }
.lightbox.open { display: flex; }
.lightbox img { max-width: 96vw; max-height: 94vh; object-fit: contain; border-radius: 6px; }
.lightbox-close { position: absolute; top: 20px; right: 28px; color: #fff; font-size: 36px; cursor: pointer; font-weight: 300; transition: .2s; }
.lightbox-close:hover { color: #ef4444; }

@media print {
    .fixed-top-header, .sidebar-panel { display: none !important; }
    .fixed-workspace-container { margin-top: 0; height: auto; overflow: visible; padding: 0; }
    .workspace-grid { grid-template-columns: 1fr; }
    body { background: #fff; color: #000; overflow: visible; }
    .notion-paper { border: none; background: #fff; color: #000; box-shadow: none; padding: 0; height: auto; overflow: visible; }
}
</style>
@endpush

@section('content')

@php
    $prevDate = $carbon->copy()->subDay()->format('Y-m-d');
    $nextDate = $carbon->copy()->addDay()->format('Y-m-d');
@endphp

{{-- Fullscreen Exit Button --}}
<button class="fullscreen-exit-btn" id="fullscreenExitBtn" onclick="togglePaperFullscreen()">
    ✕ Exit Fullscreen [ ]
</button>

{{-- ── 1. NON-SCROLLABLE FIXED TOP SECTION ── --}}
<div class="fixed-top-header">
    <div class="aj-topbar">
        <div class="aj-topbar-left">
            <a href="{{ route('commitment.index') }}" class="aj-btn" style="padding:4px 10px; font-size:12px;">
                ← Board
            </a>

            <!-- Previous & Next Daily Page Navigation -->
            <div class="date-nav-group">
                <a href="{{ route('commitment.analysis.page', $prevDate) }}" class="date-nav-btn" title="Previous Day ({{ $carbon->copy()->subDay()->format('M j, Y') }})">‹</a>
                <div class="aj-date-badge">{{ $carbon->format('l, F j, Y') }}</div>
                <a href="{{ route('commitment.analysis.page', $nextDate) }}" class="date-nav-btn" title="Next Day ({{ $carbon->copy()->addDay()->format('M j, Y') }})">›</a>
            </div>

            <a href="{{ $ffUrl }}" target="_blank" class="aj-ff-pill">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20A10 10 0 0012 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Forex Factory · Week of {{ $carbon->copy()->startOfWeek(\Carbon\Carbon::SUNDAY)->format('M j, Y') }}
            </a>
        </div>
        <div class="aj-topbar-actions">
            <button class="aj-btn" onclick="togglePaperFullscreen()" title="Toggle Fullscreen Document">
                <span style="font-size:13px; font-weight:900;">[ ]</span> Fullscreen
            </button>
            <button class="aj-btn" onclick="window.print()">Print</button>
            <button class="aj-btn aj-btn-primary" onclick="saveDocumentNow()">Save Document</button>
            <button class="aj-btn" onclick="window.location.href='{{ route('commitment.index') }}'">Back to Board</button>
        </div>
    </div>

    {{-- Notion Document Formatting Toolbar --}}
    <div class="doc-toolbar">
        <div class="toolbar-group">
            <button class="t-btn" onclick="execCmd('formatBlock','<h1>')">Title H1</button>
            <button class="t-btn heading-btn" onclick="execCmd('formatBlock','<h2>')">LONDONPLUSONE (H2)</button>
            <span style="color:#334155;">|</span>
            <button class="t-btn" onclick="execCmd('bold')"><b>B</b> Bold</button>
            <button class="t-btn" onclick="execCmd('italic')"><i>I</i> Italic</button>
            <button class="t-btn" onclick="execCmd('underline')"><u>U</u> Underline</button>
            <button class="t-btn" onclick="execCmd('insertUnorderedList')">• Bullet List</button>
            <span style="color:#334155;">|</span>
            <button class="t-btn" onclick="insertTvChartPrompt()" style="color:#38bdf8;">📷 Insert TradingView Link</button>
        </div>
        <div class="save-status-badge" id="saveStatus">
            <span>Saved ✓</span>
        </div>
    </div>
</div>

{{-- ── 2. FIXED WORKSPACE CONTAINER (OUTER WINDOW NEVER SCROLLS) ── --}}
<div class="fixed-workspace-container">
    <div class="workspace-grid">

        {{-- LEFT COLUMN: Left Sidebar Panel --}}
        <div class="sidebar-panel left-nav-panel">
            <div class="sidebar-section-title">
                <span>Page Context</span>
                <span class="progress-pill" style="color:#38bdf8; background:rgba(56,189,248,0.12);" id="tocHeadingsCount">1 Section</span>
            </div>

            <div style="font-size:12px; color:#64748b; line-height:1.6;">
                <p>📌 <b>Daily Analysis Journal</b></p>
                <p>Use the floating outline bar on the right edge of the editor canvas to jump to specific document sections.</p>
            </div>
        </div>

        {{-- MIDDLE COLUMN: Notion Document Canvas Wrapper --}}
        <div class="paper-wrapper-relative">
            {{-- FLOATING MINIMALIST OUTLINE BAR (ALWAYS VISIBLE ON RIGHT INTERIOR EDGE) --}}
            <div id="documentOutlineNav" class="floating-outline-nav">
                <!-- Outline line nodes generated by JS -->
            </div>

            <div class="notion-paper" id="notionPaper" contenteditable="true" spellcheck="true">
                @if(!empty($entries->first()?->narrative))
                    {!! $entries->first()->narrative !!}
                @else
                    <h1>REVIEW</h1>
                    <p>Type your trading analysis notes here...</p>
                    <p><i>Tip: Copy and paste any TradingView snapshot URL directly into this document flow to render the chart inline with a timestamp overlay.</i></p>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN (Stationary): Sidebar Checklist & Reflections Panel --}}
        <div class="sidebar-panel">
            <form id="sidebarLogForm">
                @csrf
                <input type="hidden" name="log_date" value="{{ $date }}">

                <!-- Daily Routine Checklist -->
                <div class="sidebar-section-title">
                    <span>Routine Checklist</span>
                    <span class="progress-pill" id="progressPill">
                        {{ count($completedActivities) }}/{{ count($activities) }}
                    </span>
                </div>

                <div class="routine-list">
                    @forelse($activities as $idx => $act)
                        @php $isChecked = in_array($act, $completedActivities); @endphp
                        <label class="routine-item {{ $isChecked ? 'checked' : '' }}" id="lbl_act_{{ $idx }}">
                            <input type="checkbox" name="completed_activities[]" value="{{ $act }}" 
                                   {{ $isChecked ? 'checked' : '' }} onchange="toggleRoutineItem(this, 'lbl_act_{{ $idx }}')">
                            <span class="custom-chk"></span>
                            <span class="routine-text">{{ $act }}</span>
                        </label>
                    @empty
                        <div style="font-size:11.5px; color:#64748b;">No routine tasks set for this month.</div>
                    @endforelse
                </div>

                <!-- Journal & Reflection Note -->
                <div class="sidebar-section-title" style="margin-top:14px;">
                    <span>Journal & Reflection</span>
                </div>
                <textarea name="notes" id="sidebar_notes" class="sidebar-textarea" 
                          placeholder="Record your discipline reflection, emotional state, or lessons learned for today...">{{ $dailyNotes }}</textarea>

                <button type="button" class="aj-btn aj-btn-primary sidebar-save-btn" onclick="saveSidebarLog()">
                    Save Routine & Notes
                </button>
            </form>
        </div>

    </div>
</div>

{{-- ── Floating Tooltip Card for Navigation Hover (Matching Screenshot 2) ── --}}
<div class="outline-tooltip-card" id="outlineTooltip">
    <div class="outline-tooltip-title" id="tooltipTitle">SECTION TITLE</div>
    <div class="outline-tooltip-sub" id="tooltipSub">Details...</div>
</div>

{{-- ── Context Menu for Image Right-Click ── --}}
<div class="custom-ctx-menu" id="imgCtxMenu">
    <div class="ctx-item" onclick="ctxAddCaption()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
        Add / Edit Caption
    </div>
    <div class="ctx-item" onclick="ctxViewFullscreen()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
        View Fullscreen
    </div>
    <div class="ctx-divider"></div>
    <div class="ctx-item" style="color:#ef4444;" onclick="ctxDeleteBlock()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
        Delete Image Block
    </div>
</div>

{{-- ── Lightbox ── --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">×</span>
    <img src="" id="lbImg" alt="Chart">
</div>
@endsection

@push('scripts')
<script>
const DATE = '{{ $date }}';
let saveTimeout = null;
let activeTargetImg = null;
let activeTargetBlock = null;

document.addEventListener('DOMContentLoaded', function() {
    rebuildDocumentOutline();
});

function getHHMM() {
    const d = new Date();
    const hh = String(d.getHours()).padStart(2, '0');
    const mm = String(d.getMinutes()).padStart(2, '0');
    return `${hh}${mm}`;
}

function togglePaperFullscreen() {
    const paper = document.getElementById('notionPaper');
    const exitBtn = document.getElementById('fullscreenExitBtn');
    paper.classList.toggle('fullscreen-mode');

    if (paper.classList.contains('fullscreen-mode')) {
        exitBtn.style.display = 'block';
    } else {
        exitBtn.style.display = 'none';
    }
}

function toggleRoutineItem(chk, labelId) {
    const lbl = document.getElementById(labelId);
    if (chk.checked) {
        lbl.classList.add('checked');
    } else {
        lbl.classList.remove('checked');
    }
    updateProgressPill();
}

function updateProgressPill() {
    const total = document.querySelectorAll('.routine-item').length;
    const checked = document.querySelectorAll('.routine-item input:checked').length;
    const pill = document.getElementById('progressPill');
    if (pill) pill.textContent = `${checked}/${total}`;
}

function saveSidebarLog() {
    const form = document.getElementById('sidebarLogForm');
    const formData = new FormData(form);
    
    fetch("{{ route('commitment.daily_log.save') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert(res.message || 'Checklist & Reflection Notes saved!');
        } else {
            alert(res.message || 'Error saving notes.');
        }
    })
    .catch(() => alert('Error saving routine log.'));
}

/* ── MINIMALIST BAR HIERARCHY OUTLINE & SMART TOOLTIP (ROBUST ELEMENT SCANNER) ── */
function rebuildDocumentOutline() {
    const paper = document.getElementById('notionPaper');
    if (!paper) return;

    // Scan for H1-H4, timeline blocks, or bold/heading paragraphs
    let nodes = Array.from(paper.querySelectorAll('h1, h2, h3, h4, .tv-timeline-entry, p > strong, p > b'));
    
    // Filter out duplicate child nodes
    nodes = nodes.filter((node, idx, self) => {
        if (node.tagName === 'STRONG' || node.tagName === 'B') {
            return node.parentElement && node.parentElement.textContent.trim().length < 40;
        }
        return true;
    });

    const outlineContainer = document.getElementById('documentOutlineNav');
    const countBadge = document.getElementById('tocHeadingsCount');

    // Default fallback if no headings exist yet
    if (nodes.length === 0) {
        outlineContainer.innerHTML = `
            <div class="outline-line-node level-h1" onclick="scrollToHeading(null)" onmouseenter="showOutlineTooltip(event, 'DOCUMENT TOP', 'H1')" onmouseleave="hideOutlineTooltip()"></div>
            <div class="outline-line-node level-h2" onclick="scrollToHeading(null)" onmouseenter="showOutlineTooltip(event, 'SECTION 1', 'H2')" onmouseleave="hideOutlineTooltip()"></div>
            <div class="outline-line-node level-h3" onclick="scrollToHeading(null)" onmouseenter="showOutlineTooltip(event, 'SUB-SECTION', 'H3')" onmouseleave="hideOutlineTooltip()"></div>
        `;
        if (countBadge) countBadge.textContent = '1 Section';
        return;
    }

    if (countBadge) countBadge.textContent = `${nodes.length} ${nodes.length === 1 ? 'Section' : 'Sections'}`;

    let html = '';
    nodes.forEach((el, index) => {
        let targetEl = (el.tagName === 'STRONG' || el.tagName === 'B') ? el.parentElement : el;
        if (!targetEl.id) {
            targetEl.id = 'doc-heading-node-' + index;
        }
        
        let text = targetEl.textContent.trim();
        if (text.length > 30) text = text.substring(0, 30) + '...';
        if (!text) text = 'Section ' + (index + 1);

        let tag = targetEl.tagName.toLowerCase();
        let levelClass = 'level-h2';
        if (tag === 'h1') levelClass = 'level-h1';
        else if (tag === 'h2') levelClass = 'level-h2';
        else levelClass = 'level-h3';

        html += `<div class="outline-line-node ${levelClass}" 
                      onclick="scrollToHeading('${targetEl.id}')"
                      onmouseenter="showOutlineTooltip(event, '${escapeHtml(text)}', '${tag.toUpperCase()}')"
                      onmouseleave="hideOutlineTooltip()">
                 </div>`;
    });

    outlineContainer.innerHTML = html;
}

function showOutlineTooltip(e, title, tag) {
    const tt = document.getElementById('outlineTooltip');
    document.getElementById('tooltipTitle').textContent = title;
    document.getElementById('tooltipSub').textContent = `Section: ${tag} • Jump to Content`;

    const rect = e.target.getBoundingClientRect();
    tt.style.left = (rect.left - 220) + 'px';
    tt.style.top = (rect.top - 10) + 'px';
    tt.style.display = 'block';
}

function hideOutlineTooltip() {
    document.getElementById('outlineTooltip').style.display = 'none';
}

/* ── SCROLL ONLY INSIDE #notionPaper CANVAS ── */
function scrollToHeading(id) {
    const paper = document.getElementById('notionPaper');
    if (!id) {
        paper.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }
    const target = document.getElementById(id);
    if (paper && target) {
        const paperRect = paper.getBoundingClientRect();
        const targetRect = target.getBoundingClientRect();
        const relativeTop = targetRect.top - paperRect.top + paper.scrollTop - 20;

        paper.scrollTo({ top: relativeTop, behavior: 'smooth' });

        target.style.transition = 'background-color 0.4s';
        const origBg = target.style.backgroundColor;
        target.style.backgroundColor = 'rgba(56,189,248,0.25)';
        setTimeout(() => target.style.backgroundColor = origBg, 1200);
    }
}

function escapeHtml(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/* ── RIGHT-CLICK CONTEXT MENU ON IMAGES ── */
document.getElementById('notionPaper').addEventListener('contextmenu', function(e) {
    const img = e.target.closest('img');
    if (img) {
        e.preventDefault();
        activeTargetImg = img;
        activeTargetBlock = img.closest('.tv-timeline-entry') || img.closest('.tv-embed-block');

        const menu = document.getElementById('imgCtxMenu');
        menu.style.left = e.clientX + 'px';
        menu.style.top = e.clientY + 'px';
        menu.style.display = 'block';
    }
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('#imgCtxMenu')) {
        document.getElementById('imgCtxMenu').style.display = 'none';
    }
});

function ctxAddCaption() {
    document.getElementById('imgCtxMenu').style.display = 'none';
    if (!activeTargetBlock && activeTargetImg) {
        activeTargetBlock = activeTargetImg.parentElement;
    }
    if (activeTargetBlock) {
        let caption = activeTargetBlock.querySelector('.tv-caption');
        if (!caption) {
            caption = document.createElement('div');
            caption.className = 'tv-caption';
            caption.setAttribute('contenteditable', 'true');
            caption.setAttribute('placeholder', 'Add a caption...');
            caption.textContent = 'Add image caption here...';
            activeTargetBlock.appendChild(caption);
        }
        caption.focus();
        const range = document.createRange();
        range.selectNodeContents(caption);
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    }
}

function ctxViewFullscreen() {
    document.getElementById('imgCtxMenu').style.display = 'none';
    if (activeTargetImg) {
        openLightbox(activeTargetImg.src);
    }
}

function ctxDeleteBlock() {
    document.getElementById('imgCtxMenu').style.display = 'none';
    if (activeTargetBlock && confirm('Delete this image block?')) {
        activeTargetBlock.remove();
        triggerAutoSave();
        rebuildDocumentOutline();
    }
}

function execCmd(command, value = null) {
    document.execCommand(command, false, value);
    document.getElementById('notionPaper').focus();
    triggerAutoSave();
    setTimeout(rebuildDocumentOutline, 100);
}

function resolveTvUrl(url) {
    if (!url) return null;
    if (/\.(png|jpg|jpeg|gif|webp)/i.test(url)) return url;
    const m = url.match(/tradingview\.com\/x\/([a-zA-Z0-9]+)\/?$/);
    if (m) return `https://s3.tradingview.com/snapshots/${m[1][0].toLowerCase()}/${m[1]}.png`;
    return null;
}

function insertTvChartPrompt() {
    const url = prompt('Paste TradingView snapshot link (e.g. https://www.tradingview.com/x/AbCdEfGh/):');
    if (url) embedChartAtCursor(url);
}

/* ── TIMELINE VIEW EMBED ── */
function embedChartAtCursor(url) {
    const previewUrl = resolveTvUrl(url);
    if (!previewUrl) {
        alert('Invalid TradingView snapshot URL');
        return;
    }
    const timeStr = getHHMM();
    
    const embedHtml = `
        <div class="tv-timeline-entry" contenteditable="false">
            <div class="timeline-left-gutter">
                <span class="timeline-time-tag">${timeStr}hrs</span>
                <div class="timeline-v-line"></div>
            </div>
            <div class="timeline-body-content">
                <div class="tv-embed-block">
                    <img src="${previewUrl}" class="tv-embed-img" alt="Chart" onclick="openLightbox('${previewUrl}')">
                    <a href="${url}" target="_blank" class="tv-open-link">Open in TradingView ↗</a>
                </div>
                <div class="tv-caption" contenteditable="true" placeholder="Right-click image to edit caption...">Add image caption...</div>
            </div>
        </div>
        <p><br></p>
    `;
    document.execCommand('insertHTML', false, embedHtml);
    triggerAutoSave();
    setTimeout(rebuildDocumentOutline, 100);
}

// Listen for paste event to auto-detect TradingView links
document.getElementById('notionPaper').addEventListener('paste', function(e) {
    const text = (e.clipboardData || window.clipboardData).getData('text');
    if (text && /tradingview\.com\/x\/[a-zA-Z0-9]+/.test(text)) {
        e.preventDefault();
        embedChartAtCursor(text.trim());
    } else {
        triggerAutoSave();
        setTimeout(rebuildDocumentOutline, 100);
    }
});

document.getElementById('notionPaper').addEventListener('input', function() {
    triggerAutoSave();
    rebuildDocumentOutline();
});

function triggerAutoSave() {
    document.getElementById('saveStatus').innerHTML = '<span style="color:#fbbf24;">Saving...</span>';
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveDocumentNow, 1200);
}

function saveDocumentNow() {
    const htmlContent = document.getElementById('notionPaper').innerHTML;
    const timeCode = getHHMM();
    fetch('/commitment-board/analysis/save-doc', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            analysis_date: DATE,
            html_content: htmlContent
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            document.getElementById('saveStatus').innerHTML = `<span style="color:#10b981;">Saved (${timeCode}) ✓</span>`;
        } else {
            document.getElementById('saveStatus').innerHTML = '<span style="color:#ef4444;">Save failed</span>';
        }
    })
    .catch(() => {
        document.getElementById('saveStatus').innerHTML = '<span style="color:#ef4444;">Save failed</span>';
    });
}

function openLightbox(src) {
    document.getElementById('lbImg').src = src;
    document.getElementById('lightbox').classList.add('open');
}
function closeLightbox() { document.getElementById('lightbox').classList.remove('open'); }
</script>
@endpush
