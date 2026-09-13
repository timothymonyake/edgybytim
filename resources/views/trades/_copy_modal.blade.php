<!-- Copy Trade Modal -->
<div class="modal fade" id="copy_trade_modal" tabindex="-1" role="dialog" aria-labelledby="copyTradeModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content rounded-2xl shadow-lg border-0">
            <form id="copy_trade_form" method="POST" action="">
                @csrf
                <input type="hidden" name="source_trade_id" id="copy_source_trade_id" value="">
                <input type="hidden" name="status" value="open">

                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title font-weight-bold" id="copyTradeModalLabel">
                            <i class="dw dw-copy text-success mr-1"></i> Copy Open Trade
                        </h5>
                        <small class="text-muted" id="copy_trade_source_info">Clone trade setup to another account</small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <!-- Target Execution Box (Account, Risk, Size) -->
                    <div class="card mb-3 border-success" style="background: #f4fbf7; border: 1.5px solid #28a745 !important;">
                        <div class="card-body py-3">
                            <h6 class="text-success font-weight-bold mb-3">
                                <i class="dw dw-target mr-1"></i> Target Account &amp; Execution Sizing
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="font-weight-bold text-dark">Target Account <span class="text-danger">*</span></label>
                                        <select name="account_id" id="copy_trade_account" class="form-control" required>
                                            <option value="">-- Select Target Account --</option>
                                            @foreach($accounts ?? [] as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->broker ?? 'N/A' }} - ${{ number_format($acc->account_size) }})</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Account where this copied trade will be logged.</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="font-weight-bold text-dark">Risk (RR)</label>
                                        <input type="number" step="0.01" name="rr" id="copy_trade_rr" class="form-control" placeholder="0.00">
                                        <small class="text-muted">Target risk/reward</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label class="font-weight-bold text-dark">Size / PnL ($)</label>
                                        <input type="number" step="0.1" name="pnl" id="copy_trade_pnl" class="form-control" placeholder="0.00">
                                        <small class="text-muted">Initial size / PnL</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Copied Trade Details Section -->
                    <h6 class="text-muted font-weight-bold mb-2">
                        <i class="dw dw-settings mr-1"></i> Copied Trade Parameters
                    </h6>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Asset</label>
                                <select name="asset_id" id="copy_trade_asset" class="form-control" required>
                                    @foreach($assets ?? [] as $asset)
                                    <option value="{{ $asset->id }}">{{ strtoupper($asset->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="trade_date" id="copy_trade_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Direction</label>
                                <select name="direction" id="copy_trade_direction" class="form-control">
                                    <option value="long">Long</option>
                                    <option value="short">Short</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Session</label>
                                <select name="session" id="copy_trade_session" class="form-control">
                                    <option value="london_open">London Open</option>
                                    <option value="ny_open">New York Open</option>
                                    <option value="london_close">London Close</option>
                                    <option value="asia">Asian</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Trade Type</label>
                                <select name="entry_type" id="copy_trade_entry_type" class="form-control">
                                    <option value="market">Market</option>
                                    <option value="limit">Limit</option>
                                    <option value="stop">Stop</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Outcome</label>
                                <select name="outcome" id="copy_trade_outcome" class="form-control">
                                    <option value="pending" selected>Pending</option>
                                    <option value="win">Win</option>
                                    <option value="loss">Loss</option>
                                    <option value="breakeven">Breakeven</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Plan Followed?</label>
                                <select name="plan_followed" id="copy_trade_plan_followed" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Setup</label>
                                <input type="text" name="setup" id="copy_trade_setup" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>News Context</label>
                                <input type="text" name="news" id="copy_trade_news" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Daily Log URL</label>
                                <input type="url" name="daily_log_url" id="copy_trade_daily_log_url" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Emotions</label>
                                <input type="text" name="emotions" id="copy_trade_emotions" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Entry Narrative</label>
                                <input type="text" name="entry_narrative" id="copy_trade_entry_narrative" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Entry PD Array</label>
                                <select class="form-control" name="entry_pd_array[]" id="copy_entry_pd_array_s2"
                                    multiple="multiple" style="width: 100%;">
                                    <option value="OB">OB</option>
                                    <option value="FVG">FVG</option>
                                    <option value="IFVG">IFVG</option>
                                    <option value="BB">BB</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Notes</label>
                                <input type="text" name="notes" id="copy_trade_notes" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" class="custom-control-input" name="hin_day" id="copy_trade_hin_day">
                                <label class="custom-control-label" for="copy_trade_hin_day">Day Has High Impact News (HIN)?</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox mt-2">
                                <input type="checkbox" class="custom-control-input" name="copy_screenshots" id="copy_trade_screenshots" checked value="1">
                                <label class="custom-control-label" for="copy_trade_screenshots">Copy Attached Chart Screenshots</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success font-weight-bold" id="submit_copy_trade_btn">
                        <i class="dw dw-copy mr-1"></i> Copy Trade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
