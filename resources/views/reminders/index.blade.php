@extends('layouts.app')

@section('title', 'Reminders')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h4>Reminders</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reminders</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 col-sm-12 text-right">
                <button class="btn btn-info" onclick="ReminderSystem.requestPermission()">
                    <i class="fa fa-bell"></i> Enable Notifications
                </button>
                <button class="btn btn-primary" data-toggle="modal" data-target="#reminderModal">
                    <i class="fa fa-plus"></i> New Reminder
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($reminders as $reminder)
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-30">
                <div class="card-box height-100-p pd-20">
                    <div class="d-flex justify-content-between align-items-center mb-10">
                        <h5 class="h5 mb-0">{{ $reminder->title }}</h5>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <form action="{{ route('reminders.destroy', $reminder) }}" method="POST" onsubmit="return confirm('Delete this reminder?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item"><i class="dw dw-delete-3"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <p class="mb-10">{{ $reminder->content }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($reminder->frequency == 'once')
                                <span class="badge badge-primary">One-off</span>
                                <small class="text-muted d-block mt-1">
                                    <i class="fa fa-clock"></i> {{ $reminder->remind_at ? $reminder->remind_at->format('M d, H:i') : 'No time set' }}
                                </small>
                            @else
                                <span class="badge badge-success">{{ ucfirst($reminder->frequency) }}</span>
                                @if($reminder->expires_at)
                                    <small class="text-muted d-block mt-1">
                                        <i class="fa fa-hourglass-end"></i> Until: {{ $reminder->expires_at->format('M d, Y') }}
                                    </small>
                                @endif
                                @if($reminder->frequency == 'daily' && $reminder->recurrence_days)
                                    <small class="text-muted d-block mt-1">
                                        Hours: {{ implode(':00, ', $reminder->recurrence_days) }}:00
                                    </small>
                                @elseif($reminder->frequency == 'custom' && $reminder->recurrence_days)
                                    <small class="text-muted d-block mt-1">
                                        Days: {{ implode(', ', $reminder->recurrence_days) }}
                                    </small>
                                @endif
                            @endif
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="switch-{{ $reminder->id }}" {{ $reminder->is_active ? 'checked' : '' }} onchange="toggleReminder({{ $reminder->id }}, this.checked)">
                            <label class="custom-control-label" for="switch-{{ $reminder->id }}"></label>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-box pd-20 text-center">
                    <p class="mb-0">No reminders set. Create one to stay on top of your trading.</p>
                </div>
            </div>
        @endforelse
    </div>

    @include('reminders._form')
@endsection

@push('scripts')
<script>
    function toggleReminder(id, status) {
        $.ajax({
            url: `/reminders/${id}`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                is_active: status ? 1 : 0,
                // We'd need to send other required fields too if the controller validation is strict
                // For a quick toggle, a partial update endpoint would be better
            },
            success: function() {
                iziToastNotify('success', 'Reminder status updated');
            },
            error: function() {
                iziToastNotify('error', 'Failed to update reminder');
            }
        });
    }
</script>
@endpush
