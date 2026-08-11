@extends('layouts.app')
@section('title', 'Trades')

@section('left-sidebar')
    <div class="right-sidebar">
        <div class="sidebar-title d-flex justify-content-between align-items-center">
            <h3 class="weight-600 font-16 text-primary mb-0">
                <i class="dw dw-filter text-primary mr-2"></i> Filter Bets
            </h3>
            <div class="close-sidebar" data-toggle="right-sidebar-close">
                <i class="icon-copy ion-close-round"></i>
            </div>
        </div>

        <div class="right-sidebar-body customscroll">
            <div class="right-sidebar-body-content">
                <form id="filter_form" class="pb-20">

                    <div class="form-group">
                        <label>Date Range</label>
                        <div class="d-flex flex-wrap mb-2" id="date-ranges">
                            <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="today">Today</button>
                            <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="yesterday">Yesterday</button>
                            <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="this_week">This Week</button>
                            <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="last_week">Last Week</button>
                            <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="this_month">This Month</button>
                            <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="last_month">Last Month</button>
                            <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="this_year">This Year</button>
                            <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="last_year">Last Year</button>
                        </div>
                        <input class="form-control" id="date-picker" placeholder="Select Date" type="text">
                        <input type="hidden" name="start_date" id="start_date">
                        <input type="hidden" name="end_date" id="end_date">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="asset_type">Asset Type</label>
                                <select class="form-control" name="asset_type" id="asset_type">
                                    <option value="0">All</option>
                                    @foreach($assetTypes ?? [] as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="market">Asset</label>
                                <select class="form-control" name="market" id="market">
                                    <option value="0">All</option>
                                    @foreach($assets ?? [] as $asset)
                                    <option value="{{ $asset->id }}">{{ strtoupper($asset->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direction">Direction</label>
                                <select class="form-control" name="direction" id="direction">
                                    <option value="0">All</option>
                                    <option value="long">Long</option>
                                    <option value="short">Short</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="session">Session</label>
                                <select class="form-control" name="session" id="session">
                                    <option value="0">All</option>
                                    <option value="london_open">London Open</option>
                                    <option value="ny_open">New York Open</option>
                                    <option value="london_close">London Close</option>
                                    <option value="asia">Asian</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="outcome">Outcome</label>
                                <select class="form-control" name="outcome" id="outcome">
                                    <option value="0">All</option>
                                    <option value="win">Win</option>
                                    <option value="pending">Pending</option>
                                    <option value="loss">Loss</option>
                                    <option value="breakeven">Breakeven</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hin_day_filter">HIN Day?</label>
                                <select class="form-control" name="hin_day" id="hin_day_filter">
                                    <option value="0">All</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entry_pd">Entry PD</label>
                                <select class="form-control" name="entry_pd" id="entry_pd">
                                    <option value="0">All</option>
                                    <option value="OB">OB</option>
                                    <option value="FVG">FVG</option>
                                    <option value="IFVG">IFVG</option>
                                    <option value="BB">BB</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="plan_followed">Followed Plan?</label>
                                <select class="form-control" name="plan_followed" id="plan_followed">
                                    <option value="0">All</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entry_type">Exec Type</label>
                                <select class="form-control" name="entry_type" id="entry_type">
                                    <option value="0">All</option>
                                    <option value="market">Market</option>
                                    <option value="limit">Limit</option>
                                    <option value="stop">Stop</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="has_emotions">Has Emotions</label>
                                <select class="form-control" name="has_emotions" id="has_emotions">
                                    <option value="0">All</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="has_news">Has News</label>
                                <select class="form-control" name="has_news" id="has_news">
                                    <option value="0">All</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="form-group mb-0 mt-3">
                        <button type="submit" class="btn btn-primary btn-block">
                            Apply Filters
                        </button>
                    </div> --}}
                </form>
            </div>
        </div>
    </div>
@endsection

@section('title', 'Dashboard')

@section('content')
    @push('styles')
        <style>
            .expanded-card {
                padding: 12px;
            }

            .expanded-card .badge {
                font-size: 0.8rem;
                margin-right: 6px;
            }

            .expanded-card .small-label {
                font-size: 0.85rem;
                color: #6c757d;
            }

            .expanded-card .note-text {
                white-space: pre-wrap;
            }

            .screenshot-thumb {
                width: 100%;
                height: 80px;
                object-fit: cover;
                border-radius: 4px;
                cursor: pointer;
                border: 1px solid #e9ecef;
                transition: transform .12s ease;
            }

            .screenshot-thumb:hover {
                transform: scale(1.03);
            }

            .screens-grid .col-thumb {
                padding: 4px;
            }

            .table-expanded-links a.btn-sm {
                padding: .25rem .45rem;
                font-size: .78rem;
            }

            /* footer tweak for compact look inside the child row */
            .dataTables_child_row {
                padding: 6px 12px;
                background: #fbfbfb;
                border-radius: 6px;
            }

            /* make modal images responsive */
            #screenshotModal .modal-body {
                padding: 1rem;
                max-height: 70vh;
                overflow-y: auto;
            }

            #screenshotModal .carousel-item img {
                width: 100%;
                height: auto;
                display: block;
                margin: 0 auto;
            }

            .screenshot-img {
                object-fit: contain;
                max-height: 500px;
                width: 100%;
                border-radius: 8px;
            }

            /* Fullscreen modal styles */
            .modal.modal-fullscreen {
                padding: 0 !important;
            }

            .modal.modal-fullscreen .modal-dialog {
                max-width: 100vw !important;
                width: 100vw !important;
                height: 100vh !important;
                margin: 0 !important;
                padding: 0 !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                transform: none !important;
            }

            .modal.modal-fullscreen .modal-content {
                height: 100vh !important;
                border: 0;
                border-radius: 0 !important;
            }

            .modal.modal-fullscreen .modal-body {
                max-height: calc(100vh - 120px) !important;
                overflow-y: auto;
            }

            .fullscreen-toggle {
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .fullscreen-toggle:hover {
                transform: scale(1.1);
            }

            /* Image zoom overlay */
            .screenshot-image-wrapper {
                position: relative;
                cursor: pointer;
            }

            .screenshot-image-wrapper:hover .zoom-overlay {
                opacity: 1;
            }

            .zoom-overlay {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(0, 0, 0, 0.7);
                color: white;
                padding: 15px 20px;
                border-radius: 50%;
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
                z-index: 5;
            }

            .zoom-overlay i {
                font-size: 24px;
            }

            /* Image fullscreen modal */
            .image-fullscreen-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.95);
                z-index: 99999;
                align-items: center;
                justify-content: center;
            }

            .image-fullscreen-modal.active {
                display: flex;
            }

            .image-fullscreen-modal img {
                max-width: 95vw;
                max-height: 95vh;
                object-fit: contain;
            }

            .image-fullscreen-close {
                position: absolute;
                top: 20px;
                right: 30px;
                color: white;
                font-size: 40px;
                font-weight: bold;
                cursor: pointer;
                z-index: 100000;
                transition: all 0.3s ease;
            }

            .image-fullscreen-close:hover {
                color: #ff4444;
                transform: scale(1.2);
            }

            /* Loading overlay */
            .modal-loading-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255, 255, 255, 0.95);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                border-radius: 8px;
            }

            .modal-loading-overlay .spinner {
                width: 50px;
                height: 50px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #1b00ff;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .screenshot-card {
                position: relative;
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .screenshot-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }

            .screenshot-card:hover .screenshot-actions {
                opacity: 1;
                visibility: visible;
            }

            .screenshot-actions {
                position: absolute;
                top: 20px;
                right: 20px;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                z-index: 10;
            }

            .screenshot-actions .btn {
                margin-left: 5px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }

            .screenshot-meta {
                margin-top: 10px;
                padding-top: 10px;
                border-top: 1px solid #f0f0f0;
            }

            .screenshot-url {
                font-size: 0.85rem;
                color: #666;
                word-break: break-all;
                margin-bottom: 5px;
            }

            .screenshot-caption {
                font-size: 0.9rem;
                color: #333;
                font-style: italic;
            }

            .screenshot-index {
                position: absolute;
                top: 20px;
                left: 20px;
                background: rgba(0,0,0,0.7);
                color: white;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 0.85rem;
                font-weight: 600;
            }

            .modal-header-actions {
                display: flex;
                gap: 10px;
            }

            @media print {
                .screenshot-actions,
                .modal-header button,
                .modal-footer {
                    display: none !important;
                }

                .screenshot-card {
                    page-break-inside: avoid;
                    box-shadow: none;
                    border: 1px solid #ddd;
                }

                #screenshotModal .modal-body {
                    max-height: none;
                    overflow: visible;
                }
            }

            .right-sidebar {
                width: 600px !important;
                right: -600px;
            }
            .right-sidebar.right-sidebar-visible {
                right: 0;
            }
            @media (max-width: 768px) {
                .right-sidebar {
                    width: 100% !important;
                    right: -100%;
                }
            }

            /* dt-control expand button - blue circle with + */
            td.dt-control {
                background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%231b00ff"><circle cx="12" cy="12" r="10"/><path fill="white" d="M11 7h2v10h-2z"/><path fill="white" d="M7 11h10v2H7z"/></svg>') no-repeat center center;
                background-size: 24px 24px;
                cursor: pointer;
                width: 40px;
                text-align: center;
            }

            td.dt-control:hover {
                background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%230056b3"><circle cx="12" cy="12" r="10"/><path fill="white" d="M11 7h2v10h-2z"/><path fill="white" d="M7 11h10v2H7z"/></svg>');
            }

            tr.shown td.dt-control {
                background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%231b00ff"><circle cx="12" cy="12" r="10"/><path fill="white" d="M7 11h10v2H7z"/></svg>');
            }

            tr.shown td.dt-control:hover {
                background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%230056b3"><circle cx="12" cy="12" r="10"/><path fill="white" d="M7 11h10v2H7z"/></svg>');
            }

            /* Hide the responsive arrow that appears on small screens */
            td.dt-control:before {
                display: none !important;
            }

            /* Reduce row padding */
            #trades_table tbody td {
                padding: 8px 10px !important;
                vertical-align: middle;
            }

            #trades_table thead th {
                padding: 10px !important;
            }

            /* Make table text darker and bolder (except badges) */
            #trades_table tbody td {
                color: #333 !important;
                font-weight: 500;
            }

            /* Keep badges with their own styling */
            #trades_table tbody td .badge {
                font-weight: 600;
            }

            /* Make specific columns extra bold: Asset, Date, RR, PNL */
            #trades_table tbody td:nth-child(2),  /* Date */
            #trades_table tbody td:nth-child(3),  /* Asset */
            #trades_table tbody td:nth-child(7),  /* RR */
            #trades_table tbody td:nth-child(8) { /* PNL */
                font-weight: 600 !important;
                color: #222 !important;
            }
        </style>
    @endpush

    <div class="modal fade" id="add_screenshot_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-2xl shadow-lg">

                <form id="add_screenshot_form" method="post">
                    @csrf
                    <input type="hidden" id="tradeId" name="trade_id" value="">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Screenshot</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Preview -->
                        <div id="previewContainer" class="mb-3" style="display:none;">
                            <img id="previewImage" src="" alt="TradingView Preview"
                                class="img-fluid w-100 rounded border" style="max-height: 400px; object-fit: contain;">
                        </div>


                        <!-- URL Input -->
                        <div class="form-group">
                            <label for="tvUrl">TradingView Snapshot URL</label>
                            <input type="url" name="url" class="form-control" id="tvUrl"
                                placeholder="https://www.tradingview.com/x/xxxxxxx/">
                        </div>



                        <!-- Caption -->
                        <div class="form-group">
                            <label for="tradeCaption">Caption / Notes</label>
                            <textarea class="form-control" name="notes" id="tradeCaption" rows="1" placeholder="Enter your notes..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button id="deleteScreenshotBtn" class="btn btn-sm btn-danger delete-screenshot pull-left"
                            style="margin-right:auto; display:none">
                            Delete
                        </button>
                        <button id="saveScreenshotBtn" class="btn btn-primary">Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="screenshotModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width:900px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Trade Logs</h5>
                    <div class="modal-header-actions">
                        <button type="button" class="btn btn-sm btn-outline-secondary fullscreen-toggle" id="fullscreenToggleBtn" title="Toggle Fullscreen">
                            <i class="fa fa-expand" id="fullscreenIcon"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="printLogsBtn" title="Print Logs">
                            <i class="fa fa-print"></i> Print
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" id="downloadPdfBtn" title="Download as PDF">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </button>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                </div>
                <div class="modal-body" id="screenshotContainer" style="position: relative;">
                    <!-- Loading overlay -->
                    <div class="modal-loading-overlay" id="modalLoadingOverlay">
                        <div class="spinner"></div>
                    </div>
                    <!-- JS will inject screenshots here -->
                </div>

                <div class="modal-footer">
                    <span class="text-muted small" id="screenshotCount"></span>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Fullscreen Modal -->
    <div class="image-fullscreen-modal" id="imageFullscreenModal">
        <span class="image-fullscreen-close" id="imageFullscreenClose">&times;</span>
        <img src="" alt="Fullscreen Image" id="fullscreenImage">
    </div>




    <div class="card-box mb-30">
        <div class="pd-20">
            <div class="row">
                <div class="col-lg-4">
                    <h4 class="text-blue h4 ">Trades</h4>
                </div>
                <div class="col-lg-4">
                    @if(env('DB_DATABASE') === 'edgy_demo')
                        <h4 class="text-danger h4 text-center">Demo / Testing Data</h4>
                    @else
                        <h4 class="text-success h4 text-center">Real Data</h4>
                    @endif
                </div>
                <div class="col-lg-4">
                    <button class="btn btn-outline-dark pull-right" id="add_trade_btn">
                        <i class="dw dw-add"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="pb-20">
            <table id="trades_table" class="data-table table stripe hover nowrap">
                <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Account</th>
                        <th>Asset</th>
                        <th>Direction</th>
                        <th>Session</th>
                        <th>Outcome</th>
                        <th>RR</th>
                        <th>PNL($)</th>
                        <th>HIN Day?</th>
                        <th>Entry PD</th>
                        <th>4l'd Plan?</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr style="background: #304d6d;color:#fff">
                        <th colspan="7">Total</th>
                        <th></th> <!-- RR total -->
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Collapsible Features Table -->
    <div class="card-box mb-30">
        <div class="pd-20 d-flex justify-content-between align-items-center" style="cursor:pointer;" data-toggle="collapse" data-target="#featuresTableCollapse" aria-expanded="false" aria-controls="featuresTableCollapse">
            <h4 class="text-blue h4 mb-0">
                <i class="dw dw-lightbulb"></i> Upcoming Features (Next Weeks)
            </h4>
            <span class="badge badge-info">Click to Expand/Collapse</span>
        </div>
        <div class="collapse" id="featuresTableCollapse">
            <div class="pb-20">
                <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Feature</th>
                            <th>Week</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>4</td>
                            <td>
                                <strong>COT Analysis</strong>
                                <br>
                                <span class="text-muted small">Commitment of Traders data integration</span>
                            </td>
                            <td>Week 5<br><span class="text-muted small">Sat 16 Nov - Fri 22 Nov 2024</span></td>
                            <td><span class="badge badge-warning">Pending</span></td>
                            <td><span class="text-muted">Visualize COT reports alongside trades</span></td>
                        </tr>


                        <tr>
                            <td>9</td>
                            <td>
                                <strong>Year-End Review & Improvements</strong>
                                <br>
                                <span class="text-muted small">Summary dashboard and feedback collection</span>
                            </td>
                            <td>Week 10<br><span class="text-muted small">Sat 21 Dec - Tue 31 Dec 2024</span></td>
                            <td><span class="badge badge-warning">Pending</span></td>
                            <td><span class="text-muted">Wrap up and plan for next year</span></td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-right text-muted small mt-2">
                    <i class="dw dw-info"></i> Each feature is planned for one week (Saturday to Friday, except last week). Click on a row for more details (future).
                </div>
            </div>
        </div>
    </div>
    <!-- End Features Table -->
    @push('scripts')
    <script>
        // Optionally, auto-expand on first visit
        $(document).ready(function(){
            // $('#featuresTableCollapse').collapse('show');
        });
    </script>
    @endpush



    @include('trades._form') <!-- Modal form -->
    @include('trades._screenshot')


@endsection

@push('scripts')
    <!-- html2pdf library for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            let table;
            
            // Check for trade_id in URL
            const urlParams = new URLSearchParams(window.location.search);
            const tradeId = urlParams.get('trade_id');

            $("#entry_pd_array_s2").select2();

            $('#add_trade_btn').click(function(e) {
                e.preventDefault();
                $('#add_trade_form')[0].reset();
                
                // Set today's date as default
                let today = new Date().toISOString().split('T')[0];
                $('[name="trade_date"]').val(today);
                
                $('#submit_trade_btn').text('Add');
                $('#add_trade_form input[name="_method"]').remove(); // remove PUT if present
                $('#add_trade_form').attr('action', '{{ route('trades.store') }}');
                $('#add_trade_modal .modal-title').text('Add Trade');
                $('#add_trade_modal').modal('show');
                $('#is_closed_checkbox_block').hide();
                $('#trade_status').attr('checked', false);
                $('#trade_hin_day').attr('checked', false);
            });

            $('#add_trade_form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                if ($('#add_trade_form input[name="rr"]').val() == '') {
                    formData.append('rr', 0.00);
                }
                if ($('#add_trade_form input[name="pnl"]').val() == '') {
                    formData.append('pnl', 0.00);
                }

                if ($('#trade_status').prop('checked') == true) {
                    if ($('#trade_outcome').val() == 'pending') {
                        iziToastNotify('error', 'Cant close a pending trade');
                        return
                    }
                    formData.append('status', 'closed');
                }

                if ($('#trade_hin_day').prop('checked') == true) {
                    if ($('input[name=news]').val() == '') {
                        iziToastNotify('error', 'Please state the High Impact News (News Context)');
                        return
                    }
                    formData.append('hin_day',1);
                }

                let entry_pds = $('#entry_pd_array_s2')
                    .val(); // array
                if (entry_pds && entry_pds.length) {
                    formData.append('entry_pd_array', JSON.stringify(entry_pds));
                }
                let form = $(this);
                let url = form.attr('action');
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#add_trade_modal').modal('hide');
                        $('#add_trade_form')[0].reset();
                        $('#add_trade_form input[name="_method"]')
                            .remove(); // reset
                        $('#add_trade_form').attr('action', '{{ route('trades.store') }}');
                        table.draw();
                        iziToastNotify('success', res.message);
                    },
                    error: function() {
                        iziToastNotify('error', "Something went wrong!");
                    }
                });
            });



            $(document).on('click', '.edit-trade', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ url('/trades') }}" + "/" + id + "/edit",
                    type: 'GET',
                    success: function(res) {
                        // Set flag to prevent filter triggering
                        isEditingTrade = true;
                        
                        // Populate fields
                        $('#trade_id').val(res.id);
                        $('[name="account_id"]').val(res.account_id || '');
                        $('[name="asset_id"]').val(res.asset_id);
                        $('[name="trade_date"]').val(res.trade_date);
                        $('[name="direction"]').val(res.direction);
                        $('[name="session"]').val(res.session);
                        $('[name="rr"]').val(res.rr);
                        $('[name="pnl"]').val(res.pnl);
                        $('[name="outcome"]').val(res.outcome);
                        $('[name="plan_followed"]').val(res.plan_followed);
                        $('[name="entry_type"]').val(res.entry_type);
                        $('[name="setup"]').val(res.setup);
                        $('[name="news"]').val(res.news);
                        $('[name="daily_log_url"]').val(res.daily_log_url);
                        $('[name="emotions"]').val(res.emotions);
                        $('[name="entry_narrative"]').val(res.entry_narrative);
                        $('[name="notes"]').val(res.notes);
                        
                        // Handle Select2 (multi-select for entry_pd_array)
                        if (res.entry_pd_array) {
                            let selected = Array.isArray(res.entry_pd_array) ? res.entry_pd_array : JSON.parse(res.entry_pd_array);
                            $('#entry_pd_array_s2').val(selected).trigger('change');
                        }

                        // Handle checkboxes and form action
                        $('#is_closed_checkbox_block').show();
                        $('#trade_status').attr('checked', res.status === 'closed' ? true : false);
                        $('#trade_hin_day').attr('checked', res.hin_day == 1 ? true : false);

                        // Change form action & add PUT method
                        $('#add_trade_form').attr('action', "{{ url('/trades/') }}" + '/' + id);
                        $('#add_trade_form').attr('method', 'POST');
                        $('#add_trade_form input[name="_method"]').remove(); // avoid duplicates
                        $('#add_trade_form').append('<input type="hidden" name="_method" value="PUT">');
                        $('#submit_trade_btn').text('Save');
                        
                        // Change modal title
                        $('#add_trade_modal .modal-title').text('Edit Trade');
                        
                        // Show modal
                        $('#add_trade_modal').modal('show');

                        // Reset flag after a short delay to allow UI updates without triggering filters
                        setTimeout(function() {
                            isEditingTrade = false;
                        }, 100);
                    },
                    error: function(err) {
                        isEditingTrade = false;
                        toastr.error("Failed to fetch trade details");
                    }
                });
            });

            $(document).on('click', '.delete-trade', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                if (!confirm('Are you sure you want to delete this trade?')) return;
                $.ajax({
                    url: "{{ url('/trades') }}" + "/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.success) {
                            iziToastNotify('success', res.message);
                            table.draw();
                        } else {
                            iziToastNotify('success', res.message);
                        }
                    },
                    error: function(err) {
                        iziToastNotify('error', "Something went wrong!");
                    }
                });
            });
            $('#filter_form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });

            // Sidebar Toggle Logic
            $('.toggle-sidebar-btn').on('click', function() {
                $('.right-sidebar').toggleClass('right-sidebar-visible');
            });

            $('[data-toggle="right-sidebar-close"]').on('click', function() {
                $('.right-sidebar').removeClass('right-sidebar-visible');
            });

            // Date Range Helper
            function setDateRange(range) {
                let now = new Date();
                let start, end;

                switch (range) {
                    case 'today':
                        start = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0);
                        end = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59);
                        break;
                    case 'yesterday':
                        let yest = new Date(now);
                        yest.setDate(now.getDate() - 1);
                        start = new Date(yest.getFullYear(), yest.getMonth(), yest.getDate(), 0, 0);
                        end = new Date(yest.getFullYear(), yest.getMonth(), yest.getDate(), 23, 59);
                        break;
                    case 'this_week':
                        let day = now.getDay() || 7; // Get current day number, converting Sun (0) to 7
                        if (day !== 1) now.setHours(-24 * (day - 1)); // Set to Monday of this week
                        start = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0);
                        end = new Date(start);
                        end.setDate(start.getDate() + 6);
                        end.setHours(23, 59, 59);
                        break;
                    case 'last_week':
                        let lastWeek = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 7);
                        let dayLast = lastWeek.getDay() || 7;
                        if (dayLast !== 1) lastWeek.setHours(-24 * (dayLast - 1));
                        start = new Date(lastWeek.getFullYear(), lastWeek.getMonth(), lastWeek.getDate(), 0, 0);
                        end = new Date(start);
                        end.setDate(start.getDate() + 6);
                        end.setHours(23, 59, 59);
                        break;
                    case 'this_month':
                        start = new Date(now.getFullYear(), now.getMonth(), 1);
                        end = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59);
                        break;
                    case 'last_month':
                        start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                        end = new Date(now.getFullYear(), now.getMonth(), 0, 23, 59, 59);
                        break;
                    case 'this_year':
                        start = new Date(now.getFullYear(), 0, 1);
                        end = new Date(now.getFullYear(), 11, 31, 23, 59, 59);
                        break;
                    case 'last_year':
                        start = new Date(now.getFullYear() - 1, 0, 1);
                        end = new Date(now.getFullYear() - 1, 11, 31, 23, 59, 59);
                        break;
                    default:
                        return;
                }

                // Update Inputs - format as dd/mm/yyyy only (no time)
                let startStr = start.toLocaleDateString('en-GB');
                let endStr = end.toLocaleDateString('en-GB');

                $('#start_date').val(startStr);
                $('#end_date').val(endStr);

                // Update Datepicker
                let dp = $('#date-picker').datepicker().data('datepicker');
                if(dp){
                    dp.selectDate([start, end]);
                }

                // Highlight active button
                $('.date-range-btn').removeClass('active');
                $(`.date-range-btn[data-range="${range}"]`).addClass('active');

                if(table) table.draw();
            }

            // Bind Buttons
            $('.date-range-btn').on('click', function() {
                let range = $(this).data('range');
                setDateRange(range);
            });

            // Hot-wire filters
            // Use a flag to prevent filter triggering during programmatic changes
            let isEditingTrade = false;
            
            $('#account_id, #market, #direction, #session, #outcome, #hin_day_filter, #entry_pd, #plan_followed, #entry_type, #has_emotions, #has_news').on('change', function() {
                if (!isEditingTrade) {
                    table.draw();
                }
            });

            // Init Datepicker
            let dp = $("#date-picker").datepicker({
                language: "en",
                range: true,
                dateFormat: 'dd/mm/yyyy',
                multipleDatesSeparator: ' - ',
                autoClose: false,
                buttons: ['today', 'clear'],
                minutesStep: 1,
                onSelect: function(formattedDate, date, inst) {
                    if (date && date.length === 2) {
                        document.getElementById('start_date').value = formattedDate.split(' - ')[0];
                        document.getElementById('end_date').value = formattedDate.split(' - ')[1];
                        $('.date-range-btn').removeClass('active'); // Clear buttons if manual select
                        if(table) table.draw(); // Trigger redraw
                    }
                }
            });


            // Default to This Month
            setDateRange('this_month');


            table = $('#trades_table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('trades.data') }}',
                    type: 'GET',
                    data: function(d) {
                        d.account_id = $('#account_id').val();
                        d.market = $('#market').val();
                        d.status = $('#outcome').val();
                        d.outcome = $('#outcome').val();
                        d.direction = $('#direction').val();
                        d.session = $('#session').val();
                        d.hin_day = $('#hin_day_filter').val();
                        d.entry_pd = $('#entry_pd').val();
                        d.plan_followed = $('#plan_followed').val();
                        d.entry_type = $('#entry_type').val();
                        d.has_emotions = $('#has_emotions').val();
                        d.has_news = $('#has_news').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d._token = '{{ csrf_token() }}';
                        // Pass trade_id if present in URL
                        const urlParams = new URLSearchParams(window.location.search);
                        const tradeIdFromUrl = urlParams.get('trade_id');
                        if (tradeIdFromUrl) {
                            d.trade_id = tradeIdFromUrl;
                        }
                        console.log('DataTables request data:', d);
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX Error:', {
                            status: xhr.status,
                            error: error,
                            thrown: thrown,
                            response: xhr.responseText
                        });
                        iziToastNotify('error', 'Failed to load trades: ' + (xhr.responseJSON?.message || error));
                    }
                },
                columns: [{
                        data: null,
                        className: 'dt-control',
                        orderable: false,
                        searchable: false,
                        defaultContent: ''
                    },
                    {
                        data: 'trade_date',
                        name: 'created_at'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'asset',
                        name: 'asset'
                    },
                    {
                        data: 'direction',
                        name: 'direction'
                    },
                    {
                        data: 'session',
                        name: 'session'
                    },
                    {
                        data: 'outcome',
                        name: 'outcome'
                    },
                    {
                        data: 'rr',
                        name: 'rr'
                    },
                    {
                        data: 'pnl',
                        name: 'pnl'
                    },
                    {
                        data: 'hin_day',
                        name: 'hin_day'
                    },
                    {
                        data: 'entry_pd_array',
                        name: 'entry_pd_array'
                    },
                    {
                        data: 'plan_followed',
                        name: 'plan_followed'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                scrollCollapse: true,
                order: [
                    [1, 'desc']
                ],
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                    targets: "datatable-nosort",
                    orderable: false
                }],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                language: {
                    info: "_START_-_END_ of _TOTAL_ entries",
                    searchPlaceholder: "Search",
                    paginate: {
                        next: '<i class="ion-chevron-right"></i>',
                        previous: '<i class="ion-chevron-left"></i>'
                    }
                },
                footerCallback: function(row, data, start, end, display) {
                    let api = this.api();
                    let intVal = function(i) {
                        return typeof i === 'string' ? i.replace(
                                /[\$,]/g, '') * 1 :
                            typeof i === 'number' ? i : 0;
                    };

                    let rrTotal = api.column(7, {
                        page: 'current'
                    }).data().reduce((a, b) => intVal(a) + intVal(
                        b), 0);
                    let pnlTotal = api.column(8, {
                        page: 'current'
                    }).data().reduce((a, b) => intVal(a) + intVal(
                        b), 0);

                    $(api.column(7).footer()).html(rrTotal.toFixed(1));
                    $(api.column(8).footer()).html(pnlTotal.toFixed(2));
                }
            });

            $('#trades_table tbody').on('click', 'td.dt-control', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);

                $('#trades_table tbody tr.shown').each(function() {
                    if (!$(this).is(tr)) {
                        let otherRow = table.row(this);
                        if (otherRow.child.isShown()) {
                            otherRow.child.hide();
                            $(this).removeClass('shown');
                        }
                    }
                });

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });

            // helpers
            function escapeHtml(unsafe) {
                if (unsafe === null || unsafe === undefined) return '—';
                return String(unsafe)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            function parseArrayField(field) {
                if (!field) return [];
                if (Array.isArray(field)) return field;
                if (typeof field === 'string') {
                    field = field.trim();
                    try {
                        let parsed = JSON.parse(field);
                        if (Array.isArray(parsed)) return parsed;
                    } catch (e) {}
                    if (field.indexOf(',') !== -1) {
                        return field.split(',').map(s => s.trim()).filter(Boolean);
                    }
                    return [field];
                }
                return [];
            }

            function parseScreenshots(raw) {
                if (!raw) return [];
                if (Array.isArray(raw)) {
                    return raw.map(item => {
                        if (typeof item === 'string') return {
                            src: item
                        };
                        return {
                            src: item.url || item.src || ''
                        };
                    }).filter(i => i.src);
                }
                if (typeof raw === 'string') {
                    try {
                        let parsed = JSON.parse(raw);
                        return parseScreenshots(parsed);
                    } catch (e) {
                        return [{
                            src: raw
                        }];
                    }
                }
                return [];
            }

            function format(d) {

                let tradeId = d.id || '';

                let emotions = parseArrayField(d.emotions || d.emotion || d
                    .emotions_list);
                let screenshots = parseScreenshots(d.screenshots || d.screenshot ||
                    d.images);
                let entryNarrative = escapeHtml(d.entry_narrative || d
                    .entryNarrative || d.entry_text);
                let notes = escapeHtml(d.notes);
                let status = d.trade_status;
                let entry_type = escapeHtml(d.entry_type);
                let news = escapeHtml(d.news);
                let journalLink = d.daily_log_url || d.url || null;
                let tradeScreenshots = d.trade_screenshots || [];

                let emotionBadges = emotions.length ?
                    emotions.map(it =>
                        `<span class="badge badge-info">${escapeHtml(it)}</span>`)
                    .join(
                        ' ') :
                    '<span class="text-muted">—</span>';

                let thumbsHtml = '';


                if (tradeScreenshots != '[]') {
                    thumbsHtml += '<div class="row screens-grid">';
                    screenshots.slice(0, 6).forEach((img, idx) => {
                        thumbsHtml += `
                        <div class="col-4 col-thumb mb-2">
                        </div>`;
                    });
                    thumbsHtml += '</div>';

                    thumbsHtml += `
                    <div class="mt-2 table-expanded-links" >
                        <button class="btn btn-sm btn-outline-primary view-screenshots"
                                data-trade-id="${escapeHtml(tradeId)}" data-trade-status="${escapeHtml(status)}" data-screenshots='${tradeScreenshots}'>
                            View Log
                        </button>
                    </div>`;
                } else {
                    thumbsHtml = '<div class="text-muted">No screenshots</div>';
                }


                return `
                <div class="container-fluid expanded-card">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-2"><strong>Entry/Execution Type:</strong><span class="text-success"> ${entry_type}</span></div>
                            <div class="mb-2"><strong>Entry Narrative:</strong> ${entryNarrative}</div>
                            <div class="mb-2"><strong>Emotions:</strong> ${emotionBadges}</div>
                            <div class="mb-2"><strong>News:</strong> ${news}</div>
                            <div class="mb-2"><strong>Notes:</strong> ${notes}</div>
                            <div class="mb-2"><strong>Journal:</strong>
                                ${journalLink ? `<a href="${escapeHtml(journalLink)}" target="_blank">${escapeHtml(journalLink)}</a>` : '—'}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Screenshots</strong>
                                <button class="btn btn-sm btn-outline-dark add_screenshot_btn"
                                    data-trade_id="${escapeHtml(tradeId)}" style="display:${escapeHtml(status == 'open' ? 'inline-flex' : 'none')}; align-items: center; gap: 4px;">
                                    <i class="dw dw-add"></i> Add
                                </button>
                            </div>
                            <div class="mt-2">${thumbsHtml}</div>
                        </div>
                    </div>
                </div>
            `;
            }

            // add screenshot modal
            $('body').on('click', '.add_screenshot_btn', function() {
                $('#add_screenshot_modal').modal('show');
                $('#tradeId').val($(this).data('trade_id') || '');
                var tradeId = $(this).data('trade_id') || '';
                $('#tvUrl').val('');
                $('#previewContainer').hide();
                $('#tradeCaption').val('');
                $('#saveScreenshotBtn').text('Add');
                $('#add_screenshot_form .modal-title').text(
                    'Add Screenshot');

                //trades/{trade}/screenshots'
                $('#add_screenshot_form').attr('action',
                    "{{ url('/trades') }}/" + $(this).data(
                        'trade_id') + "/screenshots");
                $('#add_screenshot_form').attr('method', "POST");
                $('#add_screenshot_form input[name="_method"]').remove();
                $('#deleteScreenshotBtn').hide().data('id', '').data(
                    'trade-id', '');
            });

            $('#tvUrl').on('input', function() {
                let url = $(this).val().trim();
                let match = url.match(
                    /tradingview\.com\/x\/([A-Za-z0-9]+)/);
                if (match) {
                    let snapshotId = match[1];
                    let embedUrl =
                        `https://www.tradingview.com/x/${snapshotId}/`;
                    $('#previewImage')
                        .attr('src', embedUrl)
                        .on('error', function() {
                            $('#previewContainer').hide();
                        })
                        .on('load', function() {
                            $('#previewContainer').show();
                        });
                } else {
                    $('#previewContainer').hide();
                }
            });

            $('#add_screenshot_form').on('submit', function(e) {
                e.preventDefault();
                var trade_id = $('#tradeId').val();
                var formData = $(this).serialize();
                var is_edit = $(this).find('input[name="_method"]')
                    .val() === 'PUT' ? true :
                    false;
                $.ajax({
                    url: $(this).attr('action'),
                    method: is_edit ? "PUT" : "POST",
                    data: formData,
                    success: function(response) {
                        iziToastNotify('success', response.message || 'Screenshot saved successfully');
                        
                        // Close the add/edit modal
                        $('#add_screenshot_modal').modal('hide');
                        
                        // Refresh table to get updated data
                        table.draw(false);
                        
                        // Find and refresh the expanded row if it exists
                        setTimeout(function() {
                            $('#trades_table tbody tr.shown').each(function() {
                                let row = table.row(this);
                                if (row.child.isShown()) {
                                    row.child.hide();
                                    row.child(format(row.data())).show();
                                }
                            });
                        }, 500);
                    },
                    error: function(xhr) {
                        iziToastNotify('error', xhr.responseJSON?.message || 'Failed to save screenshot');
                    }
                });
            });

            // open modal with screenshots

            function renderScreenshots(tradeScreenshots, tradeId, tradeStatus) {

                let container = $('#screenshotContainer').empty();

                if (tradeScreenshots.length < 1) {
                    container.html(
                        '<div class="text-muted text-center p-3">No screenshots available</div>'
                    );
                    $('#screenshotCount').text('');
                    return;
                }

                // Sort screenshots in ascending order by ID
                tradeScreenshots.sort((a, b) => a.id - b.id);

                // Update count
                $('#screenshotCount').text(`Total: ${tradeScreenshots.length} log${tradeScreenshots.length !== 1 ? 's' : ''}`);

                // Check if trade is closed
                let isClosed = tradeStatus === 'closed';

                tradeScreenshots.forEach((ss, index) => {

                    let src = resolveScreenshotUrl(ss.url);
                    let caption = ss.notes || '';

                    let html = `
                        <div class="screenshot-card" data-screenshot-id="${ss.id}">
                            <div class="screenshot-index">#${index + 1}</div>
                            ${!isClosed ? `
                            <div class="screenshot-actions">
                                <button class="btn btn-sm btn-primary edit-screenshot" data-id="${ss.id}" data-trade-id="${tradeId}" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-screenshot" data-id="${ss.id}" data-trade-id="${tradeId}" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            ` : ''}
                            <div class="screenshot-image-wrapper text-center" data-image-src="${src}">
                                <div class="zoom-overlay">
                                    <i class="fa fa-search-plus"></i>
                                </div>
                                <img src="${src}"
                                    class="screenshot-img"
                                    alt="Screenshot ${index + 1}"
                                    onerror="this.onerror=null;this.src='/images/broken-image.png';">
                            </div>
                            <div class="screenshot-meta">
                                <div class="screenshot-url">
                                    <strong>URL:</strong> <a href="${ss.url}" target="_blank">${ss.url}</a>
                                </div>
                                ${caption ? `<div class="screenshot-caption"><strong>Notes:</strong> ${caption}</div>` : ''}
                            </div>
                        </div>
                    `;
                    container.append(html);
                });
            }


            // Edit button
            $(document).on('click', '.edit-screenshot', function() {
                let ssId = $(this).data('id');
                let tradeId = $(this).data('trade-id');
                
                // Close screenshot modal
                $('#screenshotModal').modal('hide');
                
                // Collapse expanded rows
                $('#trades_table tbody tr.shown').each(function() {
                    let row = table.row(this);
                    if (row.child.isShown()) row.child.hide();
                    $(this).removeClass('shown');
                });
                
                // Setup form for editing
                $('#saveScreenshotBtn').text('Save');
                $('#add_screenshot_form .modal-title').text('Edit Screenshot');
                $('#add_screenshot_form').attr('action', "{{ url('/screenshots') }}" + '/' + ssId);
                $('#add_screenshot_form').append('<input type="hidden" name="_method" value="PUT">');
                $('#deleteScreenshotBtn').show().data('id', ssId).data('trade-id', tradeId);
                
                // Fetch screenshot data
                $.ajax({
                    url: "{{ url('/screenshots') }}" + '/' + ssId + '/edit',
                    type: 'GET',
                    success: function(res) {
                        $('#tradeId').val(tradeId);
                        $('#tvUrl').val(res.url || '');
                        $('#tradeCaption').val(res.notes || '');
                        $('#tvUrl').trigger('input'); // to load preview
                        
                        // Show the edit modal
                        $('#add_screenshot_modal').modal('show');
                    },
                    error: function() {
                        iziToastNotify('error', 'Failed to fetch screenshot data');
                    }
                });
            });

            // Delete button
            $(document).on('click', '.delete-screenshot', function(e) {
                e.preventDefault();
                let ssId = $(this).data('id');
                let tradeId = $(this).data('trade-id');

                if (!confirm('Are you sure you want to delete this screenshot?'))
                    return;

                $.ajax({
                    url: "{{ url('/screenshots') }}" + '/' + ssId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        iziToastNotify('success', res.message || 'Screenshot deleted successfully');
                        
                        // Close modals
                        $('#add_screenshot_modal').modal('hide');
                        $('#screenshotModal').modal('hide');
                        
                        // Refresh table
                        table.draw(false);
                        
                        // Refresh expanded rows after table redraws
                        setTimeout(function() {
                            $('#trades_table tbody tr.shown').each(function() {
                                let row = table.row(this);
                                if (row.child.isShown()) {
                                    row.child.hide();
                                    row.child(format(row.data())).show();
                                }
                            });
                        }, 500);
                        
                        // Reset form
                        $('#saveScreenshotBtn').text('Add');
                        $('#add_screenshot_form .modal-title').text('Add Screenshot');
                        $('#add_screenshot_form').attr('action', "{{ url('/screenshots') }}");
                        $('#add_screenshot_form').find('input[name="_method"]').remove();
                        $('#deleteScreenshotBtn').hide().data('id', '').data('trade-id', '');
                    },
                    error: function(xhr) {
                        iziToastNotify('error', xhr.responseJSON?.message || 'Failed to delete screenshot');
                    }
                });
            });


            // Utility: resolve TradingView URLs -> snapshot .png
            function resolveScreenshotUrl(tv_url) {

                let url = tv_url.trim();
                let match = url.match(/tradingview\.com\/x\/([A-Za-z0-9]+)/);
                if (match) {
                    let snapshotId = match[1];
                    let embedUrl = `https://www.tradingview.com/x/${snapshotId}/`;
                    return embedUrl;
                }
            }


            // Open modal with screenshots
            function openScreenshotModal(tradeScreenshots, tradeId, tradeStatus) {
                // Show loading overlay
                $('#modalLoadingOverlay').show();

                // Parse screenshots
                tradeScreenshots = JSON.parse(tradeScreenshots);

                // Show modal first
                $('#screenshotModal').modal('show');

                // Simulate async loading (or use actual AJAX if needed)
                setTimeout(function() {
                    renderScreenshots(tradeScreenshots, tradeId, tradeStatus);
                    // Hide loading overlay after rendering
                    $('#modalLoadingOverlay').fadeOut(300);
                }, 300);
            }

            // Click handler
            $(document).on('click', '.view-screenshots', function() {
                let tradeId = $(this).data('trade-id');
                let tradeStatus = $(this).data('trade-status') || 'open';
                let tradeScreenshots = [];
                try {
                    tradeScreenshots = ($(this).attr('data-screenshots') ||
                        '[]');
                } catch (e) {
                    tradeScreenshots = [];
                }
                openScreenshotModal(tradeScreenshots, tradeId, tradeStatus);
            });

            // Fullscreen toggle functionality
            $(document).on('click', '#fullscreenToggleBtn', function() {
                let modal = $('#screenshotModal');
                let icon = $('#fullscreenIcon');

                if (modal.hasClass('modal-fullscreen')) {
                    modal.removeClass('modal-fullscreen');
                    icon.removeClass('fa-compress').addClass('fa-expand');
                } else {
                    modal.addClass('modal-fullscreen');
                    icon.removeClass('fa-expand').addClass('fa-compress');
                }
            });

            // Reset fullscreen on modal close
            $('#screenshotModal').on('hidden.bs.modal', function() {
                $(this).removeClass('modal-fullscreen');
                $('#fullscreenIcon').removeClass('fa-compress').addClass('fa-expand');
                $('#modalLoadingOverlay').show(); // Reset loading for next open
            });

            // Image Fullscreen Zoom
            $(document).on('click', '.screenshot-image-wrapper', function() {
                let src = $(this).data('image-src');
                $('#fullscreenImage').attr('src', src);
                $('#imageFullscreenModal').addClass('active');
            });

            $(document).on('click', '#imageFullscreenClose, #imageFullscreenModal', function(e) {
                if (e.target !== document.getElementById('fullscreenImage')) {
                    $('#imageFullscreenModal').removeClass('active');
                    $('#fullscreenImage').attr('src', '');
                }
            });

            // Print functionality
            $(document).on('click', '#printLogsBtn', function() {
                window.print();
            });

            // Download as PDF functionality
            $(document).on('click', '#downloadPdfBtn', function() {
                // Get the modal title for filename
                let filename = 'trade_logs_' + new Date().getTime() + '.pdf';

                // Use html2pdf library if available, otherwise fallback to print
                if (typeof html2pdf !== 'undefined') {
                    let element = document.getElementById('screenshotContainer');
                    let opt = {
                        margin: 10,
                        filename: filename,
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2, useCORS: true },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    };
                    html2pdf().set(opt).from(element).save();
                } else {
                    // Fallback: open print dialog
                    iziToastNotify('info', 'Opening print dialog. You can save as PDF from there.');
                    window.print();
                }
            });






        });
    </script>
@endpush
