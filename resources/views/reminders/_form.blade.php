<div class="modal fade" id="reminderModal" tabindex="-1" role="dialog" aria-labelledby="reminderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content reminder-modal-dark">
            <div class="modal-header reminder-modal-header">
                <h5 class="modal-title" id="reminderModalLabel">
                    <span class="modal-title-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    </span>
                    <span>Add Trading Reminder</span>
                </h5>
                <button type="button" class="close reminder-modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('reminders.store') }}" method="POST">
                @csrf
                <div class="modal-body reminder-modal-body">
                    <!-- Title -->
                    <div class="form-group mb-3">
                        <label class="reminder-form-label">Rule / Reminder Title <span style="color:#ef4444;">*</span></label>
                        <input class="form-control reminder-form-input" type="text" name="title" required placeholder="e.g. Confirmed 1R > unconfirmed 2R...Use $ to validate">
                    </div>

                    <!-- Notes -->
                    <div class="form-group mb-3">
                        <label class="reminder-form-label">Notes / Execution Context (Optional)</label>
                        <textarea class="form-control reminder-form-input" name="content" rows="2" placeholder="Add specific triggers, invalidations, or rules..."></textarea>
                    </div>
                    
                    <!-- Frequency Selection -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="reminder-form-label">Frequency / Schedule</label>
                                <select class="form-control reminder-form-input" name="frequency" id="freqSelect" onchange="toggleFreqOptions()">
                                    <option value="daily" selected>Recurring (Everyday)</option>
                                    <option value="once">One-off (Specific Date & Time)</option>
                                    <option value="custom">Trading Days (Mon - Fri)</option>
                                    <option value="weekly">Weekly (Specific Day)</option>
                                    <option value="custom_days">Custom Days</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="reminder-form-label">Repeat Until (Optional)</label>
                                <input class="form-control reminder-form-input reminder-native-picker" type="date" name="expires_at" id="expiresAtInput">
                            </div>
                        </div>
                    </div>

                    <!-- Everyday Multiple Alert Times (Default) -->
                    <div id="dailyOption" class="form-group mb-3">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <label class="reminder-form-label mb-0">Everyday Alert Times (Multiple)</label>
                            <span style="font-size:10.5px; color:#64748b;" id="activeTimesCount">1 time selected</span>
                        </div>

                        <!-- Active Selected Times Container -->
                        <div id="selectedTimesContainer" class="selected-times-box">
                            <!-- Injected by JavaScript -->
                        </div>

                        <!-- Add Custom Time Row -->
                        <div style="display:flex; gap:8px; align-items:center; margin-top:8px; margin-bottom:10px;">
                            <input class="form-control reminder-form-input reminder-native-picker" type="time" id="addTimePicker" value="14:30" style="max-width:140px; padding:6px 10px;">
                            <button type="button" class="btn reminder-btn-add-time" onclick="addTimeFromInput()">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>Add Time</span>
                            </button>
                        </div>
                        
                        <!-- Quick Session Presets -->
                        <div style="font-size:10.5px; color:#94a3b8; margin-bottom:5px; font-weight:600;">1-Click Session Presets:</div>
                        <div class="reminder-preset-bar">
                            <button type="button" class="reminder-preset-btn" onclick="togglePresetTime('06:00')">+ 06:00 (Pre-London)</button>
                            <button type="button" class="reminder-preset-btn" onclick="togglePresetTime('08:00')">+ 08:00 (London)</button>
                            <button type="button" class="reminder-preset-btn" onclick="togglePresetTime('13:30')">+ 13:30 (NY Pre)</button>
                            <button type="button" class="reminder-preset-btn" onclick="togglePresetTime('14:30')">+ 14:30 (NY Open)</button>
                            <button type="button" class="reminder-preset-btn" onclick="togglePresetTime('20:00')">+ 20:00 (Review)</button>
                            <button type="button" class="reminder-preset-btn" style="color:#ef4444;" onclick="clearAllTimes()">Clear All</button>
                        </div>
                    </div>

                    <!-- One-off Date & Time Option -->
                    <div id="timedOption" class="form-group mb-3" style="display:none;">
                        <label class="reminder-form-label">Remind At Date & Time</label>
                        <input class="form-control reminder-form-input reminder-native-picker" type="datetime-local" name="remind_at" id="remindAtInput" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>

                    <!-- Custom Days Selector -->
                    <div id="customDaysOption" class="form-group mb-3" style="display:none;">
                        <label class="reminder-form-label">Select Active Days</label>
                        <div class="reminder-chip-grid">
                            @foreach(['Monday' => 'Mon', 'Tuesday' => 'Tue', 'Wednesday' => 'Wed', 'Thursday' => 'Thu', 'Friday' => 'Fri', 'Saturday' => 'Sat', 'Sunday' => 'Sun'] as $day => $short)
                                <label class="reminder-chip-label">
                                    <input type="checkbox" name="recurrence_days[]" value="{{ $day }}" class="custom-day-chk" {{ in_array($day, ['Monday','Tuesday','Wednesday','Thursday','Friday']) ? 'checked' : '' }}>
                                    <span>{{ $short }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer reminder-modal-footer">
                    <button type="button" class="btn reminder-btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn reminder-btn-primary">Save Reminder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ── TRADEZELLA-GRADE REMINDER MODAL ── */
.reminder-modal-dark {
    background: #121620 !important;
    border: 1px solid #232d3f !important;
    border-radius: 14px !important;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75), 0 0 25px rgba(56, 189, 248, 0.08) !important;
    color: #e2e8f0 !important;
    overflow: hidden;
}
.reminder-modal-header {
    background: #0e121a !important;
    border-bottom: 1px solid #232d3f !important;
    padding: 14px 20px !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.reminder-modal-header .modal-title {
    font-family: 'Outfit', sans-serif !important;
    font-weight: 700 !important;
    font-size: 15px !important;
    color: #ffffff !important;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.modal-title-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 7px;
    background: rgba(56, 189, 248, 0.12);
    color: #38bdf8;
    border: 1px solid rgba(56, 189, 248, 0.25);
}
.reminder-modal-close {
    background: transparent !important;
    border: none !important;
    color: #94a3b8 !important;
    font-size: 24px !important;
    line-height: 1 !important;
    opacity: 0.8 !important;
    padding: 0 !important;
    margin: 0 !important;
    cursor: pointer !important;
    transition: color 0.15s ease;
}
.reminder-modal-close:hover {
    color: #ffffff !important;
    opacity: 1 !important;
}
.reminder-modal-body {
    background: #121620 !important;
    padding: 20px !important;
}
.reminder-form-label {
    display: block;
    font-family: 'Inter', sans-serif;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #94a3b8 !important;
    margin-bottom: 6px;
}
.reminder-form-input {
    background: #1a202c !important;
    border: 1px solid #2d3748 !important;
    border-radius: 8px !important;
    color: #ffffff !important;
    font-size: 13px !important;
    font-family: 'Inter', sans-serif;
    padding: 9px 12px !important;
    height: auto !important;
    transition: all 0.2s ease;
    width: 100%;
}
.reminder-form-input:focus {
    background: #1e2638 !important;
    border-color: #38bdf8 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.18) !important;
    outline: none !important;
}
.reminder-form-input::placeholder {
    color: #64748b !important;
}
select.reminder-form-input option {
    background: #1a202c;
    color: #ffffff;
}

/* Native dark mode date & time picker styling */
.reminder-native-picker {
    color-scheme: dark !important;
    cursor: pointer;
}
.reminder-native-picker::-webkit-calendar-picker-indicator {
    filter: invert(0.8) brightness(1.2);
    cursor: pointer;
}

/* Selected Multiple Times Box */
.selected-times-box {
    background: #0b0f17;
    border: 1px solid #232d3f;
    border-radius: 8px;
    padding: 8px 10px;
    min-height: 42px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}
.time-tag-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(56, 189, 248, 0.12);
    border: 1px solid rgba(56, 189, 248, 0.3);
    color: #38bdf8;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}
.time-tag-remove {
    cursor: pointer;
    color: #94a3b8;
    font-size: 14px;
    font-weight: 400;
    line-height: 1;
    transition: color 0.15s ease;
}
.time-tag-remove:hover {
    color: #ef4444;
}
.empty-times-placeholder {
    font-size: 11.5px;
    color: #64748b;
    font-style: italic;
}

/* Add time button */
.reminder-btn-add-time {
    background: #1e293b !important;
    border: 1px solid #38bdf8 !important;
    color: #38bdf8 !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    padding: 6px 14px !important;
    border-radius: 7px !important;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.reminder-btn-add-time:hover {
    background: #38bdf8 !important;
    color: #0b0d12 !important;
}

/* Presets bar */
.reminder-preset-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.reminder-preset-btn {
    background: #1a202c;
    border: 1px solid #2d3748;
    color: #cbd5e1;
    font-size: 10.5px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.reminder-preset-btn:hover {
    background: #232d3f;
    border-color: #38bdf8;
    color: #38bdf8;
}

/* Chip grid */
.reminder-chip-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 4px;
}
.reminder-chip-label {
    cursor: pointer;
    margin: 0;
}
.reminder-chip-label input {
    display: none;
}
.reminder-chip-label span {
    display: inline-block;
    padding: 4px 10px;
    font-size: 11.5px;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
    color: #94a3b8;
    background: #1a202c;
    border: 1px solid #2d3748;
    border-radius: 6px;
    transition: all 0.15s ease;
}
.reminder-chip-label:hover span {
    border-color: #475569;
    color: #e2e8f0;
}
.reminder-chip-label input:checked + span {
    background: rgba(56, 189, 248, 0.15);
    border-color: #38bdf8;
    color: #38bdf8;
    box-shadow: 0 0 10px rgba(56, 189, 248, 0.2);
}

.reminder-modal-footer {
    background: #0e121a !important;
    border-top: 1px solid #232d3f !important;
    padding: 12px 20px !important;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}
.reminder-btn-secondary {
    background: #1e293b !important;
    border: 1px solid #334155 !important;
    color: #94a3b8 !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 7px 16px !important;
    border-radius: 7px !important;
    transition: all 0.15s ease;
}
.reminder-btn-secondary:hover {
    background: #283548 !important;
    color: #ffffff !important;
    border-color: #475569 !important;
}
.reminder-btn-primary {
    background: #1b00ff !important;
    border: 1px solid #1b00ff !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    padding: 7px 18px !important;
    border-radius: 7px !important;
    box-shadow: 0 4px 14px rgba(27, 0, 255, 0.4) !important;
    transition: all 0.2s ease;
}
.reminder-btn-primary:hover {
    background: #1400d4 !important;
    box-shadow: 0 6px 18px rgba(27, 0, 255, 0.6) !important;
    transform: translateY(-1px);
}
</style>

@push('scripts')
<script>
    let selectedDailyTimes = ['08:00'];

    function renderSelectedTimes() {
        const container = document.getElementById('selectedTimesContainer');
        const countBadge = document.getElementById('activeTimesCount');
        if (!container) return;

        container.innerHTML = '';
        if (selectedDailyTimes.length === 0) {
            container.innerHTML = '<span class="empty-times-placeholder">No specific times set (Active All Day)</span>';
            if (countBadge) countBadge.textContent = 'All Day';
            return;
        }

        // Sort times
        selectedDailyTimes.sort();

        selectedDailyTimes.forEach((time, idx) => {
            const pill = document.createElement('div');
            pill.className = 'time-tag-pill';
            pill.innerHTML = `
                <span>${time}</span>
                <span class="time-tag-remove" onclick="removeDailyTime('${time}')">&times;</span>
                <input type="hidden" name="recurrence_days[]" value="${time}">
            `;
            container.appendChild(pill);
        });

        if (countBadge) {
            countBadge.textContent = `${selectedDailyTimes.length} ${selectedDailyTimes.length === 1 ? 'time' : 'times'} selected`;
        }
    }

    function addTimeFromInput() {
        const input = document.getElementById('addTimePicker');
        if (!input || !input.value) return;
        addDailyTime(input.value);
    }

    function addDailyTime(timeStr) {
        if (!timeStr) return;
        if (!selectedDailyTimes.includes(timeStr)) {
            selectedDailyTimes.push(timeStr);
            renderSelectedTimes();
        }
    }

    function togglePresetTime(timeStr) {
        if (selectedDailyTimes.includes(timeStr)) {
            removeDailyTime(timeStr);
        } else {
            addDailyTime(timeStr);
        }
    }

    function removeDailyTime(timeStr) {
        selectedDailyTimes = selectedDailyTimes.filter(t => t !== timeStr);
        renderSelectedTimes();
    }

    function clearAllTimes() {
        selectedDailyTimes = [];
        renderSelectedTimes();
    }

    function toggleFreqOptions() {
        const select = document.getElementById('freqSelect');
        if (!select) return;
        const val = select.value;

        const daily = document.getElementById('dailyOption');
        const timed = document.getElementById('timedOption');
        const customDays = document.getElementById('customDaysOption');

        if (daily) daily.style.display = 'none';
        if (timed) timed.style.display = 'none';
        if (customDays) customDays.style.display = 'none';

        if (val === 'daily') {
            if (daily) daily.style.display = 'block';
        } else if (val === 'once') {
            if (timed) timed.style.display = 'block';
        } else if (val === 'custom_days') {
            if (customDays) customDays.style.display = 'block';
        } else if (val === 'custom') {
            // Trading weekdays Mon-Fri preset
            document.querySelectorAll('.custom-day-chk').forEach(el => {
                el.checked = ['Monday','Tuesday','Wednesday','Thursday','Friday'].includes(el.value);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderSelectedTimes();
        toggleFreqOptions();
    });

    if (window.jQuery) {
        $(document).on('shown.bs.modal', '#reminderModal', function() {
            renderSelectedTimes();
            toggleFreqOptions();
        });
    }
</script>
@endpush
