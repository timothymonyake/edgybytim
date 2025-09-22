@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- <h2>Supabase Data Analysis</h2> --}}


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
                            <button type="submit" class="btn btn-primary btn-flat" href="#" role="button">
                                Filter
                            </button>
                        </div>
                        <div class=" col-md-1">
                            <button class="btn btn-success" data-toggle="modal" data-target="#tradeModal">
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
            <table id="trades-table" class="data-table table stripe hover nowrap">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Asset</th>
                        <th>Direction</th>
                        <th>Setup</th>
                        <th>Outcome</th>
                        <th>RR</th>
                        <th>Pips</th>
                        <th>Tags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>



    @include('trades._form') <!-- Modal form -->



@endsection

@push('scripts')
    <script>
        $(document).ready(function() {


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

            let table = $('#tradesTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('trades.data') }}',
                    type: 'POST', // important, Laravel needs POST for CSRF
                    data: function(d) {
                        d.market = $('#market').val();
                        d.status = $('#outcome').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d._token = '{{ csrf_token() }}';
                    }
                },
                columns: [{
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
                        data: 'setup',
                        name: 'setup'
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
                        data: 'tags',
                        name: 'tags',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
                scrollCollapse: true,
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
                }
            });


            $('#add_trade_form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route('trades.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#tradeModal').modal('hide');
                        $('#tradeForm')[0].reset();
                        table.ajax.reload();
                        toastr.success("Trade logged successfully!");
                    },
                    error: function(xhr) {
                        toastr.error("Something went wrong!");
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
                $('#bets-table').DataTable().ajax.reload(null, false);
            });

        });
    </script>
@endpush
