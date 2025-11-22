@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
    @push('styles')
        <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/src/plugins/fullcalendar/fullcalendar.css') }}">
        <style>
            .fc-day {
                position: relative;
            }

            .fc-left h2 {
                font-size: 25px
            }

            .fc-bg-event {
                opacity: 1;
            }

            .fc-bgevent {
                opacity: 0.9;
                padding: 5px;
                border: 10px solid #fff
            }

            .fc-day-number {
                font-size: 14px;
                margin-right: 5px
            }

            .fc-day-grid-event {
                font-weight: bold;
                font-size: 18px;
                text-align: center;
                line-height: 1.3;
                padding: 0;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            /* Ensure the event container takes full height */
            .fc-row .fc-content-skeleton td,
            .fc-row .fc-helper-skeleton td {
                border-color: transparent;
            }
            
            .fc-content-skeleton .fc-day-grid-event {
                margin: 0; /* Remove default margins */
            }
            
            /* Force rows to have sufficient height for centering */
            .fc-basic-view .fc-body .fc-row {
                min-height: 120px;
            }

            .fc-day-grid-event .net_profit {
                font-size: 20px;
                margin-bottom: 5px
            }

            .fc-day-grid-event .trade_count {
                font-size: 14px;
                opacity: 0.85;
            }

            .fc-day-grid-event .win_rate {
                font-size: 14px;
                color: #ffc107;
            }

            .month-summary-card {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 2rem;
                background: #1b1f24;
                border-radius: 8px;
                padding: 6px 16px;
                color: #fff;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
                font-size: 15px;
                margin-top: 6px;
            }

            .month-summary-card .summary-item {
                text-align: center;
            }

            .month-summary-card .label {
                display: block;
                font-size: 12px;
                opacity: 0.7;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .month-summary-card .value {
                display: block;
                font-size: 17px;
                font-weight: 600;
                margin-top: 2px;
            }

            .text-profit {
                color: #00e676 !important;
            }

            .text-loss {
                color: #ff5252 !important;
            }

            .fc-center {
                margin-bottom: -10px;
                margin-top: -20px;
            }

            /* Weekly Summary Styles injected into Saturday cells */
            .week-summary-cell {
                height: 100%;
                min-height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                background-color: #1c3a53; /* Updated background color */
                color: #fff;
                border-left: 1px solid #444;
                padding: 5px;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
            }

            .week-summary-cell .week-pnl {
                font-size: 16px;
                font-weight: 700;
                margin-bottom: 4px;
            }

            .week-summary-cell .week-pnl.positive {
                color: #00e676;
            }

            .week-summary-cell .week-pnl.negative {
                color: #ff5252;
            }

            .week-summary-cell .week-trades {
                font-size: 12px;
                color: #ccc;
                margin-bottom: 3px;
            }

            .week-summary-cell .week-wr {
                font-size: 12px;
                color: #ffc107;
                font-weight: 600;
            }
            
            /* Hide Saturday date number */
            .fc-day-top.fc-sat .fc-day-number {
                display: none;
            }
            
            /* Style the Saturday header */
            .fc-day-header.fc-sat {
                background-color: #1c3a53;
                color: #fff;
            }
            
            /* Style Saturday background cells */
            .fc-bg .fc-sat {
                background-color: #1c3a53;
            }
            
            /* Fix vertical centering for all events */
            .fc-row .fc-content-skeleton {
                height: 100%;
                padding-bottom: 0 !important;
            }
            
            .fc-row .fc-content-skeleton table {
                height: 100%;
            }
            
            .fc-content-skeleton td {
                position: relative;
                height: 100%;
                vertical-align: middle;
            }
            
            .fc-day-grid-event {
                position: relative !important; /* Changed from absolute to relative */
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important; /* Ensure no padding affects alignment */
                display: flex !important;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                background: transparent !important; /* Ensure no background covers the cell color */
            }
            
            /* Ensure the link/container doesn't collapse */
            .fc-event-container {
                height: 100%;
            }
        </style>
    @endpush

    <div class="pd-20 card-box mb-30">
        <div id="calendar-month-summary" class="text-center mb-3"></div>
        <div class="calendar-wrap">
            <div id="calendar"></div>
        </div>
    </div>

    @include('trades._form') <!-- Modal form -->

@endsection

@push('scripts')
    <script src="{{ asset('deskapp/src/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#entry_pd_array_s2").select2();

            $('#calendar').fullCalendar({
                themeSystem: 'bootstrap4',
                defaultView: 'month',
                header: {
                    left: 'title',
                    right: 'today prev,next'
                },
                events: function(start, end, timezone, callback) {
                    $.ajax({
                        url: '{{ route('calendar.events') }}',
                        type: 'GET',
                        success: function(response) {
                            // store monthly and weekly summary globally
                            window.monthlyData = response.monthly_summary || {};
                            window.weeklyData = response.weekly_summary || {};
                            callback(response.events || []);
                            updateMonthSummary();
                            updateWeeklySummaries();
                        },
                        error: function() {
                            alert('There was an error while fetching events!');
                            callback([]);
                        }
                    });
                },
                eventRender: function(event, element) {
                    // Render rich HTML titles from backend
                    element.find('.fc-title').html(event.title);
                },
                eventAfterAllRender: function(view) {
                    // Update weekly summaries after calendar is fully rendered
                    updateWeeklySummaries();
                },
                viewRender: function(view) {
                    // Trigger when month changes
                    updateMonthSummary();
                    setTimeout(updateWeeklySummaries, 100);
                },
                dayClick: function(date) {
                    // Prevent clicking on Saturday (Summary column)
                    if (date.day() === 6) return;

                    $('#add_trade_form')[0].reset();
                    $('#submit_trade_btn').text('Add');
                    $('#add_trade_form input[name="_method"]').remove();
                    $('#add_trade_form').attr('action', '{{ route('trades.store') }}');
                    $('#add_trade_modal .modal-title').text('Add Trade');
                    
                    // Pre-fill the date
                    $('#add_trade_form input[name="trade_date"]').val(date.format('YYYY-MM-DD'));
                    
                    $('#add_trade_modal').modal('show');
                }
            });

            function updateMonthSummary() {
                if (!window.monthlyData) return;

                let view = $('#calendar').fullCalendar('getView');
                let monthKey = moment(view.title, 'MMMM YYYY').format('YYYY-MM');
                let summary = window.monthlyData[monthKey];

                if (summary) {
                    $('.fc-center').html(`
                        <div class="month-summary-card d-flex justify-content-center align-items-center gap-4">
                            <div class="summary-item">
                                <span class="label">Net P&L</span>
                                <span class="value ${summary.net_pnl >= 0 ? 'text-profit' : 'text-loss'}">
                                    ${summary.net_pnl >= 0 ? '+' : ''}${summary.net_pnl.toFixed(2)}
                                </span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Trades</span>
                                <span class="value">${summary.trades}</span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Win Rate</span>
                                <span class="value ${summary.win_rate >= 50 ? 'text-profit' : 'text-loss'}">
                                    ${summary.win_rate.toFixed(1)}%
                                </span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Total R:R</span>
                                <span class="value ${summary.total_rr >= 0 ? 'text-profit' : 'text-loss'}">
                                    ${summary.total_rr >= 0 ? '+' : ''}${summary.total_rr.toFixed(2)}
                                </span>
                            </div>
                        </div>
                    `);
                } else {
                    $('.fc-center').html(
                        '<div class="month-summary-card empty">No data for this month</div>');
                }
            }

            function updateWeeklySummaries() {
                // Change Saturday header to "Summary"
                $('.fc-day-header.fc-sat').text('Summary');

                if (!window.weeklyData) return;

                // Iterate over each week row
                $('.fc-row.fc-week').each(function() {
                    let $row = $(this);
                    
                    // Find the Saturday header cell to determine index
                    let $satHeader = $row.find('.fc-content-skeleton thead td.fc-sat');
                    let colIndex = $satHeader.index();
                    
                    // Find the corresponding cell in the tbody (where events are)
                    let $satBodyCell = $row.find('.fc-content-skeleton tbody tr:first td').eq(colIndex);
                    
                    // If we can't find it, fallback
                    if ($satBodyCell.length === 0) return;

                    // Clear existing content
                    $satBodyCell.empty();
                    
                    // Determine the week based on the first day of this row
                    let $firstDay = $row.find('.fc-bg .fc-day').first();
                    let firstDate = $firstDay.data('date');
                    if (!firstDate) return;
                    
                    let dateMoment = moment(firstDate);
                    let weekSummary = null;
                    
                    for (let key in window.weeklyData) {
                        let weekData = window.weeklyData[key];
                        let weekStart = moment(weekData.week_start);
                        
                        // Check if this date falls within this week
                        if (dateMoment.isSame(weekStart, 'week')) {
                            weekSummary = weekData;
                            break;
                        }
                    }
                    
                    let summaryHtml = '';
                    if (weekSummary && weekSummary.trades > 0) {
                        let pnlClass = weekSummary.pnl >= 0 ? 'positive' : 'negative';
                        let pnlSign = weekSummary.pnl >= 0 ? '+' : '';
                        
                        summaryHtml = `
                            <div class="week-summary-cell">
                                <div class="week-pnl ${pnlClass}">${pnlSign}${weekSummary.pnl.toFixed(2)}</div>
                                <div class="week-trades">${weekSummary.trades} Trade${weekSummary.trades > 1 ? 's' : ''}</div>
                                <div class="week-wr">WR: ${weekSummary.win_rate.toFixed(1)}%</div>
                            </div>
                        `;
                    } else {
                        summaryHtml = `
                            <div class="week-summary-cell">
                                <div class="week-pnl">-</div>
                            </div>
                        `;
                    }
                    
                    // Inject into the body cell
                    $satBodyCell.html(summaryHtml);
                });
            }

            $('#add_trade_form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                if ($('#add_trade_form input[name="rr"]').val() == '') {
                    formData.append('rr', 0.00);
                }
                if ($('#add_trade_form input[name="pips"]').val() == '') {
                    formData.append('pips', 0.00);
                }
                let entry_pds = $('#entry_pd_array_s2').val();

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
                        $('#add_trade_form input[name="_method"]').remove();
                        $('#add_trade_form').attr('action', '{{ route('trades.store') }}');
                        iziToastNotify('success', res.message);
                        window.setTimeout(() => {
                            location.reload();
                        }, 3000);
                    },
                    error: function() {
                        iziToastNotify('error', "Something went wrong!");
                    }
                });
            });
        });
    </script>
@endpush
