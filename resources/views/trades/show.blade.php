@extends('layouts.app')

@section('title', 'Trade Details')

@section('content')
<div class="min-height-200px">
    <div class="page-header">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h4>Trade Details</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('trades.index') }}">Trades</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $trade->asset }} - {{ $trade->trade_date }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 col-sm-12 text-right">
                <a href="{{ route('trades.index') }}" class="btn btn-secondary">
                    <i class="dw dw-arrow-left"></i> Back to Trades
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Key Stats -->
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style1">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="progress-data">
                        <div id="chart"></div>
                    </div>
                    <div class="widget-data">
                        <div class="h4 mb-0">{{ strtoupper($trade->asset) }}</div>
                        <div class="weight-600 font-14">{{ ucfirst($trade->direction) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style1">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="widget-data">
                        <div class="h4 mb-0 {{ $trade->pnl >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($trade->pnl, 2) }}
                        </div>
                        <div class="weight-600 font-14">P&L</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style1">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="widget-data">
                        <div class="h4 mb-0">{{ $trade->rr }}R</div>
                        <div class="weight-600 font-14">Risk:Reward</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style1">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="widget-data">
                        <div class="h4 mb-0">
                            @if($trade->outcome == 'win')
                                <span class="badge badge-success">WIN</span>
                            @elseif($trade->outcome == 'loss')
                                <span class="badge badge-danger">LOSS</span>
                            @elseif($trade->outcome == 'breakeven')
                                <span class="badge badge-secondary">BE</span>
                            @else
                                <span class="badge badge-warning">PENDING</span>
                            @endif
                        </div>
                        <div class="weight-600 font-14">Outcome</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Details Column -->
        <div class="col-lg-4 col-md-12 mb-20">
            <div class="card-box height-100-p pd-20">
                <h4 class="h4 mb-20">Trade Info</h4>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Date
                        <span class="font-weight-bold">{{ $trade->trade_date }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Session
                        <span>{{ ucfirst(str_replace('_', ' ', $trade->session)) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Entry Type
                        <span>{{ strtoupper($trade->entry_type) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Plan Followed
                        <span>
                            @if($trade->plan_followed)
                                <i class="dw dw-checked text-success"></i> Yes
                            @else
                                <i class="dw dw-cancel text-danger"></i> No
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Emotions
                        <span>{{ $trade->emotions ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        News
                        <span>{{ $trade->news ?? '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Logs / Notes Column (Full Screen feel) -->
        <div class="col-lg-8 col-md-12 mb-20">
            <div class="card-box height-100-p pd-20">
                <h4 class="h4 mb-20">Trade Logs & Analysis</h4>
                
                <div class="tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#notes" role="tab" aria-selected="true">Notes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#narrative" role="tab" aria-selected="false">Entry Narrative</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#setup" role="tab" aria-selected="false">Setup & PD Arrays</a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#screenshots" role="tab" aria-selected="false">Screenshots</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="notes" role="tabpanel">
                            <div class="pd-20">
                                <div class="markdown-content" style="min-height: 300px; font-size: 16px; line-height: 1.6;">
                                    {{ $trade->notes ?? 'No notes available.' }}
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="narrative" role="tabpanel">
                            <div class="pd-20">
                                <p>{{ $trade->entry_narrative ?? 'No narrative recorded.' }}</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="setup" role="tabpanel">
                            <div class="pd-20">
                                <h5>Setup: <span class="text-primary">{{ $trade->setup }}</span></h5>
                                <hr>
                                <h6>PD Arrays:</h6>
                                <div class="mt-2">
                                    @if($trade->entry_pd_array)
                                        @foreach(is_array($trade->entry_pd_array) ? $trade->entry_pd_array : json_decode($trade->entry_pd_array, true) as $pd)
                                            <span class="badge badge-info mr-1 mb-1" style="font-size: 14px; padding: 8px 12px;">{{ $pd }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No PD arrays selected.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                         <div class="tab-pane fade" id="screenshots" role="tabpanel">
                            <div class="pd-20">
                                <div class="row">
                                    @forelse($trade->screenshots as $screenshot)
                                        <div class="col-md-6 mb-3">
                                            <a href="{{ asset('storage/' . $screenshot->path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $screenshot->path) }}" class="img-fluid rounded" alt="Trade Screenshot">
                                            </a>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center text-muted">No screenshots attached.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Render markdown for notes if needed
    document.addEventListener('DOMContentLoaded', function() {
        const notesContent = document.querySelector('.markdown-content');
        if (notesContent && typeof marked !== 'undefined') {
            notesContent.innerHTML = marked.parse(notesContent.innerText);
        }
    });
</script>
@endpush
