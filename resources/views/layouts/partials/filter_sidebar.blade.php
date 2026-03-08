<div class="right-sidebar">
    <div class="sidebar-title d-flex justify-content-between align-items-center">
        <h3 class="weight-600 font-16 text-primary mb-0">
            <i class="dw dw-filter text-primary mr-2"></i> Filter Analytics
        </h3>
        <div class="close-sidebar" data-toggle="right-sidebar-close">
            <i class="icon-copy ion-close-round"></i>
        </div>
    </div>

    <div class="right-sidebar-body customscroll">
        <div class="right-sidebar-body-content">
            <form id="filter_form" class="pb-20">
                
                <div class="form-group">
                    <label>Date Range</label>
                    <div class="d-flex flex-wrap mb-2" id="date-ranges">
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="today">Today</button>
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="yesterday">Yesterday</button>
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="this_week">This Week</button>
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="last_week">Last Week</button>
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="this_month">This Month</button>
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="last_month">Last Month</button>
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="this_year">This Year</button>
                        <button type="button" class="btn btn-sm btn-outline-primary m-1 date-range-btn" data-range="last_year">Last Year</button>
                    </div>
                    <input class="form-control" id="date-picker" placeholder="Select Date" type="text">
                    <input type="hidden" name="start_date" id="start_date">
                    <input type="hidden" name="end_date" id="end_date">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="asset_type">Asset Type</label>
                            <select class="form-control" name="asset_type" id="asset_type">
                                <option value="0">All</option>
                                @foreach($assetTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="market">Asset</label>
                            <select class="form-control" name="market" id="market">
                                <option value="0">All</option>
                                @foreach($assets ?? [] as $asset)
                                <option value="{{ $asset->id }}">{{ strtoupper($asset->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="direction">Direction</label>
                            <select class="form-control" name="direction" id="direction">
                                <option value="0">All</option>
                                <option value="long">Long</option>
                                <option value="short">Short</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="session">Session</label>
                            <select class="form-control" name="session" id="session">
                                <option value="0">All</option>
                                <option value="london_open">London Open</option>
                                <option value="ny_open">New York Open</option>
                                <option value="london_close">London Close</option>
                                <option value="asia">Asian</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="outcome">Outcome</label>
                            <select class="form-control" name="outcome" id="outcome">
                                <option value="0">All</option>
                                <option value="win">Win</option>
                                <option value="pending">Pending</option>
                                <option value="loss">Loss</option>
                                <option value="breakeven">Breakeven</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hin_day_filter">HIN Day?</label>
                            <select class="form-control" name="hin_day" id="hin_day_filter">
                                <option value="0">All</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="plan_followed">Followed Plan?</label>
                            <select class="form-control" name="plan_followed" id="plan_followed">
                                <option value="0">All</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>
                </div>


            </form>
        </div>
    </div>
</div>
