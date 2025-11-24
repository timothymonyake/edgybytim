@extends('layouts.app')

@section('title', 'AI Insights History')

@section('content')
<div class="min-height-200px">
    <div class="page-header">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="title">
                    <h4>AI Insights History</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ai-insights.index') }}">AI Insights</a></li>
                        <li class="breadcrumb-item active" aria-current="page">History</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @forelse($history as $item)
                <div class="card-box mb-30">
                    <div class="pd-20">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="text-blue h5">
                                @if($item->source == 'monthly')
                                    <span class="badge badge-primary mr-2">Monthly</span>
                                @else
                                    <span class="badge badge-info mr-2">Quick</span>
                                @endif
                                {{ $item->title ?? 'Insight' }}
                            </h5>
                            <span class="text-muted small">{{ $item->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        
                        @if($item->prompt)
                            <div class="mb-3 p-3 bg-light rounded">
                                <strong>Query:</strong>
                                <p class="mb-0 text-muted">{{ $item->prompt }}</p>
                            </div>
                        @endif

                        <div class="insight-content mt-3" style="white-space: pre-wrap;">{{ $item->response }}</div>
                    </div>
                </div>
            @empty
                <div class="card-box pd-20 text-center text-muted">
                    <p>No insights saved yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
