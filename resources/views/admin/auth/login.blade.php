@extends('admin.layouts.login')



@section('content')


	<script src="https://kit.fontawesome.com/33e0e03e46.js" crossorigin="anonymous"></script>
 
<div class="content-inner">

				<!-- Content area -->
				<div class="content d-flex justify-content-center align-items-center">

					<!-- Login form -->
                    <form action="{{ route('admin.login') }}" method="POST" class="login-form">

                   @csrf
						<div class="card mb-0">
							<div class="card-body">
								<div class="text-center mb-3">
									<div class="d-inline-flex align-items-center justify-content-center mb-4 mt-2">
										<img src="{{asset('dist/img/gdelogo.png')}}"  style='width:150px;' alt="">
									</div>
									<h5 class="mb-0">Login to your account</h5>
									<span class="d-block text-muted">Enter your credentials below</span>
								</div>

								<div class="mb-3">
									<label class="form-label">Username</label>
									<div class="form-control-feedback form-control-feedback-start">
                                    <input type="text" name="username" class='form-control' value="" placeholder="Enter your username">
										<div class="form-control-feedback-icon">
											<i class="fa fa-user text-muted"  style = 'font-size:16px' aria-hidden="true"></i>
										</div>
									</div>
								</div>

								<div class="mb-3">
									<label class="form-label">Password</label>
									<div class="form-control-feedback form-control-feedback-start">
										<input type="password" name='password' class="form-control" placeholder="•••••••••••">

										<div class="form-control-feedback-icon">
											<i class="fa fa-key text-muted" aria-hidden="true"></i>
										</div>
									</div>
								</div>

                                
                              <div class="frm-group" style='margin-bottom:10px;'>

                                    <input type="checkbox"  name="remember" id="checkbox">

                                    <label for="checkbox">Remember Me</label>

                                    </div>

								<div class="mb-3">
									<button type="submit" class="btn btn-primary w-100">Sign in</button>
								</div>

						
							</div>
						</div>
					</form>
					<!-- /login form -->

				</div>
				<!-- /content area -->


				<!-- Footer -->
				<div class="navbar navbar-sm navbar-footer border-top">
					<div class="container-fluid">
						<span>&copy; 2022 GDENETWORK</a></span>

					</div>
				</div>
				<!-- /footer -->

			</div>


{{--

<div class="signin-section pt-5" style="background-image: url('./assets/images/login.png');">

    <div class="container-fluid">

        <div class="row justify-content-center align-items-center">

        <div class="col-xl-4 col-md-6 col-sm-8">

            <div class="login-area">

                <div class="login-header-wrapper text-center">

                    <img class="logo" src="{{ get_image(config('constants.logoIcon.path') .'/logo.png') }}" alt="image">

                    <p class="text-center admin-brand-text">Admin Pannel</p>

                </div>

                <form action="{{ route('admin.login') }}" method="POST" class="login-form">

                    @csrf

                    <div class="login-inner-block">

                        <div class="frm-grp">

                            <label>Username</label>

                            <input type="text" name="username" value="" placeholder="Enter your username">

                        </div>

                        <div class="frm-grp">

                            <label>Password</label>

                            <input type="password" name="password"  value="" placeholder="Enter your password">

                        </div>

                    </div>

                    <div class="d-flex mt-3 justify-content-between">

                        <div class="frm-group">

                            <input type="checkbox" name="remember" id="checkbox">

                            <label for="checkbox">Remember Me</label>

                        </div>

                        <a href="{{ route('admin.password.reset') }}" class="forget-pass">Forget password?</a>

                    </div>

                    <div class="btn-area text-center">

                    <button type="submit" class="submit-btn">Login now</button>

                    </div>

                </form>

            </div>

        </div>

        

        </div>

    </div>

</div>
--}}
@endsection



@push('style-lib')


@endpush