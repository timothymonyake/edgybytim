@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="min-height-200px">
    <div class="page-header">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="title">
                    <h4>Profile Settings</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
            <div class="pd-20 card-box height-100-p">
                <div class="profile-photo">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="" class="avatar-photo" style="width: 160px; height: 160px; object-fit: cover; border-radius: 50%;">
                    @else
                        <div class="avatar-initials" style="width: 160px; height: 160px; background: #667eea; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 64px; font-weight: bold; margin: 0 auto;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <h5 class="text-center h5 mb-0">{{ $user->name }}</h5>
                <p class="text-center text-muted font-14">{{ $user->email }}</p>
                <div class="profile-info">
                    <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                    <ul>
                        <li>
                            <span>Email Address:</span>
                            {{ $user->email }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
            <div class="card-box height-100-p overflow-hidden">
                <div class="profile-tab height-100-p">
                    <div class="tab height-100-p">
                        <ul class="nav nav-tabs customtab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#setting" role="tab">Settings</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#api-key" role="tab">API Keys</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- Settings Tab -->
                            <div class="tab-pane fade show active" id="setting" role="tabpanel">
                                <div class="pd-20 profile-task-wrap">
                                    @if(session('status') == 'profile-updated')
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            Profile updated successfully!
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Profile Photo</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" type="file" name="profile_photo" accept="image/*">
                                                @error('profile_photo')
                                                    <div class="form-control-feedback text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Name</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                                                @error('name')
                                                    <div class="form-control-feedback text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Email</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                                                @error('email')
                                                    <div class="form-control-feedback text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <h5 class="mb-20 h5 text-blue">Change Password</h5>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Current Password</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" type="password" name="current_password">
                                                @error('current_password')
                                                    <div class="form-control-feedback text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">New Password</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" type="password" name="password">
                                                @error('password')
                                                    <div class="form-control-feedback text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Confirm Password</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" type="password" name="password_confirmation">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-0">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- API Key Tab -->
                            <div class="tab-pane fade" id="api-key" role="tabpanel">
                                <div class="pd-20 profile-task-wrap">
                                    @if(session('status') == 'api-key-updated')
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            API Key updated successfully!
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <div class="alert alert-info">
                                        <i class="icon-copy dw dw-info"></i> Configure your Groq API key to enable AI-powered insights. 
                                        Get your API key from <a href="https://console.groq.com/keys" target="_blank" class="text-blue">Groq Console</a>.
                                    </div>

                                    <form method="POST" action="{{ route('profile.update-api-key') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label>Groq API Key</label>
                                            <input class="form-control" type="password" name="groq_api_key" 
                                                   value="{{ old('groq_api_key', $user->groq_api_key ? '••••••••••••••••' : '') }}" 
                                                   placeholder="gsk_...">
                                            <small class="form-text text-muted">
                                                Your API key is encrypted and stored securely. It will only be used for generating AI insights via Groq (LLaMA 3.3).
                                            </small>
                                            @error('groq_api_key')
                                                <div class="form-control-feedback text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group mb-0">
                                            <button type="submit" class="btn btn-primary">Update API Key</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
