@extends('layouts.app')

@section('title', 'Commitment Board')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Courier+Prime:wght@700&family=Inter:wght@400;600;800&family=Montserrat:wght@400;600;700;800;900&family=Outfit:wght@400;600;800&family=Playfair+Display:ital,wght@0,600;0,800;1,600&family=Pacifico&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<style>
    .board-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        background: #ffffff;
        padding: 16px 24px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .board-title-group h4 {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .board-title-group p {
        margin: 2px 0 0 0;
        font-size: 13px;
        color: #64748b;
    }

    .month-nav-btn {
        background: #f1f5f9;
        color: #1e293b;
        border: 1px solid #cbd5e1;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 13px;
        padding: 6px 14px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .month-nav-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .month-display-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 16px;
        letter-spacing: 1px;
        color: #0f172a;
        min-width: 170px;
        text-align: center;
    }

    .btn-action-custom {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 13px;
        padding: 6px 14px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-action-custom:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
    
    .btn-edit-active {
        background: #1b00ff !important;
        color: #ffffff !important;
        border-color: #1b00ff !important;
        box-shadow: 0 0 10px rgba(27, 0, 255, 0.3);
    }

    /* Floating Save/Cancel Bar */
    #floating-save-bar {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 24px;
        border-radius: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        display: none;
        align-items: center;
        gap: 15px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 600;
    }

    /* Main Canvas - Single Seamless Background Color */
    #commitment-canvas-container {
        position: relative;
        background-color: #f6ebd9;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        overflow: hidden;
        min-height: 850px;
        padding: 30px;
    }

    .canvas-content-wrapper {
        position: relative;
        z-index: 2;
        min-height: 800px;
    }

    /* Grid Layout Templates */
    .grid-layout-hero_top {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }
    .grid-layout-split {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
    }
    .grid-layout-sidebar_left {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 24px;
    }

    @media (max-width: 1100px) {
        .grid-layout-split, .grid-layout-sidebar_left {
            grid-template-columns: 1fr;
        }
    }

    /* Draggable & Resizable Grid Items */
    .draggable-grid-item {
        position: relative;
        box-sizing: border-radius;
        transition: box-shadow 0.2s;
    }

    /* Resizable Handles Control */
    .ui-resizable-handle {
        display: none !important; /* HIDDEN in View Mode */
        background-color: #1b00ff;
        border: 1px solid #ffffff;
        border-radius: 50%;
        width: 10px;
        height: 10px;
        z-index: 99;
    }

    /* When Edit Mode is Active */
    .edit-mode-active .draggable-grid-item {
        cursor: move !important;
        outline: 2px dashed #1b00ff !important;
        outline-offset: 4px;
    }
    .edit-mode-active .ui-resizable-handle {
        display: block !important; /* VISIBLE in Edit Mode */
    }

    /* Calendar Block - Background Image strictly applied to Calendar Container */
    .calendar-container {
        position: relative;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
        height: 100%;
    }

    .calendar-container-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255, 255, 255, var(--cal-opacity, 0.35));
        z-index: 1;
        pointer-events: none;
    }

    .calendar-inner-content {
        position: relative;
        z-index: 2;
    }

    .calendar-month-header {
        font-family: 'Montserrat', sans-serif;
        font-size: 32px;
        font-weight: 900;
        letter-spacing: 2px;
        color: #ffffff;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
        margin-bottom: 16px;
    }

    .calendar-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 6px;
        table-layout: fixed;
    }

    .calendar-table th {
        text-align: center;
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 13px;
        color: #ffffff;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.7);
        padding-bottom: 8px;
        letter-spacing: 1px;
    }

    /* Equal Fixed-Height Day Tiles */
    .day-tile {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        height: 85px;
        min-height: 85px;
        max-height: 85px;
        padding: 8px;
        vertical-align: top;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
    }

    .day-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        background: #ffffff;
        z-index: 5;
    }

    .day-tile.other-month {
        opacity: 0.3;
        background: rgba(255, 255, 255, 0.5);
    }

    .day-tile.is-today {
        border: 2.5px solid #1b00ff !important;
        box-shadow: 0 0 12px rgba(27, 0, 255, 0.3);
        background: #ffffff;
    }

    .day-number {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 13px;
        color: #0f172a;
    }

    .discipline-progress-ring {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 10px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 20px;
    }
    .ring-complete {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }
    .ring-partial {
        background: #fef9c3;
        color: #a16207;
        border: 1px solid #fde047;
    }

    .discipline-bar-mini {
        width: 100%;
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        margin-top: 4px;
        overflow: hidden;
    }
    .discipline-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #059669);
        border-radius: 2px;
    }

    .trade-tag-subtle {
        font-size: 9px;
        font-weight: 700;
        color: #64748b;
        margin-top: 3px;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Photo Collages */
    .collage-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
        width: 100%;
        height: 100%;
    }

    .collage-card-item {
        position: relative;
        background: #ffffff;
        border-radius: 12px;
        padding: 6px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        overflow: hidden;
        cursor: pointer;
    }

    .collage-card-item img {
        width: 100%;
        height: 100%;
        min-height: 140px;
        object-fit: cover;
        border-radius: 8px;
    }

    .dropzone-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(27, 0, 255, 0.85);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 12px;
        padding: 10px;
        border-radius: 12px;
        opacity: 0;
        transition: opacity 0.2s;
        pointer-events: none;
        z-index: 10;
    }
    .collage-card-item.dragover .dropzone-overlay,
    .calendar-container.dragover .dropzone-overlay {
        opacity: 1;
    }

    /* Sub Grid & Quote Box Fix */
    .board-grid-sub {
        display: grid;
        grid-template-columns: 1fr 1fr 340px;
        gap: 24px;
        margin-top: 24px;
        align-items: stretch;
    }

    @media (max-width: 1100px) {
        .board-grid-sub {
            grid-template-columns: 1fr;
        }
    }

    .banner-greatest-box {
        padding: 10px;
        width: 100%;
        height: 100%;
    }
    .banner-cursive-text {
        font-family: 'Pacifico', cursive;
        color: #f97316;
        font-size: 38px;
        margin: 0;
    }
    .banner-heading-text {
        font-family: 'Montserrat', sans-serif;
        font-size: 46px;
        font-weight: 900;
        letter-spacing: 2px;
        color: #0f172a;
        line-height: 1;
        margin: 4px 0 10px 0;
    }
    .banner-body-text {
        font-size: 15px;
        color: #334155;
        line-height: 1.4;
    }

    /* Green Quote Box Container Fix: Scalable border & text container */
    .green-quote-card {
        border: 3px solid #10b981;
        border-radius: 16px;
        padding: 24px;
        position: relative;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(5px);
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .green-quote-card-text {
        font-family: 'Courier Prime', monospace;
        font-weight: 700;
        font-size: 16px;
        color: #059669;
        text-align: center;
        line-height: 1.5;
        margin: 0;
        width: 100%;
    }

    .sticky-note-box {
        position: relative;
        transform: rotate(3deg);
        transition: transform 0.3s;
        width: 100%;
        height: 100%;
    }
    .sticky-note-box:hover {
        transform: rotate(0deg);
    }
    .sticky-tape-strip {
        width: 80px;
        height: 24px;
        background: rgba(249, 115, 22, 0.8);
        position: absolute;
        top: -10px;
        left: 20px;
        z-index: 10;
        transform: rotate(-6deg);
    }
    .sticky-paper {
        background: #ffffff;
        border-radius: 8px;
        padding: 24px 20px 18px 20px;
        box-shadow: 4px 8px 20px rgba(0,0,0,0.12);
        background-image: repeating-linear-gradient(white 0px, white 24px, #cbd5e1 25px);
        line-height: 25px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .sticky-paper-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        font-size: 22px;
        color: #0f172a;
        text-align: center;
        letter-spacing: 1px;
        border-bottom: 2px solid #0f172a;
        padding-bottom: 4px;
        margin-bottom: 12px;
    }
    .sticky-paper-sig {
        font-family: 'Caveat', cursive;
        font-size: 22px;
        color: #475569;
        text-align: right;
        margin-top: 25px;
    }

    /* Custom Added Dynamic Widgets Styling */
    .custom-widget-box {
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        position: relative;
        width: 100%;
        height: 100%;
        box-sizing: border-box;
    }
    .corner-curved { border-radius: 16px !important; }
    .corner-square { border-radius: 0px !important; }

    .widget-delete-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        background: #ef4444;
        color: #ffffff;
        border: none;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-size: 11px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 100;
    }
    .edit-mode-active .widget-delete-btn {
        display: flex !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Top Control Bar -->
    <div class="board-header">
        <div class="board-title-group">
            <h4>Commitment Board</h4>
            <p>Discipline, Consistency & Routine Execution Canvas</p>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            <!-- FullCalendar Month Navigation -->
            <div class="d-flex align-items-center mr-3">
                <button class="month-nav-btn mr-1" onclick="changeMonth('{{ $carbonMonth->copy()->subMonth()->format('Y-m') }}')">
                    &lt; Prev
                </button>
                <div class="month-display-title" id="monthDisplayTitle">
                    {{ strtoupper($carbonMonth->format('F Y')) }}
                </div>
                <button class="month-nav-btn ml-1" onclick="changeMonth('{{ $carbonMonth->copy()->addMonth()->format('Y-m') }}')">
                    Next &gt;
                </button>
                <button class="month-nav-btn ml-2" onclick="changeMonth('{{ date('Y-m') }}')">
                    Today
                </button>
            </div>

            <!-- Edit Mode Toggle Button -->
            @if($canEditLayout)
                <button class="btn-action-custom mr-2" id="btnToggleEditMode" onclick="toggleEditMode()">
                    Toggle Edit Mode
                </button>
                <button class="btn-action-custom btn-action-primary mr-2" data-toggle="modal" data-target="#editPlanModal">
                    Settings
                </button>
            @endif

            <!-- More Options Dropdown Button -->
            <div class="dropdown d-inline-block">
                <button class="btn-action-custom dropdown-toggle" type="button" id="moreOptionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    More Options
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="moreOptionsDropdown">
                    <a class="dropdown-item" href="javascript:void(0)" onclick="toggleFullscreen()">
                        Fullscreen View
                    </a>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="exportToImage()">
                        Export as PNG Image
                    </a>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="exportToPDF()">
                        Export as PDF Document
                    </a>
                    @if($canEditLayout)
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-primary" href="javascript:void(0)" data-toggle="modal" data-target="#addCustomWidgetModal">
                            + Add Custom Grid Widget
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Save/Cancel Positions Bar -->
    <div id="floating-save-bar">
        <span>Layout moved or resized! Save changes?</span>
        <button class="btn btn-sm btn-success rounded-pill px-3" onclick="saveWidgetPositions()">
            ✔ Save Layout
        </button>
        <button class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="cancelWidgetPositions()">
            ✖ Restore
        </button>
    </div>

    <!-- Main Canvas Frame -->
    <div id="commitment-canvas-container">
        <div class="canvas-content-wrapper" id="commitment-canvas">
            
            <!-- Dynamic Grid Layout Wrapper -->
            <div id="gridTemplateWrapper" class="grid-layout-{{ $plan->layout_config['grid_template'] ?? 'hero_top' }}">
                
                <!-- Calendar Block Widget -->
                <div class="draggable-grid-item" id="widget-calendar">
                    <div class="calendar-container" id="calendarBgContainer" style="{{ $plan->background_image ? 'background-image: url(' . asset($plan->background_image) . ');' : '' }} --cal-opacity: {{ $plan->layout_config['bg_opacity'] ?? 0.35 }}">
                        <div class="calendar-container-overlay"></div>
                        <div class="dropzone-overlay" id="calDropOverlay">Drop photo here to set Calendar background</div>

                        <div class="calendar-inner-content">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="calendar-month-header mb-0" id="calendarTitleText">
                                    {{ strtoupper($carbonMonth->format('F Y')) }}
                                </div>
                            </div>

                            <table class="calendar-table">
                                <thead>
                                    <tr>
                                        <th>SUN</th>
                                        <th>MON</th>
                                        <th>TUE</th>
                                        <th>WED</th>
                                        <th>THU</th>
                                        <th>FRI</th>
                                        <th>SAT</th>
                                    </tr>
                                </thead>
                                <tbody id="calendarTbody">
                                    @include('discipline.partials.calendar_grid', [
                                        'carbonMonth' => $carbonMonth,
                                        'dailyLogs' => $dailyLogs,
                                        'tradesDaily' => $tradesDaily,
                                        'plan' => $plan,
                                        'todayDate' => $todayDate
                                    ])
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Top Quote & Photo Collages Block Widget -->
                <div class="draggable-grid-item" id="widget-quote-collages">
                    <div class="mb-3" id="topQuoteElement" style="font-family: '{{ $plan->layout_config['quote_top_font'] ?? 'Outfit' }}', sans-serif; font-size: 16px; font-weight: 600; color: #0f172a; line-height: 1.4;">
                        {{ $plan->layout_config['quote_top'] ?? 'First you create the process. Then the process creates you. Eventually, you and the process become one.' }}
                    </div>

                    <div class="collage-grid-container">
                        <div class="collage-card-item collage-dropzone" data-slot="collage_img_1">
                            <div class="dropzone-overlay">Drop image here</div>
                            <img src="{{ $plan->layout_config['collage_img_1'] ?? asset('deskapp/vendors/images/photo1.jpg') }}" id="img_slot_collage_img_1" alt="Vision 1">
                        </div>
                        <div class="collage-card-item collage-dropzone" data-slot="collage_img_2">
                            <div class="dropzone-overlay">Drop image here</div>
                            <img src="{{ $plan->layout_config['collage_img_2'] ?? asset('deskapp/vendors/images/photo2.jpg') }}" id="img_slot_collage_img_2" alt="Vision 2">
                        </div>
                        <div class="collage-card-item collage-dropzone" data-slot="collage_img_3">
                            <div class="dropzone-overlay">Drop image here</div>
                            <img src="{{ $plan->layout_config['collage_img_3'] ?? asset('deskapp/vendors/images/photo3.jpg') }}" id="img_slot_collage_img_3" alt="Vision 3">
                        </div>
                        <div class="collage-card-item collage-dropzone" data-slot="collage_img_4">
                            <div class="dropzone-overlay">Drop image here</div>
                            <img src="{{ $plan->layout_config['collage_img_4'] ?? asset('deskapp/vendors/images/photo4.jpg') }}" id="img_slot_collage_img_4" alt="Vision 4">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sub Grid Items -->
            <div class="board-grid-sub" id="customWidgetsContainer">
                <!-- Bottom Left Banner Widget -->
                <div class="banner-greatest-box draggable-grid-item" id="widget-banner">
                    <p class="banner-cursive-text">You're the</p>
                    <h1 class="banner-heading-text" id="bannerTitleText">{{ strtoupper($plan->layout_config['banner_left_title'] ?? "GREATEST") }}</h1>
                    <p class="banner-body-text" id="bannerBodyText">
                        {{ $plan->layout_config['banner_left_body'] ?? "Greatness begins as a belief before it becomes a reality. You must see it, believe it, and commit yourself to becoming it." }}
                    </p>
                </div>

                <!-- Bottom Middle Green Quote Box Widget -->
                <div class="green-quote-card draggable-grid-item" id="widget-green-quote">
                    <p class="green-quote-card-text" id="greenQuoteText" style="font-family: '{{ $plan->layout_config['quote_box_font'] ?? 'Courier Prime' }}', monospace;">
                        {{ $plan->layout_config['quote_box'] ?? "Talent may open doors, but discipline, commitment, and relentless work are what keep you in the room" }}
                    </p>
                </div>

                <!-- Bottom Right Sticky Note Widget -->
                <div class="sticky-note-box draggable-grid-item" id="widget-sticky-note">
                    <div class="sticky-tape-strip"></div>
                    <div class="sticky-paper">
                        <div class="sticky-paper-title" id="stickyTitleText">
                            {{ $plan->layout_config['sticky_title'] ?? "IN GOD WE TRUST" }}
                        </div>
                        <div class="sticky-paper-sig" id="stickySigText" style="font-family: '{{ $plan->layout_config['sticky_font'] ?? 'Caveat' }}', cursive;">
                            {{ $plan->layout_config['sticky_signature'] ?? "Segolame Timothy Bush Monyake" }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ADD CUSTOM GRID WIDGET MODAL -->
<div class="modal fade" id="addCustomWidgetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-family: 'Outfit', sans-serif; font-weight: 700;">Add Custom Grid Widget</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addCustomWidgetForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Widget Type</label>
                        <select id="w_type" class="form-control" onchange="toggleWidgetTypeInputs(this.value)">
                            <option value="text">Custom Text / Quote Box</option>
                            <option value="image">Image Card</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Corner Style</label>
                        <select id="w_corner" class="form-control">
                            <option value="curved">Curved Rounded Corners (16px)</option>
                            <option value="square">90° Square Corners</option>
                        </select>
                    </div>

                    <!-- Text Widget Inputs -->
                    <div id="textWidgetInputs">
                        <div class="form-group">
                            <label class="font-weight-bold">Heading Title (Optional)</label>
                            <input type="text" id="w_title" class="form-control" placeholder="e.g. MORNING MOTTO">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Body / Quote Text</label>
                            <textarea id="w_body" class="form-control" rows="3" placeholder="Enter quote or text..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Font Family</label>
                                <select id="w_font" class="form-control">
                                    <option value="Outfit">Outfit</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Courier Prime">Courier Prime</option>
                                    <option value="Caveat">Caveat</option>
                                    <option value="Playfair Display">Playfair Display</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Text Color</label>
                                <input type="color" id="w_color" class="form-control h-auto" value="#0f172a">
                            </div>
                        </div>
                    </div>

                    <!-- Image Widget Inputs -->
                    <div id="imageWidgetInputs" style="display: none;">
                        <div class="form-group">
                            <label class="font-weight-bold">Image Orientation</label>
                            <select id="w_img_orient" class="form-control">
                                <option value="landscape">Landscape Mode</option>
                                <option value="portrait">Portrait Mode</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Select Image File</label>
                            <input type="file" id="w_img_file" class="form-control-file">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Grid Widget</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT LAYOUT & MONTHLY PLAN MODAL -->
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-family: 'Outfit', sans-serif; font-weight: 700;">
                    Commitment Board Settings
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPlanForm">
                @csrf
                <input type="hidden" name="year_month" id="plan_year_month" value="{{ $yearMonth }}">

                <div class="modal-body">
                    @if(!$canEditLayout)
                        <div class="alert alert-warning font-13">
                            <strong>Layout Locked:</strong> Month layout editing is only enabled during days 1 to 5 of the month.
                        </div>
                    @endif

                    <ul class="nav nav-tabs customtab" id="planModalTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="layout-tab" data-toggle="tab" href="#tab-layout" role="tab">
                                Grid & Transparency
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activities-tab" data-toggle="tab" href="#tab-activities" role="tab">
                                Daily Routine Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="typography-tab" data-toggle="tab" href="#tab-typography" role="tab">
                                Quotes & Fonts
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pd-20" id="planModalTabsContent">
                        <!-- Tab 1: Grid Template & Background Opacity -->
                        <div class="tab-pane fade show active" id="tab-layout" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Grid Layout Template</label>
                                    <select name="layout_config[grid_template]" class="form-control" {{ !$canEditLayout ? 'disabled' : '' }}>
                                        <option value="hero_top" {{ ($plan->layout_config['grid_template'] ?? '') == 'hero_top' ? 'selected' : '' }}>Hero Calendar Top</option>
                                        <option value="split" {{ ($plan->layout_config['grid_template'] ?? '') == 'split' ? 'selected' : '' }}>Split Canvas</option>
                                        <option value="sidebar_left" {{ ($plan->layout_config['grid_template'] ?? '') == 'sidebar_left' ? 'selected' : '' }}>Sidebar Left</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Calendar Background Overlay Opacity</label>
                                    <input type="range" name="layout_config[bg_opacity]" class="form-control-range" min="0.0" max="0.8" step="0.05" value="{{ $plan->layout_config['bg_opacity'] ?? 0.35 }}" oninput="updateCalOpacityPreview(this.value)" {{ !$canEditLayout ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Daily Routine Items -->
                        <div class="tab-pane fade" id="tab-activities" role="tabpanel">
                            <p class="text-muted font-13">Define the routine activities you commit to executing daily:</p>
                            <div id="activitiesListContainer">
                                @foreach($plan->monthly_activities ?? [] as $index => $act)
                                    <div class="input-group mb-2 activity-row">
                                        <input type="text" name="monthly_activities[]" class="form-control" value="{{ $act }}" required {{ !$canEditLayout ? 'readonly' : '' }}>
                                        @if($canEditLayout)
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger" onclick="$(this).closest('.activity-row').remove();">
                                                    Remove
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if($canEditLayout)
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addActivityRow()">
                                    + Add Routine Task
                                </button>
                            @endif
                        </div>

                        <!-- Tab 3: Quotes & Typography Styles -->
                        <div class="tab-pane fade" id="tab-typography" role="tabpanel">
                            <div class="form-group">
                                <label class="font-weight-bold">Top Right Quote</label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <textarea name="layout_config[quote_top]" class="form-control" rows="2" {{ !$canEditLayout ? 'readonly' : '' }}>{{ $plan->layout_config['quote_top'] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="layout_config[quote_top_font]" class="form-control" {{ !$canEditLayout ? 'disabled' : '' }}>
                                            <option value="Outfit" {{ ($plan->layout_config['quote_top_font'] ?? '') == 'Outfit' ? 'selected' : '' }}>Outfit</option>
                                            <option value="Playfair Display" {{ ($plan->layout_config['quote_top_font'] ?? '') == 'Playfair Display' ? 'selected' : '' }}>Playfair Display</option>
                                            <option value="Montserrat" {{ ($plan->layout_config['quote_top_font'] ?? '') == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Green Quote Box Text & Font</label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <textarea name="layout_config[quote_box]" class="form-control" rows="2" {{ !$canEditLayout ? 'readonly' : '' }}>{{ $plan->layout_config['quote_box'] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="layout_config[quote_box_font]" class="form-control" {{ !$canEditLayout ? 'disabled' : '' }}>
                                            <option value="Courier Prime" {{ ($plan->layout_config['quote_box_font'] ?? '') == 'Courier Prime' ? 'selected' : '' }}>Courier Prime</option>
                                            <option value="Inter" {{ ($plan->layout_config['quote_box_font'] ?? '') == 'Inter' ? 'selected' : '' }}>Inter</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Sticky Note Title & Signature</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" name="layout_config[sticky_title]" class="form-control" value="{{ $plan->layout_config['sticky_title'] ?? '' }}" {{ !$canEditLayout ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="layout_config[sticky_signature]" class="form-control" value="{{ $plan->layout_config['sticky_signature'] ?? '' }}" {{ !$canEditLayout ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @if($canEditLayout)
                        <button type="submit" class="btn btn-primary">
                            Save Settings
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DAILY COMMITMENT EXECUTION MODAL -->
<div class="modal fade" id="dayDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a, #1e293b); color: #fff; border-radius: 8px 8px 0 0;">
                <div>
                    <h5 class="modal-title" id="dayModalTitle" style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #fff; margin: 0;">Daily Log</h5>
                    <small id="dayModalSubtitle" style="color: #94a3b8; font-size: 12px;"></small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a id="openAnalysisPageBtn" href="#" target="_blank"
                       style="font-family:'Outfit',sans-serif; font-weight:700; font-size:12px; background:rgba(27,0,255,.85); color:#fff; padding:6px 14px; border-radius:8px; text-decoration:none; border:1px solid rgba(27,0,255,.5); white-space:nowrap; margin-right:10px;">
                        Full Analysis Journal ↗
                    </a>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:.7;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body p-0">
                <!-- Forex Factory Week Banner -->
                <div id="ffWeekBanner" style="display:none; background:#1e293b; padding:8px 20px; font-size:12px; color:#94a3b8; border-bottom:1px solid #334155;">
                    <a id="ffWeekLink" href="#" target="_blank" style="color:#fbbf24; font-weight:600; text-decoration:none;">
                        📅 Forex Factory Economic Calendar — <span id="ffWeekText"></span>
                    </a>
                </div>

                <!-- Modal Tabs -->
                <ul class="nav nav-tabs px-3 pt-3" id="dayModalTabs" role="tablist"
                    style="border-bottom: 1px solid #e2e8f0; gap: 4px;">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-checklist-trigger" data-toggle="tab" href="#tab-checklist" role="tab"
                           style="font-family:'Outfit',sans-serif; font-weight:700; font-size:13px; padding: 8px 16px;">
                            Checklist
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-analysis-trigger" data-toggle="tab" href="#tab-analysis" role="tab"
                           style="font-family:'Outfit',sans-serif; font-weight:700; font-size:13px; padding: 8px 16px;">
                            Analysis Journal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-trades-trigger" data-toggle="tab" href="#tab-trades" role="tab"
                           style="font-family:'Outfit',sans-serif; font-weight:700; font-size:13px; padding: 8px 16px;">
                            Trades
                        </a>
                    </li>
                </ul>

                <form id="saveDailyLogForm">
                    @csrf
                    <input type="hidden" name="log_date" id="log_date_input">

                    <div class="tab-content p-4" id="dayModalTabContent">

                        <!-- Tab 1: Checklist & Reflection -->
                        <div class="tab-pane fade show active" id="tab-checklist" role="tabpanel">
                            <div id="ruleBanner" class="alert mb-3 font-13"></div>
                            <div style="background:#f8fafc; border-left:4px solid #1b00ff; border-radius:8px; padding:16px 18px; margin-bottom:16px;">
                                <h6 class="font-weight-bold mb-3" style="font-size:13px; color:#0f172a;">Daily Routine Checklist</h6>
                                <div id="dayChecklistContainer"></div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold" style="font-size:13px;">Daily Journal & Reflection Note</label>
                                <textarea name="notes" id="daily_notes_input" class="form-control" rows="3"
                                          placeholder="Log your thoughts, emotional state, or takeaways for today..."></textarea>
                            </div>
                        </div>

                        <!-- Tab 2: Analysis Journal (Notion / Word Style Document) -->
                        <div class="tab-pane fade" id="tab-analysis" role="tabpanel">
                            <div style="background:#0f172a; border-radius:12px; padding:16px 18px; margin-bottom:16px;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 style="font-family:'Outfit',sans-serif; font-weight:800; color:#fff; margin:0; font-size:14px;">
                                        Daily Journal Document
                                    </h6>
                                    <span style="font-size:11px; color:#64748b;">Paste TradingView links anywhere to embed inline</span>
                                </div>

                                <!-- Mini Notion Toolbar -->
                                <div style="background:#1a2035; border-radius:8px; padding:6px 10px; margin-bottom:12px; display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                                    <button type="button" class="btn btn-sm btn-dark py-0 px-2 font-11" onclick="modalExecCmd('formatBlock','<h1>')">Title H1</button>
                                    <button type="button" class="btn btn-sm btn-dark py-0 px-2 font-11" style="font-family:'Outfit',sans-serif; text-decoration:underline; font-weight:800;" onclick="modalExecCmd('formatBlock','<h2>')">LONDONPLUSONE (H2)</button>
                                    <button type="button" class="btn btn-sm btn-dark py-0 px-2 font-11" onclick="modalExecCmd('bold')"><b>B</b></button>
                                    <button type="button" class="btn btn-sm btn-dark py-0 px-2 font-11" onclick="modalExecCmd('italic')"><i>I</i></button>
                                    <button type="button" class="btn btn-sm btn-dark py-0 px-2 font-11" onclick="modalExecCmd('underline')"><u>U</u></button>
                                    <button type="button" class="btn btn-sm btn-dark py-0 px-2 font-11" onclick="modalExecCmd('insertUnorderedList')">• List</button>
                                    <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 font-11" onclick="modalInsertChartPrompt()">📷 Insert Chart Link</button>
                                    <button type="button" class="btn btn-sm btn-success py-0 px-2 font-11 ml-auto" onclick="modalSaveDoc()">Save Journal</button>
                                </div>

                                <!-- Document Canvas -->
                                <div id="modalNotionPaper" contenteditable="true" spellcheck="true"
                                     style="background:#141820; border:1px solid #334155; border-radius:10px; padding:20px; min-height:300px; max-height:480px; overflow-y:auto; color:#e2e8f0; font-size:14px; line-height:1.7; outline:none;">
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Trade Metrics -->
                        <div class="tab-pane fade" id="tab-trades" role="tabpanel">
                            <div id="tradesSummaryStats" class="mb-2 font-13 font-weight-bold"></div>
                            <div id="tradesListContainer"></div>
                            <div id="noTradesMessage" class="text-center text-muted py-4" style="display:none;">
                                No trades recorded for this day.
                            </div>
                        </div>

                    </div><!-- /.tab-content -->

                    <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" id="saveLogBtn" class="btn btn-success btn-sm">
                            Save Execution Log
                        </button>
                    </div>
                </form>

            </div><!-- /.modal-body -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    let currentMonthStr = "{{ $yearMonth }}";
    let loadedPositions = @json($plan->layout_config['widget_positions'] ?? []);
    let customWidgets = @json($plan->layout_config['custom_widgets'] ?? []);
    let isEditMode = false;

    $(document).ready(function() {
        // Render custom widgets if any
        renderCustomWidgets();

        // Initialize Draggable and Resizable (Disabled by default until Edit Mode is ON)
        initDraggableAndResizable();

        // Apply saved positions
        applyWidgetPositions(loadedPositions);

        // Setup HTML5 Drag & Drop image file dropzones
        setupImageDropzones();
    });

    function toggleEditMode() {
        isEditMode = !isEditMode;
        const $btn = $('#btnToggleEditMode');
        const $container = $('#commitment-canvas-container');

        if (isEditMode) {
            $btn.addClass('btn-edit-active').text('Edit Mode: ON (Click to Lock)');
            $container.addClass('edit-mode-active');
            
            // Enable dragging & resizing
            $(".draggable-grid-item").draggable("enable").resizable("enable");
            iziToastNotify('info', 'Edit Mode Active: Drag & resize widgets freely horizontally and vertically!');
        } else {
            $btn.removeClass('btn-edit-active').text('Toggle Edit Mode');
            $container.removeClass('edit-mode-active');
            
            // Disable dragging & resizing
            $(".draggable-grid-item").draggable("disable").resizable("disable");
            hideFloatingSaveBar();
            iziToastNotify('success', 'Edit Mode locked.');
        }
    }

    function initDraggableAndResizable() {
        $(".draggable-grid-item").draggable({
            containment: "#commitment-canvas-container",
            scroll: false,
            opacity: 0.85,
            cursor: "move",
            disabled: true, // disabled on page load
            stop: function() {
                if (isEditMode) showFloatingSaveBar();
            }
        }).resizable({
            containment: "#commitment-canvas-container",
            handles: "all",
            disabled: true, // disabled on page load
            stop: function() {
                if (isEditMode) showFloatingSaveBar();
            }
        });
    }

    function showFloatingSaveBar() {
        $('#floating-save-bar').css('display', 'flex');
    }

    function hideFloatingSaveBar() {
        $('#floating-save-bar').hide();
    }

    function saveWidgetPositions() {
        let positions = {};
        $('.draggable-grid-item').each(function() {
            let id = $(this).attr('id');
            if (id) {
                positions[id] = {
                    left: $(this).css('left'),
                    top: $(this).css('top'),
                    width: $(this).css('width'),
                    height: $(this).css('height'),
                    position: $(this).css('position')
                };
            }
        });

        iziToastNotify('info', 'Saving layout positions...');

        $.ajax({
            url: "{{ route('commitment.plan.save') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                year_month: currentMonthStr,
                layout_config: { 
                    widget_positions: positions,
                    custom_widgets: customWidgets
                }
            },
            success: function(res) {
                if (res.success) {
                    loadedPositions = positions;
                    hideFloatingSaveBar();
                    iziToastNotify('success', 'Layout positions saved!');
                }
            },
            error: function(err) {
                iziToastNotify('error', 'Failed to save positions');
            }
        });
    }

    function cancelWidgetPositions() {
        applyWidgetPositions(loadedPositions);
        hideFloatingSaveBar();
        iziToastNotify('info', 'Layout positions restored.');
    }

    function applyWidgetPositions(positions) {
        if (!positions || Object.keys(positions).length === 0) return;
        
        Object.keys(positions).forEach(function(id) {
            let pos = positions[id];
            let $el = $('#' + id);
            if ($el.length && pos) {
                $el.css({
                    position: pos.position || 'relative',
                    left: pos.left || 'auto',
                    top: pos.top || 'auto',
                    width: pos.width || 'auto',
                    height: pos.height || 'auto'
                });
            }
        });
    }

    function updateCalOpacityPreview(val) {
        document.getElementById('calendarBgContainer').style.setProperty('--cal-opacity', val);
    }

    function toggleWidgetTypeInputs(val) {
        if (val === 'text') {
            $('#textWidgetInputs').show();
            $('#imageWidgetInputs').hide();
        } else {
            $('#textWidgetInputs').hide();
            $('#imageWidgetInputs').show();
        }
    }

    $('#addCustomWidgetForm').on('submit', function(e) {
        e.preventDefault();
        const type = $('#w_type').val();
        const corner = $('#w_corner').val();
        const widgetId = 'custom_w_' + Date.now();

        let widgetData = {
            id: widgetId,
            type: type,
            corner: corner
        };

        if (type === 'text') {
            widgetData.title = $('#w_title').val();
            widgetData.body = $('#w_body').val();
            widgetData.font = $('#w_font').val();
            widgetData.color = $('#w_color').val();

            customWidgets.push(widgetData);
            renderCustomWidgets();
            $('#addCustomWidgetModal').modal('hide');
            if (isEditMode) showFloatingSaveBar();
        } else {
            const fileInput = document.getElementById('w_img_file');
            if (fileInput.files && fileInput.files[0]) {
                uploadImageFile(fileInput.files[0], function(url) {
                    widgetData.img_url = url;
                    widgetData.orient = $('#w_img_orient').val();
                    customWidgets.push(widgetData);
                    renderCustomWidgets();
                    $('#addCustomWidgetModal').modal('hide');
                    if (isEditMode) showFloatingSaveBar();
                });
            } else {
                iziToastNotify('error', 'Please select an image file');
            }
        }
    });

    function deleteCustomWidget(widgetId) {
        customWidgets = customWidgets.filter(w => w.id !== widgetId);
        $(`#${widgetId}`).remove();
        showFloatingSaveBar();
    }

    function renderCustomWidgets() {
        if (!customWidgets || customWidgets.length === 0) return;

        customWidgets.forEach(function(w) {
            if ($(`#${w.id}`).length) return; // already rendered

            let cornerClass = w.corner === 'square' ? 'corner-square' : 'corner-curved';
            let contentHtml = '';

            if (w.type === 'text') {
                contentHtml = `
                    <div class="custom-widget-box ${cornerClass}">
                        <button class="widget-delete-btn" onclick="deleteCustomWidget('${w.id}')">&times;</button>
                        ${w.title ? `<h5 style="font-family:'${w.font}',sans-serif; color:${w.color}; font-weight:800;">${w.title}</h5>` : ''}
                        <p style="font-family:'${w.font}',sans-serif; color:${w.color}; font-size:15px; margin:0; line-height:1.4;">
                            ${w.body}
                        </p>
                    </div>
                `;
            } else {
                contentHtml = `
                    <div class="custom-widget-box ${cornerClass} pd-0 overflow-hidden">
                        <button class="widget-delete-btn" onclick="deleteCustomWidget('${w.id}')">&times;</button>
                        <img src="${w.img_url}" style="width:100%; height:100%; object-fit:cover;" alt="Custom Image">
                    </div>
                `;
            }

            let $newWidget = $(`<div class="draggable-grid-item" id="${w.id}">${contentHtml}</div>`);
            $('#customWidgetsContainer').append($newWidget);

            // Re-init draggable and resizable on new widget
            $newWidget.draggable({
                containment: "#commitment-canvas-container",
                scroll: false,
                opacity: 0.85,
                disabled: !isEditMode,
                stop: function() { if (isEditMode) showFloatingSaveBar(); }
            }).resizable({
                containment: "#commitment-canvas-container",
                handles: "all",
                disabled: !isEditMode,
                stop: function() { if (isEditMode) showFloatingSaveBar(); }
            });
        });
    }

    /* Direct Drag & Drop File Upload Handler */
    function setupImageDropzones() {
        const $calContainer = $('#calendarBgContainer');
        $calContainer.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $calContainer.addClass('dragover');
        }).on('dragleave dragend drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $calContainer.removeClass('dragover');
        });

        $calContainer.on('drop', function(e) {
            let files = e.originalEvent.dataTransfer.files;
            if (files && files[0]) {
                uploadImageFile(files[0], function(url) {
                    $calContainer.css('background-image', `url(${url})`);
                    savePlanAttribute({ background_image: url });
                });
            }
        });

        $('.collage-dropzone').each(function() {
            const $slot = $(this);
            const slotKey = $slot.data('slot');

            $slot.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $slot.addClass('dragover');
            }).on('dragleave dragend drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $slot.removeClass('dragover');
            });

            $slot.on('drop', function(e) {
                let files = e.originalEvent.dataTransfer.files;
                if (files && files[0]) {
                    uploadImageFile(files[0], function(url) {
                        $(`#img_slot_${slotKey}`).attr('src', url);
                        let configUpdate = {};
                        configUpdate[slotKey] = url;
                        savePlanAttribute({ layout_config: configUpdate });
                    });
                }
            });
        });
    }

    function uploadImageFile(file, callback) {
        let formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        iziToastNotify('info', 'Uploading image...');

        $.ajax({
            url: "{{ route('commitment.upload_image') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    iziToastNotify('success', 'Photo uploaded!');
                    if (callback) callback(res.url);
                }
            },
            error: function(err) {
                iziToastNotify('error', 'Image upload failed');
            }
        });
    }

    function savePlanAttribute(dataObj) {
        dataObj._token = '{{ csrf_token() }}';
        dataObj.year_month = currentMonthStr;

        $.ajax({
            url: "{{ route('commitment.plan.save') }}",
            type: 'POST',
            data: dataObj,
            success: function(res) {}
        });
    }

    function changeMonth(targetMonth) {
        currentMonthStr = targetMonth;
        $('#calendarTbody').html('<tr><td colspan="7" class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading month data...</td></tr>');

        $.ajax({
            url: "{{ route('commitment.month_data') }}?month=" + targetMonth,
            type: 'GET',
            success: function(res) {
                $('#monthDisplayTitle').text(res.monthTitle);
                $('#calendarTitleText').text(res.monthTitle);
                $('#plan_year_month').val(res.yearMonth);

                $('.month-nav-btn:first').attr('onclick', `changeMonth('${res.prevMonth}')`);
                $('.month-nav-btn:eq(1)').attr('onclick', `changeMonth('${res.nextMonth}')`);

                if (res.plan && res.plan.background_image) {
                    $('#calendarBgContainer').css('background-image', `url(${res.plan.background_image})`);
                }

                if (res.plan && res.plan.layout_config) {
                    loadedPositions = res.plan.layout_config.widget_positions || {};
                    customWidgets = res.plan.layout_config.custom_widgets || [];
                    applyWidgetPositions(loadedPositions);
                    renderCustomWidgets();
                }

                renderCalendarGrid(res);
            },
            error: function(err) {
                iziToastNotify('error', 'Failed to load month data');
            }
        });
    }

    function renderCalendarGrid(res) {
        const dailyLogs = res.dailyLogs || {};
        const tradesDaily = res.tradesDaily || {};
        const plan = res.plan || {};
        const activitiesCount = (plan.monthly_activities || []).length;
        const todayDate = res.todayDate;

        const firstDayOfWeek = res.firstDayOfWeek;
        const daysInMonth = res.daysInMonth;
        const totalCells = Math.ceil((firstDayOfWeek + daysInMonth) / 7) * 7;

        let html = '';
        let dateIter = new Date(res.startOfMonth);
        dateIter.setDate(dateIter.getDate() - firstDayOfWeek);

        for (let i = 0; i < totalCells; i++) {
            if (i % 7 === 0) html += '<tr>';

            const dStr = dateIter.toISOString().split('T')[0];
            const isCurrentMonth = (dateIter.getMonth() + 1) === parseInt(res.yearMonth.split('-')[1]);
            const isToday = (dStr === todayDate);

            const log = dailyLogs[dStr];
            const completedCount = log && log.completed_activities ? log.completed_activities.length : 0;
            const trade = tradesDaily[dStr];

            html += `
                <td class="day-tile ${!isCurrentMonth ? 'other-month' : ''} ${isToday ? 'is-today' : ''}" onclick="openDayModal('${dStr}')">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="day-number">${String(dateIter.getDate()).padStart(2, '0')}</span>
                        ${completedCount > 0 ? `
                            <span class="discipline-progress-ring ${completedCount >= activitiesCount ? 'ring-complete' : 'ring-partial'}">
                                ${completedCount}/${activitiesCount}
                            </span>
                        ` : ''}
                    </div>

                    ${activitiesCount > 0 ? `
                        <div class="discipline-bar-mini">
                            <div class="discipline-bar-fill" style="width: ${Math.round((completedCount / activitiesCount) * 100)}%;"></div>
                        </div>
                    ` : ''}

                    ${trade ? `
                        <span class="trade-tag-subtle">
                            ${trade.pnl >= 0 ? '+' : ''}$${Math.round(trade.pnl)} (${trade.total_trades}T)
                        </span>
                    ` : ''}
                </td>
            `;

            dateIter.setDate(dateIter.getDate() + 1);
            if (i % 7 === 6) html += '</tr>';
        }

        $('#calendarTbody').html(html);
    }

    function toggleFullscreen() {
        const elem = document.getElementById('commitment-canvas-container');
        if (!document.fullscreenElement) {
            if (elem.requestFullscreen) elem.requestFullscreen();
            else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    function exportToImage() {
        const elem = document.getElementById('commitment-canvas-container');
        iziToastNotify('info', 'Generating high-resolution PNG image...');

        html2canvas(elem, {
            scale: 2,
            useCORS: true,
            logging: false
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = `Commitment_Board_${currentMonthStr}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
            iziToastNotify('success', 'Image exported successfully!');
        });
    }

    function exportToPDF() {
        const elem = document.getElementById('commitment-canvas-container');
        iziToastNotify('info', 'Generating PDF document...');

        html2canvas(elem, {
            scale: 2,
            useCORS: true,
            logging: false
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'landscape',
                unit: 'px',
                format: [canvas.width, canvas.height]
            });

            pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
            pdf.save(`Commitment_Board_${currentMonthStr}.pdf`);
            iziToastNotify('success', 'PDF exported successfully!');
        });
    }

    function addActivityRow() {
        const html = `
            <div class="input-group mb-2 activity-row">
                <input type="text" name="monthly_activities[]" class="form-control" placeholder="New routine item..." required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger" onclick="$(this).closest('.activity-row').remove();">
                        Remove
                    </button>
                </div>
            </div>
        `;
        $('#activitiesListContainer').append(html);
    }

    $('#editPlanForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('commitment.plan.save') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    iziToastNotify('success', res.message);
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: function(err) {
                iziToastNotify('error', err.responseJSON ? err.responseJSON.message : 'Error saving plan');
            }
        });
    });

    function openDayModal(date) {
        $('#log_date_input').val(date);
        $('#dayChecklistContainer').html('<div class="text-center py-3"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
        $('#qaEntriesList').html('<div style="text-align:center;color:#64748b;padding:20px;font-size:13px;">Loading entries...</div>');

        // Set Full Analysis Journal links
        const pageUrl = `/commitment-board/analysis/${date}/page`;
        $('#openAnalysisPageBtn').attr('href', pageUrl);
        $('#openAnalysisPageBtn2').attr('href', pageUrl);

        // Forex Factory calendar link: find preceding Sunday
        const d = new Date(date);
        const dayOfWeek = d.getDay(); // 0=Sun
        const sunday = new Date(d);
        sunday.setDate(d.getDate() - dayOfWeek);
        const monthNames = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        const ffUrl = `https://www.forexfactory.com/calendar?week=${monthNames[sunday.getMonth()]}${sunday.getDate()}.${sunday.getFullYear()}`;
        const ffDisplay = sunday.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
        $('#ffWeekLink').attr('href', ffUrl);
        $('#ffWeekText').text('Week of ' + ffDisplay);
        $('#ffWeekBanner').show();

        // Load checklist & trades
        $.ajax({
            url: "/commitment-board/day/" + date,
            type: 'GET',
            success: function(res) {
                $('#dayModalTitle').text(res.formatted_date);
                $('#dayModalSubtitle').text(res.is_today ? '✦ Today' : 'Commitment Log — Read Only');

                const isToday = res.is_today;
                const canEdit = res.can_edit_log;

                if (isToday) {
                    $('#ruleBanner').attr('class', 'alert alert-success mb-3 font-13').html("Today's Log: Check off completed tasks and record your reflection.");
                    $('#saveLogBtn').show();
                    $('#daily_notes_input').prop('disabled', false).val(res.notes || '');
                } else {
                    $('#ruleBanner').attr('class', 'alert alert-secondary mb-3 font-13').html('Read-Only: Past days cannot be edited to maintain accountability.');
                    $('#saveLogBtn').hide();
                    $('#daily_notes_input').prop('disabled', true).val(res.notes || '');
                }

                let activitiesHtml = '';
                if (res.activities && res.activities.length > 0) {
                    res.activities.forEach(function(act, idx) {
                        const isChecked = res.completed_activities.includes(act) ? 'checked' : '';
                        const disabledAttr = !canEdit ? 'disabled' : '';
                        activitiesHtml += `
                            <div class="custom-control custom-checkbox mb-2 font-14">
                                <input type="checkbox" name="completed_activities[]" value="${act}" class="custom-control-input" id="task_${idx}" ${isChecked} ${disabledAttr}>
                                <label class="custom-control-label font-weight-600" for="task_${idx}">${act}</label>
                            </div>
                        `;
                    });
                } else {
                    activitiesHtml = '<p class="text-muted">No routine activities defined for this month yet.</p>';
                }
                $('#dayChecklistContainer').html(activitiesHtml);

                // Trades tab
                const tSum = res.trade_summary;
                if (tSum.count > 0) {
                    const pnlColor = tSum.total_pnl > 0 ? '#10b981' : (tSum.total_pnl < 0 ? '#ef4444' : '#6b7280');
                    $('#tradesSummaryStats').html(`
                        Trades: <span class="badge badge-secondary">${tSum.count}</span>
                        &nbsp;|&nbsp; Net PnL: <span style="color:${pnlColor}; font-weight:800;">$${tSum.total_pnl.toFixed(2)}</span>
                        &nbsp;|&nbsp; <span class="text-success">${tSum.won_trades}W</span> / <span class="text-danger">${tSum.lost_trades}L</span>
                    `);
                    let tradesHtml = '<div class="table-responsive"><table class="table table-sm table-bordered bg-white mb-0"><thead><tr><th>Symbol</th><th>Dir</th><th>Outcome</th><th>PnL</th><th>Plan</th></tr></thead><tbody>';
                    res.trades.forEach(function(t) {
                        const bc = t.outcome === 'win' ? 'badge-success' : (t.outcome === 'loss' ? 'badge-danger' : 'badge-secondary');
                        tradesHtml += `<tr>
                            <td><strong>${t.symbol}</strong></td>
                            <td>${t.direction}</td>
                            <td><span class="badge ${bc}">${t.outcome.toUpperCase()}</span></td>
                            <td style="font-weight:700;color:${t.pnl >= 0 ? '#10b981' : '#ef4444'}">${t.pnl >= 0 ? '+' : ''}$${t.pnl}</td>
                            <td>${t.plan_followed}</td>
                        </tr>`;
                    });
                    tradesHtml += '</tbody></table></div>';
                    $('#tradesListContainer').html(tradesHtml);
                    $('#noTradesMessage').hide();
                } else {
                    $('#tradesListContainer').html('');
                    $('#noTradesMessage').show();
                }

                $('#dayDetailsModal').modal('show');
            },
            error: function() { iziToastNotify('error', 'Failed to load date details.'); }
        });

        // Load analysis document
        $.ajax({
            url: "/commitment-board/analysis/" + date,
            type: 'GET',
            success: function(res) {
                if (res.document_html && res.document_html.trim() !== '') {
                    $('#modalNotionPaper').html(res.document_html);
                } else {
                    $('#modalNotionPaper').html('<p>Type your trading analysis notes here...</p><p><i>Tip: Paste a TradingView link anywhere in this document to embed chart snapshots inline.</i></p>');
                }
            }
        });
    }

    function modalExecCmd(command, value = null) {
        document.execCommand(command, false, value);
        document.getElementById('modalNotionPaper').focus();
    }

    function modalInsertChartPrompt() {
        const url = prompt('Paste TradingView snapshot link (e.g. https://www.tradingview.com/x/AbCdEfGh/):');
        if (url) embedModalChartAtCursor(url);
    }

    function embedModalChartAtCursor(url) {
        const previewUrl = resolveTvUrlQA(url);
        if (!previewUrl) {
            alert('Invalid TradingView snapshot URL');
            return;
        }
        const now = new Date();
        const timeStr = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false});
        
        const embedHtml = `
            <div class="tv-embed-block" contenteditable="false" style="margin: 16px 0; position: relative; border-radius: 10px; overflow: hidden; background: #000; border: 1px solid #334155;">
                <div style="position:absolute; top:8px; left:8px; z-index:3; background:rgba(15,23,42,0.85); color:#38bdf8; font-size:11px; font-weight:800; font-family:'JetBrains Mono',monospace; padding:3px 8px; border-radius:5px; border:1px solid rgba(56,189,248,.3); backdrop-filter:blur(4px);">⏰ ${timeStr}</div>
                <img src="${previewUrl}" style="width:100%; max-height:450px; object-fit:contain; display:block; cursor:pointer;" onclick="window.open('${url}','_blank')">
                <a href="${url}" target="_blank" style="position:absolute; bottom:8px; right:8px; z-index:3; background:rgba(0,0,0,0.75); color:#fff; font-size:10px; padding:3px 8px; border-radius:4px; text-decoration:none;">Open in TradingView ↗</a>
            </div>
            <p><br></p>
        `;
        document.execCommand('insertHTML', false, embedHtml);
    }

    $(document).on('paste', '#modalNotionPaper', function(e) {
        const text = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        if (text && /tradingview\.com\/x\/[a-zA-Z0-9]+/.test(text)) {
            e.preventDefault();
            embedModalChartAtCursor(text.trim());
        }
    });

    function modalSaveDoc() {
        const date = $('#log_date_input').val();
        const html = $('#modalNotionPaper').html();
        $.ajax({
            url: "/commitment-board/analysis/save-doc",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                analysis_date: date,
                html_content: html
            },
            success: function(res) {
                if (res.success) {
                    iziToastNotify('success', 'Journal document saved!');
                } else {
                    iziToastNotify('error', 'Error saving document.');
                }
            },
            error: function() {
                iziToastNotify('error', 'Error saving document.');
            }
        });
    }

    function resolveTvUrlQA(url) {
        if (!url) return null;
        if (/\.(png|jpg|jpeg|gif|webp)/i.test(url)) return url;
        const m = url.match(/tradingview\.com\/x\/([a-zA-Z0-9]+)\/?$/);
        if (m) return `https://s3.tradingview.com/snapshots/${m[1][0].toLowerCase()}/${m[1]}.png`;
        return null;
    }

    function qaDeleteEntry(id) {
        if (!confirm('Delete this entry?')) return;
        $.ajax({
            url: `/commitment-board/analysis/${id}`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
            success: function() {
                const date = $('#log_date_input').val();
                $.get("/commitment-board/analysis/" + date, function(r) { renderQaEntries(r.entries || []); });
            }
        });
    }

    $('#saveDailyLogForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('commitment.daily_log.save') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    iziToastNotify('success', res.message);
                    $('#dayDetailsModal').modal('hide');
                    setTimeout(() => changeMonth(currentMonthStr), 500);
                } else {
                    iziToastNotify('error', res.message);
                }
            },
            error: function(err) {
                iziToastNotify('error', err.responseJSON ? err.responseJSON.message : 'Failed to save daily log.');
            }
        });
    });
</script>
@endpush

