<!DOCTYPE html>
<html>
<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>Reset Password - Trading Journal</title>

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/core.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/icon-font.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/style.css') }}">
</head>
<body class="login-page">
	<div class="login-header box-shadow">
		<div class="container-fluid d-flex justify-content-between align-items-center">
			<div class="brand-logo">
				<a href="{{ url('/') }}">
					<img src="{{ asset('deskapp/vendors/images/logo-dark.png') }}" alt="" style="max-height: 50px;">
				</a>
			</div>
		</div>
	</div>
	<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-6">
					<img src="{{ asset('deskapp/vendors/images/forgot-password.png') }}" alt="">
				</div>
				<div class="col-md-6">
					<div class="login-box bg-white box-shadow border-radius-10">
						<div class="login-title">
							<h2 class="text-center text-primary">Reset Password</h2>
						</div>
						<h6 class="mb-20">Enter your new password</h6>
						<form method="POST" action="{{ route('password.store') }}">
							@csrf
							<!-- Password Reset Token -->
							<input type="hidden" name="token" value="{{ $request->route('token') }}">

							<div class="input-group custom">
								<input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
									   placeholder="Email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="dw dw-email"></i></span>
								</div>
								@error('email')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>
							<div class="input-group custom">
								<input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
									   placeholder="New Password" name="password" required autocomplete="new-password">
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
								</div>
								@error('password')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>
							<div class="input-group custom">
								<input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
									   placeholder="Confirm Password" name="password_confirmation" required autocomplete="new-password">
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
								</div>
								@error('password_confirmation')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>
							<div class="row align-items-center">
								<div class="col-5">
									<div class="input-group mb-0">
										<button class="btn btn-primary btn-lg btn-block" type="submit">Submit</button>
									</div>
								</div>
								<div class="col-2">
									<div class="font-16 weight-600 text-center" data-color="#707373">OR</div>
								</div>
								<div class="col-5">
									<div class="input-group mb-0">
										<a class="btn btn-outline-primary btn-lg btn-block" href="{{ route('login') }}">Login</a>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- js -->
	<script src="{{ asset('deskapp/vendors/scripts/core.js') }}"></script>
	<script src="{{ asset('deskapp/vendors/scripts/script.min.js') }}"></script>
	<script src="{{ asset('deskapp/vendors/scripts/process.js') }}"></script>
	<script src="{{ asset('deskapp/vendors/scripts/layout-settings.js') }}"></script>
</body>
</html>
