<div class="navbar navbar-dark navbar-expand-lg navbar-static border-bottom border-bottom-white border-opacity-10">
		<div class="container-fluid">
			<div class="d-flex d-lg-none me-2">
				<button type="button" class="navbar-toggler sidebar-mobile-main-toggle rounded-pill">
					<i class="ph-list"></i>
				</button>
			</div>

			<div class="navbar-brand flex-1 flex-lg-0">
				<a href="{{route('admin.dashboard')}}" class="d-inline-flex align-items-center">
					<img src="{{asset('dist/img/gdelogo.png')}}" style="font-size:24px" alt="">
					GDENETWORK
				</a>
			</div>

			
			<div class="navbar-collapse justify-content-center flex-lg-1 order-2 order-lg-1 collapse" id="navbar_search">
				<div class="navbar-search flex-fill position-relative mt-2 mt-lg-0 mx-lg-3">
					<div class="form-control-feedback form-control-feedback-start flex-grow-1" data-color-theme="dark">
						<input type="text" class="form-control bg-transparent rounded-pill" placeholder="Search" data-bs-toggle="dropdown">
						<div class="form-control-feedback-icon">
						<i class="fa fa-search" style="font-size:24px"></i>
						</div>
						
					</div>

				

					
				</div>
			</div>

			<ul class="nav flex-row justify-content-end order-1 order-lg-2">
				

				<li class="nav-item nav-item-dropdown-lg dropdown ms-lg-2">
					<a href="#" class="navbar-nav-link align-items-center rounded-pill p-1" data-bs-toggle="dropdown">
						<div class="status-indicator-container">
							<img src="{{asset('dist/img/gdelogo.png')}}" class="w-32px h-32px rounded-pill" alt="">
							<span class="status-indicator bg-success"></span>
						</div>
						<span class="d-none d-lg-inline-block mx-lg-2">{{$user->username}}</span>
					</a>

					<div class="dropdown-menu dropdown-menu-end">
						<a href="#" class="dropdown-item">
						<i class="fa fa-user" aria-hidden="true" style='margin-right:5px;'></i>
							My profile
						</a>
						
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
						<i class="fa fa-cogs" aria-hidden="true" style='margin-right:5px;'></i>
							Account settings
						</a>
						<a href="{{route('admin.logout')}}" class="dropdown-item">
						<i class="fa fa-times-circle" aria-hidden="true" style='margin-right:5px;'></i>
							Logout
						</a>
					</div>
				</li>
			</ul>
		</div>
	</div>