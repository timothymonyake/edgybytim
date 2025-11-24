@extends('layouts.app')

@section('title', 'Milestones')

@section('content')
<div class="min-height-200px">
    <div class="page-header">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h4>Milestones</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Milestones</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 col-sm-12 text-right">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addMilestoneModal">
                    <i class="dw dw-add"></i> Add Milestone
                </button>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        @forelse($milestones as $milestone)
        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
            <div class="card card-box">
                <div class="milestone-img-container" style="overflow: hidden; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                    <img class="card-img-top milestone-img" src="{{ $milestone->img_path }}" alt="Milestone Image" style="height: 200px; object-fit: cover; cursor: zoom-in; transition: transform 0.3s ease;" onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
                </div>
                <div class="card-body">
                    <h5 class="card-title weight-500">{{ \Carbon\Carbon::parse($milestone->date)->format('F j, Y') }}</h5>
                    <p class="card-text">{{ $milestone->text }}</p>
                    <form action="{{ route('milestones.destroy', $milestone->id) }}" method="POST" class="text-right">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                            <i class="dw dw-delete-3"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center">
            <div class="card-box pd-20">
                <h3 class="text-muted">No milestones yet. Add one to track your journey!</h3>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Add Milestone Modal -->
<div class="modal fade" id="addMilestoneModal" tabindex="-1" role="dialog" aria-labelledby="addMilestoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMilestoneModalLabel">Add New Milestone</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMilestoneForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" class="form-control-file" name="img_path" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="text" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Milestone</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Image Zoom Modal -->
<div class="modal fade" id="imageZoomModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="background: transparent; border: none;">
            <div class="modal-body text-center p-0">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="position: absolute; right: -20px; top: -20px; opacity: 1;">
                    <span aria-hidden="true" style="font-size: 2rem;">&times;</span>
                </button>
                <img src="" id="zoomedImage" class="img-fluid rounded" style="max-height: 90vh; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#addMilestoneForm').on('submit', function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            let submitBtn = $(this).find('button[type="submit"]');
            let originalText = submitBtn.text();
            
            submitBtn.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: "{{ route('milestones.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addMilestoneModal').modal('hide');
                        // Reload page to show new milestone (or append dynamically if preferred, but reload is simpler for now)
                        window.location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    if (errors) {
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });
                    } else {
                        errorMessage = 'An error occurred while saving the milestone.';
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
        // Image Zoom Handler
        $('.milestone-img').on('click', function() {
            let src = $(this).attr('src');
            $('#zoomedImage').attr('src', src);
            $('#imageZoomModal').modal('show');
        });
    });
</script>
@endpush
