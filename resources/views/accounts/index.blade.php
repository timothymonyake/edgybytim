@extends('layouts.app')

@section('title', 'Trading Accounts')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>Trading Accounts</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Accounts</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-right">
            <button type="button" class="btn btn-primary" id="open_create_account_modal">
                <i class="dw dw-add-file1"></i> Add Account
            </button>
        </div>
    </div>
</div>

<!-- Accounts List Cards -->
<div class="row">
    @forelse($accounts as $acc)
        <div class="col-xl-4 col-lg-6 col-md-6 mb-30">
            <div class="card-box pd-20 height-100-p position-relative" style="border-top: 5px solid {{ $acc->color }}; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-15">
                    <div class="d-flex align-items-center">
                        <span class="badge font-14 font-weight-bold mr-2" style="background-color: {{ $acc->color }}; color: #fff; padding: 6px 12px; border-radius: 6px;">
                            {{ $acc->broker }}
                        </span>
                        <h5 class="font-18 weight-700 text-blue mb-0">{{ $acc->name }}</h5>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-link text-secondary dropdown-toggle no-arrow" type="button" data-toggle="dropdown">
                            <i class="dw dw-more font-20"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('accounts.show', $acc->id) }}"><i class="dw dw-analytics-21"></i> Analytics Details</a>
                            <a class="dropdown-item edit-account-btn" href="javascript:void(0)" data-account='@json($acc)'><i class="dw dw-edit2"></i> Edit Account</a>
                            <a class="dropdown-item toggle-archive-btn" href="javascript:void(0)" data-id="{{ $acc->id }}"><i class="dw dw-archive"></i> {{ $acc->status === 'Archived' ? 'Unarchive' : 'Archive' }}</a>
                            <form action="{{ route('accounts.destroy', $acc->id) }}" method="POST" class="delete-account-form" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger confirm-delete"><i class="dw dw-delete-3"></i> Delete</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-10">
                    <span class="badge badge-outline-secondary font-12" style="border: 1px solid #ddd; padding: 4px 8px; border-radius: 4px;">
                        <i class="dw dw-layers mr-1"></i> {{ $acc->phase }}
                    </span>
                    <span class="badge badge-{{ $acc->status === 'Active' ? 'success' : ($acc->status === 'Passed' ? 'info' : ($acc->status === 'Failed' ? 'danger' : 'secondary')) }} font-12" style="padding: 4px 8px; border-radius: 4px;">
                        {{ $acc->status }}
                    </span>
                </div>

                <div class="row text-center mt-20 mb-20 py-15" style="background: #f8f9fa; border-radius: 8px;">
                    <div class="col-4 border-right">
                        <div class="font-12 text-muted uppercase">Size</div>
                        <div class="font-16 weight-700 text-dark">${{ number_format($acc->account_size, 0) }}</div>
                    </div>
                    <div class="col-4 border-right">
                        <div class="font-12 text-muted uppercase">Balance</div>
                        <div class="font-16 weight-700 text-{{ $acc->computed_balance >= $acc->initial_balance ? 'success' : 'danger' }}">
                            ${{ number_format($acc->computed_balance, 2) }}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="font-12 text-muted uppercase">Net PnL</div>
                        <div class="font-16 weight-700 text-{{ $acc->net_pnl >= 0 ? 'success' : 'danger' }}">
                            {{ $acc->net_pnl >= 0 ? '+' : '' }}${{ number_format($acc->net_pnl, 2) }}
                        </div>
                    </div>
                </div>

                <div class="row font-14 mb-15">
                    <div class="col-6 mb-2">
                        <span class="text-muted">Net RR:</span> <strong class="text-dark">{{ $acc->net_rr >= 0 ? '+' : '' }}{{ $acc->net_rr }}R</strong>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted">Win Rate:</span> <strong class="text-dark">{{ $acc->win_rate }}%</strong>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted">Trades:</span> <strong class="text-dark">{{ $acc->trades_count }}</strong>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted">Best Pair:</span> <strong class="text-dark">{{ $acc->best_pair }}</strong>
                    </div>
                </div>

                <div class="text-right mt-15">
                    <a href="{{ route('accounts.show', $acc->id) }}" class="btn btn-outline-primary btn-sm btn-block style-btn">
                        View Account Dashboard <i class="dw dw-right-arrow1 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card-box pd-30 text-center">
                <div class="mb-20">
                    <i class="dw dw-money-2 text-muted" style="font-size: 64px;"></i>
                </div>
                <h4 class="text-muted">No Trading Accounts Found</h4>
                <p class="text-secondary mb-20">Create your first trading account to separate challenges, funded accounts, and personal portfolios.</p>
                <button type="button" class="btn btn-primary" id="empty_state_add_btn">
                    <i class="dw dw-add-file1"></i> Add Account Now
                </button>
            </div>
        </div>
    @endforelse
</div>

<!-- Account Form Modal (Create / Edit) -->
<div class="modal fade" id="account_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="account_form" method="POST" action="{{ route('accounts.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="account_modal_title">Add Trading Account</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Account Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. The5ers $5K Funded" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Broker / Prop Firm <span class="text-danger">*</span></label>
                                <input type="text" name="broker" class="form-control" placeholder="e.g. The5ers, FTMO, IC Markets" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Account Number</label>
                                <input type="text" name="account_number" class="form-control" placeholder="e.g. 5829471">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Account Size ($) <span class="text-danger">*</span></label>
                                <input type="number" step="100" name="account_size" class="form-control" placeholder="5000" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Currency</label>
                                <select name="currency" class="form-control">
                                    <option value="USD" selected>USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                    <option value="AUD">AUD ($)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Platform</label>
                                <select name="platform" class="form-control">
                                    <option value="MT5">MT5</option>
                                    <option value="MT4">MT4</option>
                                    <option value="cTrader">cTrader</option>
                                    <option value="DXTrade">DXTrade</option>
                                    <option value="MatchTrader">MatchTrader</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Phase <span class="text-danger">*</span></label>
                                <select name="phase" class="form-control" required>
                                    <option value="Challenge">Challenge</option>
                                    <option value="Verification">Verification</option>
                                    <option value="Funded">Funded</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Demo">Demo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="Active">Active</option>
                                    <option value="Passed">Passed</option>
                                    <option value="Failed">Failed</option>
                                    <option value="Archived">Archived</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Initial Balance ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="initial_balance" class="form-control" placeholder="5000" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Profit Target ($)</label>
                                <input type="number" step="0.01" name="profit_target" class="form-control" placeholder="e.g. 500">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Max Daily Loss ($)</label>
                                <input type="number" step="0.01" name="max_daily_loss" class="form-control" placeholder="e.g. 250">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Max Total Loss ($)</label>
                                <input type="number" step="0.01" name="max_total_loss" class="form-control" placeholder="e.g. 500">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Leverage</label>
                                <input type="text" name="leverage" class="form-control" placeholder="1:100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Account Color Badge</label>
                                <input type="color" name="color" class="form-control style-color" value="#3b82f6" style="height: 45px; padding: 2px;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Notes & Guidelines</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Rules, leverage constraints, or notes for this account..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="account_submit_btn">Save Account</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#open_create_account_modal, #empty_state_add_btn').click(function() {
        $('#account_form')[0].reset();
        $('#account_form input[name="_method"]').remove();
        $('#account_form').attr('action', '{{ route('accounts.store') }}');
        $('#account_modal_title').text('Add Trading Account');
        $('#account_submit_btn').text('Create Account');
        $('[name="color"]').val('#3b82f6');
        $('#account_modal').modal('show');
    });

    $(document).on('click', '.edit-account-btn', function() {
        let acc = $(this).data('account');
        $('#account_form')[0].reset();
        $('#account_modal_title').text('Edit Trading Account');
        $('#account_submit_btn').text('Update Account');
        
        $('#account_form').attr('action', '{{ url("/accounts") }}/' + acc.id);
        $('#account_form input[name="_method"]').remove();
        $('#account_form').append('<input type="hidden" name="_method" value="PUT">');

        $('[name="name"]').val(acc.name);
        $('[name="broker"]').val(acc.broker);
        $('[name="account_number"]').val(acc.account_number);
        $('[name="account_size"]').val(acc.account_size);
        $('[name="currency"]').val(acc.currency || 'USD');
        $('[name="platform"]').val(acc.platform || 'MT5');
        $('[name="phase"]').val(acc.phase || 'Challenge');
        $('[name="status"]').val(acc.status || 'Active');
        $('[name="initial_balance"]').val(acc.initial_balance);
        $('[name="current_balance"]').val(acc.current_balance);
        $('[name="profit_target"]').val(acc.profit_target);
        $('[name="max_daily_loss"]').val(acc.max_daily_loss);
        $('[name="max_total_loss"]').val(acc.max_total_loss);
        $('[name="leverage"]').val(acc.leverage);
        $('[name="color"]').val(acc.color || '#3b82f6');
        if (acc.start_date) $('[name="start_date"]').val(acc.start_date.split('T')[0]);
        if (acc.end_date) $('[name="end_date"]').val(acc.end_date.split('T')[0]);
        $('[name="notes"]').val(acc.notes);

        $('#account_modal').modal('show');
    });

    $(document).on('click', '.toggle-archive-btn', function() {
        let id = $(this).data('id');
        $.ajax({
            url: '{{ url("/accounts") }}/' + id + '/archive',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    iziToastNotify('success', res.message);
                    location.reload();
                }
            }
        });
    });

    $('.confirm-delete').click(function(e) {
        if (!confirm('Are you sure you want to delete this account? All assigned trades will have their account unlinked.')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
