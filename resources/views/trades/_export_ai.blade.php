<!-- AI Export Modal -->
<div class="modal fade" id="export_ai_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header" style="background: #1b00ff;">
                <h5 class="modal-title" style="color: #fff;">
                    <i class="dw dw-upload mr-2"></i> Export Trades for AI Analysis
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info">
                    <i class="dw dw-information1 mr-1"></i>
                    Exports all your trade executions, account sizes &amp; balances, setups, high-impact news context, emotions, entry narratives, comments, and chart screenshot URLs — ready for ChatGPT, Claude, or any AI agent.
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Export Scope:</label>
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="export_scope_filtered" name="export_scope" class="custom-control-input" value="filtered" checked>
                        <label class="custom-control-label" for="export_scope_filtered">
                            <strong>Apply active filters</strong> &mdash; date range, account, outcome, etc.
                        </label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="export_scope_all" name="export_scope" class="custom-control-input" value="all">
                        <label class="custom-control-label" for="export_scope_all">
                            <strong>Export all trades</strong> &mdash; ignore current filters
                        </label>
                    </div>
                </div>

                <hr>

                <h6 class="font-weight-bold mb-3">Choose Export Format:</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card border p-3 text-center">
                            <h5 class="mb-2" style="color:#1b00ff;"><i class="dw dw-file-1 mr-1"></i> JSON File</h5>
                            <p class="small text-muted mb-3">Best for Custom GPTs, Claude Projects, or Python scripts. Full structured data.</p>
                            <button id="download_json_ai_btn" class="btn btn-primary btn-block">
                                <i class="dw dw-download mr-1"></i> Download JSON
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card border p-3 text-center">
                            <h5 class="mb-2" style="color:#28a745;"><i class="dw dw-newspaper-1 mr-1"></i> Markdown / Prompt</h5>
                            <p class="small text-muted mb-3">Best for pasting directly into ChatGPT or Claude chat.</p>
                            <div class="btn-group w-100">
                                <button id="download_md_ai_btn" class="btn btn-outline-success">
                                    <i class="dw dw-download"></i> Download .md
                                </button>
                                <button id="copy_ai_prompt_btn" class="btn btn-success">
                                    <i class="dw dw-copy"></i> Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="copy_success_alert" class="alert alert-success mt-2" style="display: none;">
                    <i class="dw dw-tick mr-1"></i> Copied to clipboard! Paste into ChatGPT / Claude.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
