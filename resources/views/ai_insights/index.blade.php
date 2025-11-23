@extends('layouts.app')

@section('title', 'AI Trading Insights')

@push('styles')
<style>
    :root {
        --ai-primary: #667eea;
        --ai-secondary: #764ba2;
        --ai-success: #00e676;
        --ai-glow: rgba(102, 126, 234, 0.3);
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
    }

    .ai-header {
        background: linear-gradient(135deg, var(--ai-primary), var(--ai-secondary));
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 30px;
        color: #fff;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
        position: relative;
        overflow: hidden;
    }

    .ai-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .ai-header h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }

    .ai-header p {
        font-size: 16px;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    .ai-card {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.12);
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .ai-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.16);
    }

    .card-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: var(--ai-primary);
    }

    .input-group-modern {
        position: relative;
        margin-bottom: 20px;
    }

    .input-modern {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s ease;
        resize: vertical;
        min-height: 120px;
    }

    .input-modern:focus {
        outline: none;
        border-color: var(--ai-primary);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .btn-ai {
        background: linear-gradient(135deg, var(--ai-primary), var(--ai-secondary));
        color: #fff;
        border: none;
        padding: 14px 32px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-ai:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px var(--ai-glow);
        color: #fff;
    }

    .btn-ai:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-ai-secondary {
        background: linear-gradient(135deg, #00e676, #00c853);
    }

    .insight-output {
        background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 24px;
        min-height: 200px;
        white-space: pre-wrap;
        font-size: 15px;
        line-height: 1.7;
        color: #333;
        display: none;
    }

    .insight-output.show {
        display: block;
        animation: fadeInUp 0.5s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .loading-spinner {
        display: none;
        text-align: center;
        padding: 40px;
    }

    .loading-spinner.show {
        display: block;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #e9ecef;
        border-top-color: var(--ai-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .checkbox-modern {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        cursor: pointer;
    }

    .checkbox-modern input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .checkbox-modern label {
        font-weight: 500;
        color: #495057;
        cursor: pointer;
        margin: 0;
    }

    .alert-warning {
        background: linear-gradient(135deg, #ffc107, #ff9800);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 16px 20px;
    }

    .alert-warning a {
        color: #fff;
        text-decoration: underline;
        font-weight: 600;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .feature-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 24px;
        border-left: 4px solid var(--ai-primary);
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .feature-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--ai-primary), var(--ai-secondary));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
        margin-bottom: 16px;
    }

    .feature-title {
        font-size: 16px;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }

    .feature-desc {
        font-size: 14px;
        color: #6c757d;
        line-height: 1.5;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="ai-header text-center mb-5">
        <h1><i class="fa fa-brain mr-3"></i>AI Trading Insights</h1>
        <p>Get intelligent analysis and recommendations powered by Groq LLaMA 3.3</p>
    </div>

    @if(!auth()->user()->groq_api_key)
        <div class="alert alert-warning mb-4">
            <strong>API Key Required:</strong> Please set your Groq API key in your <a href="{{ route('profile.edit') }}">Profile Settings</a> to use this feature.
        </div>
    @endif

    <div class="row">
        <!-- On-Demand Analysis -->
        <div class="col-md-6">
            <div class="ai-card">
                <div class="card-title">
                    <i class="fa fa-comment-dots"></i>
                    Ask AI Anything
                </div>

                <form id="ai-analyze-form">
                    <div class="input-group-modern">
                        <textarea 
                            class="input-modern" 
                            id="ai-prompt" 
                            placeholder="Ask me anything about your trading... e.g., 'What are my weaknesses?' or 'How can I improve my win rate?'"
                            required
                        ></textarea>
                    </div>

                    <div class="checkbox-modern">
                        <input type="checkbox" id="include-trade-data" checked>
                        <label for="include-trade-data">Include my recent trade data in analysis</label>
                    </div>

                    <button type="submit" class="btn btn-ai" id="analyze-btn">
                        <i class="fa fa-magic"></i>
                        Generate Insight
                    </button>
                </form>

                <div class="loading-spinner" id="loading-analyze">
                    <div class="spinner"></div>
                    <p style="color: #6c757d; font-weight: 500;">AI is analyzing...</p>
                </div>

                <div class="insight-output" id="insight-output"></div>
            </div>
        </div>

        <!-- Monthly Automated Insights -->
        <div class="col-md-6">
            <div class="ai-card">
                <div class="card-title">
                    <i class="fa fa-calendar-check"></i>
                    Monthly Performance Review
                </div>

                <p style="color: #6c757d; margin-bottom: 24px;">
                    Get a comprehensive AI-powered analysis of last month's trading performance with actionable recommendations.
                </p>

                <button type="button" class="btn btn-ai btn-ai-secondary" id="monthly-btn">
                    <i class="fa fa-chart-line"></i>
                    Generate Monthly Report
                </button>

                <div class="loading-spinner" id="loading-monthly">
                    <div class="spinner"></div>
                    <p style="color: #6c757d; font-weight: 500;">Generating comprehensive report...</p>
                </div>

                <div class="insight-output" id="monthly-output"></div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="ai-card">
        <div class="card-title">
            <i class="fa fa-lightbulb"></i>
            What Can AI Insights Do?
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa fa-search"></i>
                </div>
                <div class="feature-title">Pattern Recognition</div>
                <div class="feature-desc">Identify hidden patterns in your winning and losing trades</div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa fa-shield-alt"></i>
                </div>
                <div class="feature-title">Risk Analysis</div>
                <div class="feature-desc">Get personalized risk management recommendations</div>
            </div>

            <div class="feature-card">
                <p class="text-muted mb-4">
                Get instant, data-driven analysis of your trading performance using <strong>Groq LLaMA 3.3 70B</strong>.
                Ask questions about your strategy, psychology, or specific trades.
            </p>    <div class="feature-desc">Understand behavioral patterns affecting your performance</div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa fa-chart-bar"></i>
                </div>
                <div class="feature-title">Performance Optimization</div>
                <div class="feature-desc">Receive specific, actionable improvement strategies</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // On-Demand Analysis
    $('#ai-analyze-form').on('submit', function(e) {
        e.preventDefault();
        
        const prompt = $('#ai-prompt').val();
        const includeData = $('#include-trade-data').is(':checked');
        
        $('#analyze-btn').prop('disabled', true);
        $('#loading-analyze').addClass('show');
        $('#insight-output').removeClass('show');
        
        $.ajax({
            url: '{{ route("ai-insights.analyze") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                prompt: prompt,
                include_trade_data: includeData
            },
            success: function(response) {
                $('#loading-analyze').removeClass('show');
                $('#insight-output').text(response.insight).addClass('show');
                $('#analyze-btn').prop('disabled', false);
            },
            error: function(xhr) {
                $('#loading-analyze').removeClass('show');
                $('#analyze-btn').prop('disabled', false);
                
                let errorMsg = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                
                $('#insight-output').text('❌ Error: ' + errorMsg).addClass('show');
            }
        });
    });
    
    // Monthly Insights
    $('#monthly-btn').on('click', function() {
        $(this).prop('disabled', true);
        $('#loading-monthly').addClass('show');
        $('#monthly-output').removeClass('show');
        
        $.ajax({
            url: '{{ route("ai-insights.monthly") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loading-monthly').removeClass('show');
                const output = `📊 Monthly Report for ${response.period}\n` +
                              `Total Trades Analyzed: ${response.trade_count}\n\n` +
                              `${response.insight}`;
                $('#monthly-output').text(output).addClass('show');
                $('#monthly-btn').prop('disabled', false);
            },
            error: function(xhr) {
                $('#loading-monthly').removeClass('show');
                $('#monthly-btn').prop('disabled', false);
                
                let errorMsg = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                
                $('#monthly-output').text('❌ Error: ' + errorMsg).addClass('show');
            }
        });
    });
});
</script>
@endpush
