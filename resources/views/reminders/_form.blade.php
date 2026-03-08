<div class="modal fade" id="reminderModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Add Reminder</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{ route('reminders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input class="form-control" type="text" name="title" required placeholder="e.g. CPI Release, Check EURGBP">
                    </div>
                    <div class="form-group">
                        <label>Content (Optional)</label>
                        <textarea class="form-control" name="content" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Frequency</label>
                        <select class="form-control" name="frequency" id="freqSelect" onchange="toggleFreqOptions()">
                            <option value="once">One-off / Timed</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="custom">Custom Days</option>
                        </select>
                    </div>

                    <div id="timedOption" class="form-group">
                        <label>Remind At</label>
                        <input class="form-control daterange-single" name="remind_at" type="text" placeholder="Select Date & Time">
                    </div>

                    <div id="dailyOption" class="form-group" style="display:none;">
                        <label>Select Hours (Daily)</label>
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Morning</h6>
                                @foreach([6, 7, 8, 9, 10] as $h)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="hour-{{ $h }}" name="recurrence_days[]" value="{{ $h }}">
                                        <label class="custom-control-label" for="hour-{{ $h }}">{{ sprintf('%02d:00', $h) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-4">
                                <h6>Afternoon</h6>
                                @foreach([12, 13, 14, 15, 16] as $h)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="hour-{{ $h }}" name="recurrence_days[]" value="{{ $h }}">
                                        <label class="custom-control-label" for="hour-{{ $h }}">{{ sprintf('%02d:00', $h) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-4">
                                <h6>Evening</h6>
                                @foreach([19, 20, 21, 22] as $h)
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="hour-{{ $h }}" name="recurrence_days[]" value="{{ $h }}">
                                        <label class="custom-control-label" for="hour-{{ $h }}">{{ sprintf('%02d:00', $h) }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Repeat Until (Optional)</label>
                        <input class="form-control daterange-single" name="expires_at" type="text" placeholder="Select Expiration Date">
                        <small class="text-muted">Reminder will stop after this date.</small>
                    </div>

                    <div id="customDaysOption" class="form-group" style="display:none;">
                        <label>Select Days</label>
                        <div class="d-flex flex-wrap">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <div class="custom-control custom-checkbox mr-3 mb-2">
                                    <input type="checkbox" class="custom-control-input" id="day-{{ $day }}" name="recurrence_days[]" value="{{ $day }}">
                                    <label class="custom-control-label" for="day-{{ $day }}">{{ substr($day, 0, 3) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Reminder</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleFreqOptions() {
        const freq = document.getElementById('freqSelect').value;
        const timed = document.getElementById('timedOption');
        const custom = document.getElementById('customDaysOption');
        const daily = document.getElementById('dailyOption');

        // Reset all
        timed.style.display = 'none';
        custom.style.display = 'none';
        daily.style.display = 'none';

        if (freq === 'once') {
            timed.style.display = 'block';
        } else if (freq === 'custom') {
            custom.style.display = 'block';
        } else if (freq === 'daily') {
            daily.style.display = 'block';
        }
    }

    // Initialize daterangepicker single picker
    $(document).ready(function() {
        $('.daterange-single').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            }
        });
    });
</script>
@endpush
