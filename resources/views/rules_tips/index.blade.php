@extends('layouts.app')

@section('title', 'Trading Guidelines')

@section('content')
<style>
    .rules-card {
        border-radius: 16px;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        height: 700px;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease;
        background: #fff;
    }
    .rules-card:hover {
        transform: translateY(-5px);
    }
    .rules-header {
        padding: 25px;
        position: relative;
        color: white;
    }
    .header-rule { background: linear-gradient(135deg, #1b00ff 0%, #4facfe 100%); }
    .header-checklist { background: linear-gradient(135deg, #13b02d 0%, #38ef7d 100%); }
    .header-psych { background: linear-gradient(135deg, #6f42c1 0%, #a18cd1 100%); }
    
    .rules-content {
        padding: 0;
        flex-grow: 1;
        overflow-y: auto;
        background: #fff;
    }
    .rules-footer {
        padding: 15px 25px;
        background: #fcfcfc;
        border-top: 1px solid #f0f0f0;
    }
    
    .guideline-item {
        padding: 18px 25px;
        border-bottom: 1px solid #f5f5f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s;
        cursor: pointer;
    }
    .guideline-item:hover {
        background-color: #f9f9ff;
    }
    .guideline-item:last-child {
        border-bottom: none;
    }
    .item-text {
        font-size: 14px;
        line-height: 1.6;
        color: #444;
        font-weight: 500;
    }
    .item-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
        font-size: 12px;
    }
    .icon-rule { background: rgba(27, 0, 255, 0.1); color: #1b00ff; }
    .icon-checklist { background: rgba(19, 176, 45, 0.1); color: #13b02d; }
    .icon-psych { background: rgba(111, 66, 193, 0.1); color: #6f42c1; }

    .action-buttons {
        display: flex;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .guideline-item:hover .action-buttons {
        opacity: 1;
    }
    
    /* Custom Scrollbar */
    .rules-content::-webkit-scrollbar { width: 4px; }
    .rules-content::-webkit-scrollbar-track { background: #f1f1f1; }
    .rules-content::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
</style>

<!-- <div class="page-header">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="title">
                <h4>Trading Guidelines</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Guidelines</li>
                </ol>
            </nav>
        </div>
    </div>
</div> -->

<div class="row">
    <!-- Trading Rules -->
    <div class="col-lg-4 col-md-6 mb-30">
        <div class="card-box rules-card">
            <div class="rules-header header-rule">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-white h4 mb-1">Trading Rules</h4>
                        <p class="text-white-50 mb-0 font-12">Strict Guidelines & Risk Management</p>
                    </div>
                    <i class="dw dw-book-1 font-30 opacity-5"></i>
                </div>
            </div>
            <div class="rules-content" id="rulesList">
                @forelse($rules as $rule)
                    <div class="guideline-item" data-id="{{ $rule->id }}">
                        <div class="d-flex align-items-start flex-grow-1">
                            <div class="item-icon icon-rule"><i class="fa fa-bolt"></i></div>
                            <span class="item-text rule-content">{{ $rule->content }}</span>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-warning edit-rule" data-id="{{ $rule->id }}" data-content="{{ $rule->content }}">
                                <i class="fa fa-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-rule" data-id="{{ $rule->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted" id="noRulesMessage">
                        <i class="dw dw-list font-40 d-block mb-3 opacity-2"></i>
                        No rules defined yet.
                    </div>
                @endforelse
            </div>
            <div class="rules-footer">
                <button class="btn btn-primary btn-block btn-sm" data-toggle="modal" data-target="#addRuleModal">
                    <i class="dw dw-add mr-2"></i> New Rule
                </button>
            </div>
        </div>
    </div>

    <!-- Entry Checklist -->
    <div class="col-lg-4 col-md-6 mb-30">
        <div class="card-box rules-card">
            <div class="rules-header header-checklist">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-white h4 mb-1">Entry Checklist</h4>
                        <p class="text-white-50 mb-0 font-12">Mandatory Pre-Flight Checks</p>
                    </div>
                    <i class="dw dw-checked-1 font-30 opacity-5"></i>
                </div>
            </div>
            <div class="rules-content" id="checklistList">
                @forelse($checklists as $checklist)
                    <div class="guideline-item" data-id="{{ $checklist->id }}">
                        <div class="d-flex align-items-start flex-grow-1">
                            <div class="item-icon icon-checklist"><i class="fa fa-check"></i></div>
                            <span class="item-text checklist-content">{{ $checklist->content }}</span>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-warning edit-checklist" data-id="{{ $checklist->id }}" data-content="{{ $checklist->content }}">
                                <i class="fa fa-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-checklist" data-id="{{ $checklist->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted" id="noChecklistMessage">
                        <i class="dw dw-checked font-40 d-block mb-3 opacity-2"></i>
                        No checklist items yet.
                    </div>
                @endforelse
            </div>
            <div class="rules-footer">
                <button class="btn btn-success btn-block btn-sm" data-toggle="modal" data-target="#addChecklistModal">
                    <i class="dw dw-add mr-2"></i> New Item
                </button>
            </div>
        </div>
    </div>

    <!-- Psychology -->
    <div class="col-lg-4 col-md-6 mb-30">
        <div class="card-box rules-card">
            <div class="rules-header header-psych">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-white h4 mb-1">Psychology</h4>
                        <p class="text-white-50 mb-0 font-12">Mental Frameworks & Mindset</p>
                    </div>
                    <i class="dw dw-idea font-30 opacity-5"></i>
                </div>
            </div>
            <div class="rules-content" id="tipsList">
                @forelse($tips as $tip)
                    <div class="guideline-item" data-id="{{ $tip->id }}">
                        <div class="d-flex align-items-start flex-grow-1">
                            <div class="item-icon icon-psych"><i class="fa fa-brain"></i></div>
                            <span class="item-text tip-content">{{ $tip->content }}</span>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-warning edit-tip" data-id="{{ $tip->id }}" data-content="{{ $tip->content }}">
                                <i class="fa fa-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-tip" data-id="{{ $tip->id }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted" id="noTipsMessage">
                        <i class="dw dw-idea font-40 d-block mb-3 opacity-2"></i>
                        No tips added yet.
                    </div>
                @endforelse
            </div>
            <div class="rules-footer">
                <button class="btn btn-sm btn-block text-white" style="background: #6f42c1;" data-toggle="modal" data-target="#addTipModal">
                    <i class="dw dw-add mr-2"></i> New Tip
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Rule Modal -->
<div class="modal fade" id="addRuleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="dw dw-add mr-2"></i>Add Trading Rule</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addRuleForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Rule Content</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Enter your trading rule..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="dw dw-add"></i> Add Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Tip Modal -->
<div class="modal fade" id="addTipModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="dw dw-add mr-2"></i>Add Psychological Tip</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addTipForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tip Content</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Enter your psychological tip..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #6f42c1; border-color: #6f42c1;">
                        <i class="dw dw-add"></i> Add Tip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Rule Modal -->
<div class="modal fade" id="editRuleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="dw dw-edit2 mr-2"></i>Edit Trading Rule</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editRuleForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editRuleId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Rule Content</label>
                        <textarea name="content" id="editRuleContent" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="dw dw-checked"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Tip Modal -->
<div class="modal fade" id="editTipModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="dw dw-edit2 mr-2"></i>Edit Psychological Tip</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTipForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editTipId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tip Content</label>
                        <textarea name="content" id="editTipContent" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #6f42c1; border-color: #6f42c1;">
                        <i class="dw dw-checked"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Checklist Modal -->
<div class="modal fade" id="addChecklistModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="dw dw-add mr-2"></i>Add Checklist Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addChecklistForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Checklist Content</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Enter your checklist item..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="dw dw-add"></i> Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Checklist Modal -->
<div class="modal fade" id="editChecklistModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="dw dw-edit2 mr-2"></i>Edit Checklist Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editChecklistForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editChecklistId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Checklist Content</label>
                        <textarea name="content" id="editChecklistContent" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="dw dw-checked"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    

    
    // Add Checklist
    $('#addChecklistForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("checklists.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addChecklistModal').modal('hide');
                $('#addChecklistForm')[0].reset();
                
                // Remove "no checklist" message if exists
                $('#noChecklistMessage').remove();
                
                // Add new checklist to the list
                const newChecklist = `
                    <div class="list-group-item guideline-item d-flex justify-content-between align-items-center" data-id="${response.data.id}">
                        <div class="d-flex align-items-start flex-grow-1">
                            <span class="text-success mr-2">✓</span>
                            <span class="checklist-content">${response.data.content}</span>
                        </div>
                        <div class="action-buttons">
                            <button type="button" class="btn btn-warning btn-sm edit-checklist" 
                                data-id="${response.data.id}" 
                                data-content="${response.data.content}">
                                <i class="dw dw-edit2"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-checklist" 
                                data-id="${response.data.id}">
                                <i class="dw dw-delete-3"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#checklistList').append(newChecklist);
                
                iziToastNotify('success', 'Checklist item added successfully!');
            },
            error: function(xhr) {
                iziToastNotify('error', 'Failed to add checklist item');
            }
        });
    });

    // Edit Checklist - Open Modal
    $(document).on('click', '.edit-checklist', function() {
        const id = $(this).data('id');
        const content = $(this).data('content');
        
        $('#editChecklistId').val(id);
        $('#editChecklistContent').val(content);
        $('#editChecklistModal').modal('show');
    });
    
    // Edit Checklist - Submit
    $('#editChecklistForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#editChecklistId').val();
        const newContent = $('#editChecklistContent').val();
        
        $.ajax({
            url: '/checklists/' + id,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                $('#editChecklistModal').modal('hide');
                
                // Update the content in the DOM
                const $item = $('#checklistList').find(`[data-id="${id}"]`);
                $item.find('.checklist-content').text(newContent);
                
                // Update data attribute for future edits
                $item.find('.edit-checklist').attr('data-content', newContent);
                
                iziToastNotify('success', 'Checklist item updated successfully!');
            },
            error: function(xhr) {
                iziToastNotify('error', 'Failed to update checklist item');
            }
        });
    });
    
    // Delete Checklist
    $(document).on('click', '.delete-checklist', function() {
        const id = $(this).data('id');
        const $item = $(this).closest('.guideline-item');
        
        if (confirm('Are you sure you want to delete this checklist item?')) {
            $.ajax({
                url: '/checklists/' + id,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $item.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if list is empty
                        if ($('#checklistList .guideline-item').length === 0) {
                            $('#checklistList').html('<div class="text-center py-4 text-muted" id="noChecklistMessage">No checklist items yet.</div>');
                        }
                    });
                    iziToastNotify('success', 'Checklist item deleted successfully!');
                },
                error: function(xhr) {
                    iziToastNotify('error', 'Failed to delete checklist item');
                }
            });
        }
    });
    
    // Add Rule
    $('#addRuleForm').on('submit', function(e) {

        e.preventDefault();
        
        $.ajax({
            url: '{{ route("rules.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addRuleModal').modal('hide');
                $('#addRuleForm')[0].reset();
                
                // Remove "no rules" message if exists
                $('#noRulesMessage').remove();
                
                // Add new rule to the list
                const newRule = `
                    <div class="list-group-item guideline-item d-flex justify-content-between align-items-center" data-id="${response.data.id}">
                        <div class="d-flex align-items-start flex-grow-1">
                            <span class="text-primary mr-2">•</span>
                            <span class="rule-content">${response.data.content}</span>
                        </div>
                        <div class="action-buttons">
                            <button type="button" class="btn btn-warning btn-sm edit-rule" 
                                data-id="${response.data.id}" 
                                data-content="${response.data.content}">
                                <i class="dw dw-edit2"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-rule" 
                                data-id="${response.data.id}">
                                <i class="dw dw-delete-3"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#rulesList').append(newRule);
                
                iziToastNotify('success', 'Rule added successfully!');
            },
            error: function(xhr) {
                iziToastNotify('error', 'Failed to add rule');
            }
        });
    });
    
    // Add Tip
    $('#addTipForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("tips.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addTipModal').modal('hide');
                $('#addTipForm')[0].reset();
                
                // Remove "no tips" message if exists
                $('#noTipsMessage').remove();
                
                // Add new tip to the list
                const newTip = `
                    <div class="list-group-item guideline-item d-flex justify-content-between align-items-center" data-id="${response.data.id}">
                        <div class="d-flex align-items-start flex-grow-1">
                            <span class="mr-2" style="color: #6f42c1;">•</span>
                            <span class="tip-content">${response.data.content}</span>
                        </div>
                        <div class="action-buttons">
                            <button type="button" class="btn btn-warning btn-sm edit-tip" 
                                data-id="${response.data.id}" 
                                data-content="${response.data.content}">
                                <i class="dw dw-edit2"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-tip" 
                                data-id="${response.data.id}">
                                <i class="dw dw-delete-3"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#tipsList').append(newTip);
                
                iziToastNotify('success', 'Tip added successfully!');
            },
            error: function(xhr) {
                iziToastNotify('error', 'Failed to add tip');
            }
        });
    });
    
    // Edit Rule - Open Modal
    $(document).on('click', '.edit-rule', function() {
        const id = $(this).data('id');
        const content = $(this).data('content');
        
        $('#editRuleId').val(id);
        $('#editRuleContent').val(content);
        $('#editRuleModal').modal('show');
    });
    
    // Edit Rule - Submit
    $('#editRuleForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#editRuleId').val();
        const newContent = $('#editRuleContent').val();
        
        $.ajax({
            url: '/rules/' + id,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                $('#editRuleModal').modal('hide');
                
                // Update the content in the DOM
                const $item = $('#rulesList').find(`[data-id="${id}"]`);
                $item.find('.rule-content').text(newContent);
                
                // Update data attribute for future edits
                $item.find('.edit-rule').attr('data-content', newContent);
                
                iziToastNotify('success', 'Rule updated successfully!');
            },
            error: function(xhr) {
                iziToastNotify('error', 'Failed to update rule');
            }
        });
    });
    
    // Edit Tip - Open Modal
    $(document).on('click', '.edit-tip', function() {
        const id = $(this).data('id');
        const content = $(this).data('content');
        
        $('#editTipId').val(id);
        $('#editTipContent').val(content);
        $('#editTipModal').modal('show');
    });
    
    // Edit Tip - Submit
    $('#editTipForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#editTipId').val();
        const newContent = $('#editTipContent').val();
        
        $.ajax({
            url: '/tips/' + id,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                $('#editTipModal').modal('hide');
                
                // Update the content in the DOM
                const $item = $('#tipsList').find(`[data-id="${id}"]`);
                $item.find('.tip-content').text(newContent);
                
                // Update data attribute for future edits
                $item.find('.edit-tip').attr('data-content', newContent);
                
                iziToastNotify('success', 'Tip updated successfully!');
            },
            error: function(xhr) {
                iziToastNotify('error', 'Failed to update tip');
            }
        });
    });
    
    // Delete Rule
    $(document).on('click', '.delete-rule', function() {
        const id = $(this).data('id');
        const $item = $(this).closest('.guideline-item');
        
        if (confirm('Are you sure you want to delete this rule? This action cannot be undone.')) {
            $.ajax({
                url: '/rules/' + id,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $item.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if list is empty
                        if ($('#rulesList .guideline-item').length === 0) {
                            $('#rulesList').html('<div class="text-center py-4 text-muted" id="noRulesMessage">No rules defined yet.</div>');
                        }
                    });
                    iziToastNotify('success', 'Rule deleted successfully!');
                },
                error: function(xhr) {
                    iziToastNotify('error', 'Failed to delete rule');
                }
            });
        }
    });
    
    // Delete Tip
    $(document).on('click', '.delete-tip', function() {
        const id = $(this).data('id');
        const $item = $(this).closest('.guideline-item');
        
        if (confirm('Are you sure you want to delete this tip? This action cannot be undone.')) {
            $.ajax({
                url: '/tips/' + id,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $item.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if list is empty
                        if ($('#tipsList .guideline-item').length === 0) {
                            $('#tipsList').html('<div class="text-center py-4 text-muted" id="noTipsMessage">No psychological tips yet.</div>');
                        }
                    });
                    iziToastNotify('success', 'Tip deleted successfully!');
                },
                error: function(xhr) {
                    iziToastNotify('error', 'Failed to delete tip');
                }
            });
        }
    });
    
});
</script>
@endpush
