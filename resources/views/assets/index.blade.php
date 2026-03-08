@extends('layouts.app')
@section('title', 'Asset Management')

@section('content')
<div class="pd-20 card-box mb-30">
    <div class="clearfix mb-20">
        <div class="pull-left">
            <h4 class="text-blue h4">Manage Assets</h4>
            <p>Categorize assets as SYNTHETIC or NON-SYNTHETIC</p>
        </div>
        <div class="pull-right">
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addAssetModal">
                <i class="dw dw-add"></i> Add Asset
            </button>
        </div>
    </div>
    
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Type</th>
                <th class="datatable-nosort">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $asset)
            <tr data-id="{{ $asset->id }}">
                <td class="asset-name">{{ $asset->name }}</td>
                <td class="asset-code">{{ $asset->code ?: '—' }}</td>
                <td>
                    <span class="badge {{ $asset->assetType->name == 'SYNTHETIC' ? 'badge-warning' : 'badge-info' }}">
                        {{ $asset->assetType->name }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary edit-asset" 
                        data-id="{{ $asset->id }}" 
                        data-name="{{ $asset->name }}" 
                        data-code="{{ $asset->code }}" 
                        data-type-id="{{ $asset->asset_type_id }}">
                        <i class="dw dw-edit2"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-asset" data-id="{{ $asset->id }}">
                        <i class="dw dw-delete-3"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Asset Modal -->
<div class="modal fade" id="addAssetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Asset</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addAssetForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Asset Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Step Index" required>
                    </div>
                    <div class="form-group">
                        <label>Asset Code (Optional)</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. STEP">
                    </div>
                    <div class="form-group">
                        <label>Asset Type</label>
                        <select name="asset_type_id" class="form-control" required>
                            @foreach($assetTypes as $type)
                            <option value="{{ $type->id }}" {{ $type->name == 'NON-SYNTHETIC' ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Asset Modal -->
<div class="modal fade" id="editAssetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Asset</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editAssetForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit-asset-id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Asset Name</label>
                        <input type="text" name="name" id="edit-asset-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Asset Code (Optional)</label>
                        <input type="text" name="code" id="edit-asset-code" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Asset Type</label>
                        <select name="asset_type_id" id="edit-asset-type-id" class="form-control" required>
                            @foreach($assetTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add Asset
    $('#addAssetForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('assets.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    iziToastNotify('success', res.message);
                    location.reload();
                }
            }
        });
    });

    // Edit Asset
    $('.edit-asset').on('click', function() {
        $('#edit-asset-id').val($(this).data('id'));
        $('#edit-asset-name').val($(this).data('name'));
        $('#edit-asset-code').val($(this).data('code'));
        $('#edit-asset-type-id').val($(this).data('type-id'));
        $('#editAssetModal').modal('show');
    });

    $('#editAssetForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit-asset-id').val();
        $.ajax({
            url: "/assets/" + id,
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    iziToastNotify('success', res.message);
                    location.reload();
                }
            }
        });
    });

    // Delete Asset
    $('.delete-asset').on('click', function() {
        if (confirm('Are you sure? Trades associated with this asset will lose their link (but will not be deleted).')) {
            let id = $(this).data('id');
            $.ajax({
                url: "/assets/" + id,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    if (res.success) {
                        iziToastNotify('success', res.message);
                        location.reload();
                    }
                }
            });
        }
    });
});
</script>
@endpush
