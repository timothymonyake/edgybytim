@extends('layouts.app')

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
                padding: 0.5rem;
            }

            #screenshotModal .carousel-item img {
                width: 100%;
                height: auto;
                display: block;
                margin: 0 auto;
            }

            .screenshot-img {
                object-fit: cover;
                max-height: 250px;
                border-radius: 6px;
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


                        <div class="row">
                            <div class="col-md-6">
                                <!-- URL Input -->
                                <div class="form-group">
                                    <label for="tvUrl">TradingView Snapshot URL</label>
                                    <input type="url" name="url" class="form-control" id="tvUrl"
                                        placeholder="https://www.tradingview.com/x/xxxxxxx/">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- When Select -->
                                <div class="form-group">
                                    <label for="tradeWhen">When</label>
                                    <select class="form-control" name="when" id="tradeWhen">
                                        <option value="">-- Select --</option>
                                        <option value="before">Before Entry</option>
                                        <option value="during">During Entry</option>
                                        <option value="after">After Entry</option>
                                    </select>
                                </div>
                            </div>
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
                    <h5 class="modal-title">Logs</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="screenshotContainer">
                    <!-- JS will inject screenshots here -->
                </div>
                <!-- Edit/Delete buttons -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="page-header">
        <div class="row">
            {{-- <div class="col-md-3 col-sm-3">
                <div class="title">
                    <h4>Trades</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Supabase Bets</li>
                    </ol>
                </nav>
            </div> --}}
            <div class="col-md-12 col-sm-12 ">
                <form id="filter_form">
                    <div class="row">
                        <div class=" col-md-2">
                            <select class="form-control" name="market" id="market">
                                <option value="0">All Markets</option>
                            </select>
                        </div>
                        <div class=" col-md-2">
                            <select class="form-control" name="outcome" id="outcome">
                                <option value="0">All Statuses</option>
                                <option value="won">Won</option>
                                <option value="pending">Pending</option>
                                <option value="lost">Lost</option>
                            </select>
                        </div>
                        <div class=" col-md-3">
                            <input class="form-control" id="date-picker" placeholder="Select Date" type="text">
                            <input type="hidden" name="start_date" id="start_date">
                            <input type="hidden" name="end_date" id="end_date">
                        </div>

                        <div class=" col-md-1">
                            <button type="submit" class="btn btn-flat" href="#"
                                style="background: #d91072;color:#fff" role="button">
                                Filter
                            </button>
                        </div>
                        <div class=" col-md-1">
                            <button class="btn btn-outline-dark" id="add_trade_btn">
                                <i class="dw dw-add"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <div class="card-box mb-30">
        <div class="pd-20">
            <h4 class="text-blue h4">Trades</h4>
        </div>
        <div class="pb-20">
            <table id="trades_table" class="data-table table stripe hover nowrap">
                <thead>
                    <tr>
                        <th></th>
                        <th>Date</th>
                        <th>Asset</th>
                        <th>Direction</th>
                        <th>Session</th>
                        <th>Outcome</th>
                        <th>RR</th>
                        <th>PNL</th>
                        <th>Xcution</th>
                        <th>Entry PD</th>
                        <th>4l'd Plan?</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr style="background: #304d6d;color:#fff">
                        <th colspan="6"></th>
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



    @include('trades._form') <!-- Modal form -->
    @include('trades._screenshot')


@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $("#entry_pd_array_s2").select2();

            $('#add_trade_btn').click(function(e) {
                e.preventDefault();
                $('#add_trade_form')[0].reset();
                $('#submit_trade_btn').text('Add');
                $('#add_trade_form input[name="_method"]').remove(); // remove PUT if present
                $('#add_trade_form').attr('action', '{{ route('trades.store') }}');
                $('#add_trade_modal .modal-title').text('Add Trade');
                $('#add_trade_modal').modal('show');
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


            $('body').on('click', '.edit-trade', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: "{{ url('/trades/') }}" + '/' + id + '/edit',
                    type: 'GET',
                    success: function(
                        res) { // Change form action & add PUT method
                        $('#add_trade_form').attr('action', "{{ url('/trades/') }}" + '/' + id);
                        $('#add_trade_form').attr('method', 'POST');
                        $('#add_trade_form input[name="_method"]')
                            .remove(); // avoid duplicates
                        $('#add_trade_form').append(
                            '<input type="hidden" name="_method" value="PUT">');
                        $('#submit_trade_btn').text(
                            'Save'); // Populate fields
                        $('[name="asset"]').val(res.asset);
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
                        $('[name="notes"]').val(res
                            .notes); // Handle Select2 (multi-select for entry_pd_array)
                        if (res.entry_pd_array) {
                            let selected = Array.isArray(res.entry_pd_array) ? res
                                .entry_pd_array : JSON.parse(res.entry_pd_array);
                            $('#entry_pd_array_s2').val(selected).trigger('change');
                        } //change modal title
                        $('#add_trade_modal .modal-title').text('Edit Trade');
                        $('#add_trade_modal').modal(
                            'show'); // ...existing code...
                    },
                    error: function(err) {
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
                let formData = {
                    market: $('#market').val(),
                    status: $('#status').val(),
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    _token: '{{ csrf_token() }}'
                };
                table.draw();
            });

            function getTodayRange() {
                let now = new Date();
                let start = new Date(now.getFullYear(), now.getMonth(), now
                    .getDate(), 0, 0);
                let end = new Date(now.getFullYear(), now.getMonth(), now.getDate(),
                    23, 59);
                return [start, end];
            }

            let todayRange = getTodayRange();

            $("#date-picker").datepicker({
                language: "en",
                range: true,
                dateFormat: 'dd/mm/yyyy',
                multipleDatesSeparator: ' - ',
                autoClose: false,
                buttons: ['today', 'clear'],
                minutesStep: 1,
                selectedDates: todayRange,
                onSelect({
                    formattedDate,
                    date
                }) {
                    if (date.length === 2) {
                        document.getElementById('start_date').value =
                            formattedDate[0];
                        document.getElementById('end_date').value =
                            formattedDate[1];
                    }
                }
            });

            $('#start_date').val(
                todayRange[0].toLocaleDateString('en-GB') + ' ' +
                todayRange[0].toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                })
            );
            $('#end_date').val(
                todayRange[1].toLocaleDateString('en-GB') + ' ' +
                todayRange[1].toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                })
            );

            let table = $('#trades_table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('trades.data') }}',
                    type: 'GET',
                    data: function(d) {
                        d.market = $('#market').val();
                        d.status = $('#outcome').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d._token = '{{ csrf_token() }}';
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
                        name: 'trade_date'
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
                        data: 'entry_type',
                        name: 'entry_type'
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

                    let rrTotal = api.column(6, {
                        page: 'current'
                    }).data().reduce((a, b) => intVal(a) + intVal(
                        b), 0);
                    let pnlTotal = api.column(7, {
                        page: 'current'
                    }).data().reduce((a, b) => intVal(a) + intVal(
                        b), 0);

                    $(api.column(6).footer()).html(rrTotal.toFixed(1));
                    $(api.column(7).footer()).html('$ '+pnlTotal.toFixed(2));
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
                let emotions = parseArrayField(d.emotions || d.emotion || d
                    .emotions_list);
                let screenshots = parseScreenshots(d.screenshots || d.screenshot ||
                    d.images);
                let entryNarrative = escapeHtml(d.entry_narrative || d
                    .entryNarrative || d.entry_text);
                let notes = escapeHtml(d.notes);
                let news = escapeHtml(d.news);
                let journalLink = d.daily_log_url || d.url || null;
                let tradeId = d.id || '';
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
                                data-trade-id="${escapeHtml(tradeId)}" data-screenshots='${tradeScreenshots}'>
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
                            <div class="mb-2"><strong>News:</strong> ${news}</div>
                            <div class="mb-2"><strong>Journal:</strong>
                                ${journalLink ? `<a href="${escapeHtml(journalLink)}" target="_blank">${escapeHtml(journalLink)}</a>` : '—'}
                            </div>
                            <div class="mb-2"><strong>Entry Narrative:</strong> ${entryNarrative}</div>
                            <div class="mb-2"><strong>Notes:</strong> ${notes}</div>
                            <div class="mb-2"><strong>Emotions:</strong> ${emotionBadges}</div>
                        </div>
                        <div class="col-md-4">
                            <div><strong>Screenshots</strong>
                                <span class="btn btn-sm btn-outline-dark add_screenshot_btn"
                                      data-trade_id="${escapeHtml(tradeId)}" style="margin-left:10px">
                                    <i class="dw dw-add"></i>
                                </span>
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
                $('#tvUrl').val('');
                $('#previewContainer').hide();
                $('#tradeWhen').val('');
                $('#tradeCaption').val('');
                $('#saveScreenshotBtn').text('Add');
                $('#add_screenshot_form .modal-title').text(
                    'Add Screenshot');

                //trades/{trade}/screenshots'
                $('#add_screenshot_form').attr('action',
                    "{{ url('/trades') }}/" + $(this).data(
                        'trade_id') + "/screenshots");
                $('#add_screenshot_form').attr('method', "POST");
                $('#add_screenshot_form').remove(
                    '<input type="hidden" name="_method" value="PUT">');
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
                        iziToastNotify('success', response
                            .message);
                        // Refresh table
                        table.draw();
                        // Optionally, close and reopen the expanded row to refresh screenshots
                    },
                    error: function(xhr) {
                        iziToastNotify('error', xhr.responseJSON
                            .message ||
                            'Failed to add screenshot');
                    }
                });
                $('#add_screenshot_modal').modal('hide');
            });

            // open modal with screenshots

            function renderScreenshots(tradeScreenshots) {

                let container = $('#screenshotContainer').empty();

                if (tradeScreenshots.length < 1) {
                    container.html(
                        '<div class="text-muted text-center p-3">No screenshots available</div>'
                    );
                    return;
                }

                tradeScreenshots.forEach(ss => {

                    let src = resolveScreenshotUrl(ss.url);
                    let caption = ss.notes || ss.when || '';


                    let html = `
                        <div class="screenshot-actions position-absolute wd-none" style="bottom:5px; right:5px;">
                            <button class="btn btn-sm btn-outline-primary edit-screenshot mr-1" data-id="${ss.id}" data-trade-id="${tradeId}">
                                <i class="dw dw-edit-2"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-screenshot" data-id="${ss.id}" data-trade-id="${tradeId}">
                                <i class="dw dw-delete-3"></i>
                            </button>
                        </div>
                        <div class="screenshot-wrapper mb-3 text-center">
                             <img src="${src}"
                                class="screenshot-img rounded border"
                                style="width: 100%; height: auto; max-height: 90vh;"
                                onerror="this.onerror=null;this.src='/images/broken-image.png';">
                            ${caption ? `<div class="mt-2 text-muted small">${caption}</div>` : ''}

                        </div>
                    `;
                    container.append(html);
                });
            }

            //hover event - add a download button for editing screenshots
            $(document).on('mouseenter', '.screenshot-wrapper', function() {
                $(this).find('.screenshot-actions').removeClass('d-none');
            }).on('mouseleave', '.screenshot-wrapper', function() {
                $(this).find('.screenshot-actions').addClass('d-none');
            });


            // Edit button
            $(document).on('click', '.edit-screenshot', function() {
                let ssId = $(this).data('id');
                let tradeId = $(this).data('trade-id');
                $('#screenshotModal').modal('hide');
                $('#trades_table tbody tr.shown').each(function() {
                    let row = table.row(this);
                    if (row.child.isShown()) row.child.hide();
                    $(this).removeClass('shown');
                });
                $('#saveScreenshotBtn').text('Save');
                $('#add_screenshot_form .modal-title').text(
                    'Edit Screenshot');
                $('#add_screenshot_form').attr('action',
                    "{{ url('/screenshots') }}" + '/' +
                    ssId);
                $('#add_screenshot_form').append(
                    '<input type="hidden" name="_method" value="PUT">');
                $('#deleteScreenshotBtn').show().data('id', ssId).data(
                    'trade-id', tradeId);
                $.ajax({
                    url: "{{ url('/screenshots') }}" + '/' + ssId +
                        '/edit',
                    type: 'GET',
                    success: function(res) {
                        $('#tradeId').val(tradeId);
                        $('#tvUrl').val(res.url || '');
                        $('#tradeWhen').val(res.when || '');
                        $('#tradeCaption').val(res.notes || '');
                        $('#tvUrl').trigger(
                            'input'); // to load preview
                    },
                    error: function() {
                        iziToastNotify('error',
                            'Failed to fetch screenshot data'
                        );
                    }
                });
                $('#add_screenshot_modal').modal('show');
            });

            // Delete button
            $(document).on('click', '.delete-screenshot', function(e) {
                e.preventDefault();
                let ssId = $(this).data('id');
                let tradeId = $(this).data('trade-id');

                if (!confirm(
                        'Are you sure you want to delete this screenshot?'))
                    return;

                $.ajax({
                    url: "{{ url('/screenshots') }}" + '/' + ssId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        iziToastNotify('success', res.message);
                        // Refresh table
                        table.draw(false);
                        // Optionally, close and reopen the expanded row to refresh screenshots
                        $(`#trades_table tbody tr`).each(
                            function() {
                                let row = table.row(this);
                                if (row.child.isShown()) {
                                    row.child.hide();
                                    row.child(format(row
                                        .data())).show();
                                }
                            });
                        $('#add_screenshot_modal').modal(
                            'hide');
                        $('#screenshotModal').modal('hide');
                        $('#saveScreenshotBtn').text('Add');
                        $('#add_screenshot_form .modal-title')
                            .text(
                                'Add Screenshot');
                        $('#add_screenshot_form').attr('action',
                            "{{ url('/screenshots') }}");
                        $('#add_screenshot_form').remove(
                            '<input type="hidden" name="_method" value="PUT">'
                        );
                        $('#deleteScreenshotBtn').hide().data(
                            'id', '').data(
                            'trade-id', '');
                    },
                    error: function() {
                        iziToastNotify('error',
                            'Failed to delete screenshot');
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
            function openScreenshotModal(tradeScreenshots) {
                tradeScreenshots = JSON.parse(tradeScreenshots);
                renderScreenshots(tradeScreenshots);
                $('#screenshotModal').modal('show');
            }

            // Click handler
            $(document).on('click', '.view-screenshots', function() {
                let tradeId = $(this).data('trade-id');
                let tradeScreenshots = [];
                try {
                    tradeScreenshots = ($(this).attr('data-screenshots') ||
                        '[]');
                } catch (e) {
                    tradeScreenshots = [];
                }
                openScreenshotModal(tradeScreenshots);
            });






        });
    </script>
@endpush
