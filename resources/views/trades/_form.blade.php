<div class="modal fade" id="add_trade_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="add_trade_form" method="POST" action="{{ route('trades.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Log Trade</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Asset</label>
                                <select name="asset" id="asset" class="form-control">
                                    <option value="eurusd">EU</option>
                                    <option value="gbpusd">GU</option>
                                    <option value="xauusd">GOLD</option>
                                    <option value="xagusd">SILVER</option>
                                    <option value="us500">ES</option>
                                    <option value="us100">NQ</option>
                                    <option value="btusd">Long</option>
                                    <option value="ethusd">ETH</option>
                                    <option value="audusd">BTC</option>
                                    <option value="nzdusd">NU</option>
                                    <option value="usdcad">UCAD</option>
                                    <option value="usdchf">UCHF</option>
                                    <option value="usdjpy">UJ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="trade_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Direction</label>
                                <select name="direction" class="form-control">
                                    <option value="long">Long</option>
                                    <option value="short">Short</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Session</label>
                                <select name="session" class="form-control">
                                    <option value="london_open">London Open</option>
                                    <option value="ny_open">New York Open</option>
                                    <option value="london_close">London Close</option>
                                    <option value="asia">Asian</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>RR</label>
                                <input type="number" step="0.01" name="rr" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pips</label>
                                <input type="number" step="0.1" name="pips" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Outcome</label>
                                <select name="outcome" class="form-control">
                                    <option value="pending">Pending</option>
                                    <option value="win">Win</option>
                                    <option value="loss">Loss</option>
                                    <option value="breakeven">Breakeven</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Plan Followed?</label>
                                <select name="plan_followed" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Trade Type</label>
                                <select name="entry_type" class="form-control">
                                    <option value="market">Market</option>
                                    <option value="limit">Limit</option>
                                    <option value="stop">Stop</option>
                                </select>
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Setup</label>
                                <input type="text" name="setup" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>News Context</label>
                                <input type="text" name="news" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Daily Log URL</label>
                                <input type="url" name="daily_log_url" class="form-control" />
                            </div>
                        </div>

                        <!-- Psychology -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Emotions</label>
                                <input type="text" name="emotions" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Entry Narrative</label>
                                <input type="text" name="entry_narrative" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Entry PD Array</label>
                                <select class="form-control" name="entry_pd_array" id="entry_pd_array_s2"
                                    multiple="multiple" style="width: 100%;">
                                    <option value="OB">OB</option>
                                    <option value="FVG">FVG</option>
                                    <option value="IFVG">IFVG</option>
                                    <option value="BB">BB</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Notes</label>
                                <input type="text" name="notes" class="form-control" />
                            </div>
                        </div>{{--
                        <div class="col-lg-12">
                            <div class="custom-control custom-checkbox mb-5">
                                <input type="checkbox" class="custom-control-input" name="status" id="status">
                                <label class="custom-control-label" for="status">IS OPEN</label>
                            </div>
                        </div> --}}

                        {{-- <div class="form-group">
                            <label>Tags (comma separated)</label>
                            <input type="text" name="tags" class="form-control">
                        </div> --}}





                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="submit_trade_btn">Log</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
        </form>
    </div>
</div>
