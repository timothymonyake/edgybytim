@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
    @push('styles')
        <link rel="stylesheet" type="text/css" href="{{ asset('deskapp/src/plugins/fullcalendar/fullcalendar.css') }}">
        <style>

        </style>
    @endpush
    <div class="page-header">
        <div class="row">
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
                    </div>
                </form>
            </div>
        </div>
    </div>




    <div class="pd-20 card-box mb-30">
        <div class="calendar-wrap">
            <div id='calendar'></div>
        </div>
    </div>
    @include('trades._form') <!-- Modal form -->


@endsection



@push('scripts')
    <script src="{{ asset('deskapp/src/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    <script>
        $(document).ready(function() {


            $("#entry_pd_array_s2").select2();

            /* var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: '{{ route('calendar.events') }}',
                eventClick: function(info) {
                    alert(
                        "Date: " + info.event.start.toLocaleDateString() + "\n" +
                        "PnL: " + info.event.extendedProps.pnl + " pips\n" +
                        "Trades: " + info.event.extendedProps.trades
                    );
                }
            });
            calendar.render(); */


            $('#calendar').fullCalendar({
                themeSystem: 'bootstrap4',
                // emphasizes business hours
                businessHours: false,
                defaultView: 'month',
                // event dragging & resizing
                editable: true,
                // header
                header: {
                    left: 'title',
                    center: 'month,agendaWeek,agendaDay',
                    right: 'today prev,next'
                },
                events: [{
                        title: 'Barber',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-05-05',
                        end: '2020-05-05',
                        className: 'fc-bg-default',
                        icon: "circle"
                    },
                    {
                        title: 'Flight Paris',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-08-08T14:00:00',
                        end: '2020-08-08T20:00:00',
                        className: 'fc-bg-deepskyblue',
                        icon: "cog",
                        allDay: false
                    },
                    {
                        title: 'Team Meeting',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-07-10T13:00:00',
                        end: '2020-07-10T16:00:00',
                        className: 'fc-bg-pinkred',
                        icon: "group",
                        allDay: false
                    },
                    {
                        title: 'Meeting',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-08-12',
                        className: 'fc-bg-lightgreen',
                        icon: "suitcase"
                    },
                    {
                        title: 'Conference',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-08-13',
                        end: '2020-08-15',
                        className: 'fc-bg-blue',
                        icon: "calendar"
                    },
                    {
                        title: 'Baby Shower',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-08-13',
                        end: '2020-08-14',
                        className: 'fc-bg-default',
                        icon: "child"
                    },
                    {
                        title: 'Birthday',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-09-13',
                        end: '2020-09-14',
                        className: 'fc-bg-default',
                        icon: "birthday-cake"
                    },
                    {
                        title: 'Restaurant',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-10-15T09:30:00',
                        end: '2020-10-15T11:45:00',
                        className: 'fc-bg-default',
                        icon: "glass",
                        allDay: false
                    },
                    {
                        title: 'Dinner',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-11-15T20:00:00',
                        end: '2020-11-15T22:30:00',
                        className: 'fc-bg-default',
                        icon: "cutlery",
                        allDay: false
                    },
                    {
                        title: 'Shooting',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-08-25',
                        end: '2020-08-25',
                        className: 'fc-bg-blue',
                        icon: "camera"
                    },
                    {
                        title: 'Go Space :)',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-12-27',
                        end: '2020-12-27',
                        className: 'fc-bg-default',
                        icon: "rocket"
                    },
                    {
                        title: 'Dentist',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu pellentesque nibh. In nisl nulla, convallis ac nulla eget, pellentesque pellentesque magna.',
                        start: '2020-12-29T11:30:00',
                        end: '2020-12-29T012:30:00',
                        className: 'fc-bg-blue',
                        icon: "medkit",
                        allDay: false
                    }
                ],
                dayClick: function() {
                    $('#add_trade_form')[0].reset();
                    $('#submit_trade_btn').text('Add');
                    $('#add_trade_form input[name="_method"]').remove(); // remove PUT if present
                    $('#add_trade_form').attr('action', '{{ route('trades.store') }}');
                    $('#add_trade_modal .modal-title').text('Add Trade');
                    $('#add_trade_modal').modal('show');
                },
                eventClick: function(event, jsEvent, view) {
                    jQuery('.event-icon').html("<i class='fa fa-" + event.icon + "'></i>");
                    jQuery('.event-title').html(event.title);
                    jQuery('.event-body').html(event.description);
                    jQuery('.eventUrl').attr('href', event.url);
                    jQuery('#modal-view-event').modal();
                },
            })

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
