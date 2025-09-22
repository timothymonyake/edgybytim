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
        </style>
    @endpush



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
                            <button class="btn btn-success" id="add_trade_btn">
                                New
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
                        <th>Pips</th>
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
                        <th></th> <!-- Pips total -->
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



@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $("#entry_pd_array_s2").select2();

            $('#add_trade_btn').click(function() {
                $('#add_trade_form')[0].reset();
                $('#submit_trade_btn').text('Add');
                $('#add_trade_form input[name="_method"]').remove(); // remove PUT if present
                $('#add_trade_form').attr('action', '{{ route('trades.store') }}');
                $('#add_trade_modal .modal-title').text('Add Trade');
                $('#add_trade_modal').modal('show');

            });




            function getTodayRange() {
                let now = new Date();
                let start = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0);
                let end = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59);
                return [start, end];
            }

            let todayRange = getTodayRange();


            $("#date-picker").datepicker({
                language: "en",
                range: true, // enable range mode
                // timepicker: true, // enable time
                // timeFormat: 'hh:mm', // 24-hour format
                dateFormat: 'dd/mm/yyyy', // desired output
                multipleDatesSeparator: ' - ', // separator between dates
                autoClose: false,
                buttons: ['today', 'clear'],
                minutesStep: 1,
                selectedDates: todayRange,
                onSelect({
                    formattedDate,
                    date
                }) {
                    if (date.length === 2) {
                        document.getElementById('start_date').value = formattedDate[0];
                        document.getElementById('end_date').value = formattedDate[1];
                    }
                }
            });


            $('#start_date').val(
                todayRange[0].toLocaleDateString('en-GB') + ' ' + todayRange[0].toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                })
            );
            $('#end_date').val(
                todayRange[1].toLocaleDateString('en-GB') + ' ' + todayRange[1].toLocaleTimeString('en-GB', {
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
                    type: 'GET', // important, Laravel needs POST for CSRF
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
                        data: 'pips',
                        name: 'pips'
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
                    orderable: false,
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

                    // Helper function to parse numbers
                    let intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i :
                            0;
                    };

                    // Total for RR
                    let rrTotal = api
                        .column(6, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Total for Pips
                    let pipsTotal = api
                        .column(7, {
                            page: 'current'
                        })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Update footer
                    $(api.column(6).footer()).html(rrTotal.toFixed(2));
                    $(api.column(7).footer()).html(pipsTotal.toFixed(1));
                }
            });


            /* function format(d) {
                                        // `d` is the row data object from DataTables
                                        return `
            <div class="p-2">
                <strong>Notes:</strong> ${d.notes ?? '—'} <br>
                <strong>Emotions:</strong> ${d.emotions ?? '—'} <br>
                <strong>Entry Narrative:</strong> ${d.entry_narrative ?? '—'} <br>
                <strong>News:</strong> ${d.news ?? '—'} <br>
                <a href="${d.journal_link ?? '#'}" target="_blank">Journal Link</a>
            </div>
        `;
                                    } */

            $('#trades_table tbody').on('click', 'td.dt-control', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });

            $('#add_trade_form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                if ($('#add_trade_form input[name="rr"]').val() == '') {
                    formData.append('rr', 0.00);
                }
                if ($('#add_trade_form input[name="pips"]').val() == '') {
                    formData.append('pips', 0.00);
                }
                let entry_pds = $('#entry_pd_array_s2').val(); // array

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
                        $('#add_trade_form input[name="_method"]').remove(); // reset
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
                    success: function(res) {
                        // Change form action & add PUT method
                        $('#add_trade_form').attr('action', "{{ url('/trades/') }}" + '/' + id);
                        $('#add_trade_form').attr('method', 'POST');
                        $('#add_trade_form input[name="_method"]').remove(); // avoid duplicates
                        $('#add_trade_form').append(
                            '<input type="hidden" name="_method" value="PUT">');

                        $('#submit_trade_btn').text('Save');


                        // Populate fields
                        $('[name="asset"]').val(res.asset);
                        $('[name="trade_date"]').val(res.trade_date);
                        $('[name="direction"]').val(res.direction);
                        $('[name="session"]').val(res.session);
                        $('[name="rr"]').val(res.rr);
                        $('[name="pips"]').val(res.pips);
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
                            let selected = Array.isArray(res.entry_pd_array) ? res
                                .entry_pd_array : JSON.parse(res.entry_pd_array);
                            $('#entry_pd_array_s2').val(selected).trigger('change');
                        }
                        //change modal title
                        $('#add_trade_modal .modal-title').text('Edit Trade');
                        $('#add_trade_modal').modal('show');
                        // ...existing code...
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

            /* HERE */
            // helper: escape text for safe HTML
            function escapeHtml(unsafe) {
                if (unsafe === null || unsafe === undefined) return '—';
                return String(unsafe)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // helper: normalize an "array-like" field (JSON string, comma list or array)
            function parseArrayField(field) {
                if (!field) return [];
                if (Array.isArray(field)) return field;
                if (typeof field === 'string') {
                    field = field.trim();
                    // JSON
                    try {
                        let parsed = JSON.parse(field);
                        if (Array.isArray(parsed)) return parsed;
                    } catch (e) {}
                    // comma-separated
                    if (field.indexOf(',') !== -1) {
                        return field.split(',').map(s => s.trim()).filter(Boolean);
                    }
                    // single item string
                    return [field];
                }
                return [];
            }

            // helper: normalize screenshots into an array of objects {src, type, id}
            function parseScreenshots(raw) {
                if (!raw) return [];
                if (Array.isArray(raw)) {
                    return raw.map(item => {
                        if (typeof item === 'string') return {
                            src: resolveSrc(item),
                            type: null,
                            id: null
                        };
                        // assume object with file_path/url
                        let src = item.url || item.file_path || item.src || '';
                        return {
                            src: resolveSrc(src),
                            type: item.type || null,
                            id: item.id || null
                        };
                    }).filter(i => i.src);
                }
                // string case: JSON or single URL
                if (typeof raw === 'string') {
                    try {
                        let parsed = JSON.parse(raw);
                        return parseScreenshots(parsed);
                    } catch (e) {
                        return [{
                            src: resolveSrc(raw),
                            type: null,
                            id: null
                        }];
                    }
                }
                return [];
            }

            // small helper to convert storage filepaths to usable URLs
            function resolveSrc(path) {
                if (!path) return '';
                if (path.startsWith('http://') || path.startsWith('https://')) return path;
                // if you store screenshots in Laravel storage (public disk), prefix /storage/
                // if your API already returns full urls, remove this transform.
                path = path.replace(/^\/+/, '');
                if (path.startsWith('storage/')) return '/' + path;
                // heuristic: files in "screenshots/..." -> /storage/screenshots/...
                return '/' + path;
            }

            /* format() - returns HTML to show in DataTables child row.
               Uses Bootstrap layout to show left info and right thumbnails.
               'd' is the row data object coming from server.
            */
            function format(d) {
                // parse and safe values
                let setupItems = escapeHtml(d.setup);
                let emotions = parseArrayField(d.emotions || d.emotion || d.emotions_list);
                let screenshots = parseScreenshots(d.screenshots || d.screenshot || d.images);
                let entryNarrative = escapeHtml(d.entry_narrative || d.entryNarrative || d.entry_text);
                let notes = escapeHtml(d.notes);
                let dailyLog = escapeHtml(d.daily_log || d.dailyLog || d.daily);
                let news = escapeHtml(d.news);
                let journalLink = d.daily_log_url || d.daily_log_url || d.url || null;
                let tradeId = d.id || d.trade_id || '';

                // badges for setup
                // badges for emotions (use info color)
                let emotionBadges = emotions.length ? emotions.map(it =>
                        `<span class="badge badge-info">${escapeHtml(it)}</span>`).join(' ') :
                    '<span class="text-muted">—</span>';

                // screenshots markup (show up to 4 thumbs)
                let thumbsHtml = '';
                if (screenshots.length) {
                    thumbsHtml += '<div class="row screens-grid">';
                    screenshots.slice(0, 6).forEach((img, idx) => {
                        thumbsHtml += `
                <div class="col-4 col-thumb">
                <img src="${escapeHtml(img.src)}" data-trade-id="${escapeHtml(tradeId)}" data-index="${idx}" class="screenshot-thumb" alt="screenshot ${idx+1}">
                </div>`;
                    });
                    thumbsHtml += '</div>';

                    // "view all" / mini page link
                    thumbsHtml += `
                <div class="mt-2 table-expanded-links">
                    <button class="btn btn-sm btn-outline-primary mr-2 view-screenshots" data-trade-id="${escapeHtml(tradeId)}">View gallery</button>
                    <a class="btn btn-sm btn-primary" href="/trades/${escapeHtml(tradeId)}/screenshots" target="_blank">Open mini page</a>
                </div>`;
                } else {
                    thumbsHtml = '<div class="text-muted">No screenshots</div>';
                }

                // assemble HTML with Bootstrap grid: left (8) info, right (4) thumbs
                return `
                    <div class="dataTables_child_row">
                    <div class="container-fluid expanded-card">
                        <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-2"><span class="small-label">Emotions:</span> ${emotionBadges}</div>
                                    <div class="mb-2"><span class="small-label">News</span> <div class="note-text mt-1">${news}</div></div>
                                    <div class="mb-2">
                                    <span class="small-label">Journal:</span>
                                    ${journalLink ? `<a href="${escapeHtml(journalLink)}" target="_blank" class="ml-1">${escapeHtml(journalLink)}</a>` : '<span class="text-muted ml-1">—</span>'}
                                    </div>
                                </div>
                                <div class="col-md-7">
                                     <div class="mb-2"><span class="small-label">Setup Narrative</span><div class="note-text mt-1">${setupItems}</div></div>
                                     <div class="mb-2"><span class="small-label">Entry Narrative</span><div class="note-text mt-1">${entryNarrative}</div></div>
                                     <div class="mb-2"><span class="small-label">Notes</span><div class="note-text mt-1">${notes}</div></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div><strong>Screenshots</strong></div>
                            <div class="mt-2">${thumbsHtml}</div>
                        </div>
                        </div>
                    </div>
                    </div>
                `;
            }

            // --------------- screenshot modal / gallery handler ---------------
            // click handler: clicking a thumbnail or "view gallery" button
            $(document).on('click', '.screenshot-thumb', function() {
                let tradeId = $(this).data('trade-id');
                let idx = parseInt($(this).data('index') || 0, 10);
                // get screenshots list from row data in DataTable if available
                let row = $('#trades_table').DataTable().row($(this).closest('tr')
                    .prev()); // child is after parent; getting parent row could be tricky
                // Instead: get screenshots from the clicked image src / or request server for trade screenshots
                let all = [];
                // easiest: build from DOM thumbnails in the same container
                $(this).closest('.screens-grid').find('.screenshot-thumb').each(function() {
                    all.push({
                        src: $(this).attr('src')
                    });
                });
                openScreenshotModal(all, idx, $(this).data('trade-id'));
            });

            // click handler for "View gallery" button: fetch all screenshots from server OR read DOM
            $(document).on('click', '.view-screenshots', function() {
                let tradeId = $(this).data('trade-id');
                // if you have an endpoint that returns all screenshots for a trade: /trades/{id}/screenshots/json
                // fallback: read thumbnails in the same child row:
                let container = $(this).closest('.dataTables_child_row');
                let all = [];
                container.find('.screenshot-thumb').each(function() {
                    all.push({
                        src: $(this).attr('src')
                    });
                });
                openScreenshotModal(all, 0, tradeId);
            });

            function openScreenshotModal(screenshots, startIndex = 0, tradeId = '') {
                let indicators = $('#screenshotIndicators').empty();
                let inner = $('#screenshotInner').empty();

                screenshots.forEach(function(img, i) {
                    let active = (i === startIndex) ? 'active' : '';
                    indicators.append(
                        `<li data-target="#screenshotCarousel" data-slide-to="${i}" class="${active}"></li>`
                    );
                    inner.append(`
                    <div class="carousel-item ${active}">
                        <img src="${escapeHtml(img.src)}" alt="screenshot ${i+1}">
                    </div>
                    `);
                });

                // update mini page link
                if (tradeId) {
                    $('#screenshotMiniPageLink').attr('href', `/trades/${tradeId}/screenshots`);
                } else {
                    $('#screenshotMiniPageLink').attr('href', '#');
                }

                if (!screenshots.length) {
                    $('#screenshotInner').html('<div class="p-4 text-center text-muted">No screenshots</div>');
                    $('#screenshotIndicators').empty();
                }

                $('#screenshotModal').modal('show');
                // move carousel to chosen slide after modal shown
                $('#screenshotModal').on('shown.bs.modal.once', function() {
                    $('#screenshotCarousel').carousel(startIndex);
                });
            }


        });
    </script>
@endpush
