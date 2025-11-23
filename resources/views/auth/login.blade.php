<!DOCTYPE html>
<html>
<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>Login - Trading Journal</title>

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
				<div class="col-md-6 col-lg-7">
					<img src="{{ asset('deskapp/vendors/images/login-page-img.png') }}" alt="">
				</div>
				<div class="col-md-6 col-lg-5">
					<div class="login-box bg-white box-shadow border-radius-10">
						<div class="login-title">
							<h2 class="text-center text-primary">Login To Trading Journal</h2>
						</div>

						<!-- Session Status -->
						@if (session('status'))
							<div class="alert alert-success alert-dismissible fade show" role="alert">
								{{ session('status') }}
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						@endif

						<form method="POST" action="{{ route('login') }}">
							@csrf
							<div class="input-group custom">
								<input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
									   placeholder="Email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
								</div>
								@error('email')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>
							<div class="input-group custom">
								<input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
									   placeholder="Password" name="password" required autocomplete="current-password">
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
								</div>
								@error('password')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>
							<div class="row pb-30">
								<div class="col-6">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
										<label class="custom-control-label" for="remember_me">Remember</label>
									</div>
								</div>
								<div class="col-6">
									<div class="forgot-password">
										@if (Route::has('password.request'))
											<a href="{{ route('password.request') }}">Forgot Password</a>
										@endif
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="input-group mb-0">
										<button class="btn btn-primary btn-lg btn-block" type="submit">Sign In</button>
									</div>
									@if (Route::has('register'))
										<div class="font-16 weight-600 pt-10 pb-10 text-center" data-color="#707373">OR</div>
										<div class="input-group mb-0">
											<a class="btn btn-outline-primary btn-lg btn-block" href="{{ route('register') }}">Register To Create Account</a>
										</div>
									@endif
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
