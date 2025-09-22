<div class="modal fade" id="tradeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="tradeForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Log Trade</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="trade_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Asset</label>
                                <input type="text" name="asset" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Direction</label>
                                <select name="direction" class="form-control">
                                    <option value="long">Long</option>
                                    <option value="short">Short</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Trade Type</label>
                                <select name="trade_type" class="form-control">
                                    <option value="scalp">Scalp</option>
                                    <option value="intraday">Intraday</option>
                                    <option value="swing">Swing</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Session</label>
                                <select name="session" class="form-control">
                                    <option value="London">London</option>
                                    <option value="New York">New York</option>
                                    <option value="Asian">Asian</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Setup</label>
                                <input type="text" name="setup" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>RR</label>
                                <input type="number" step="0.01" name="rr" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Pips</label>
                                <input type="number" step="0.1" name="pips" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Outcome</label>
                                <select name="outcome" class="form-control">
                                    <option value="win">Win</option>
                                    <option value="loss">Loss</option>
                                    <option value="breakeven">Breakeven</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tags (comma separated)</label>
                                <input type="text" name="tags" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Psychology -->
                    <div class="form-group">
                        <label>Emotions</label>
                        <textarea name="emotions" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Entry Narrative</label>
                        <textarea name="entry_narrative" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>

                    <!-- Screenshots -->
                    <div class="form-group">
                        <label>Screenshots</label>
                        <input type="file" name="screenshots[]" class="form-control" multiple>
                        <small class="text-muted">Upload D1, H1, Entry, After charts (multiple allowed)</small>
                    </div>

                    <div class="form-group">
                        <label>Plan Followed?</label>
                        <select name="plan_followed" class="form-control">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>News Context</label>
                        <textarea name="news" class="form-control" rows="2" placeholder="FOMC, CPI, High Impact News..."></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Trade</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
