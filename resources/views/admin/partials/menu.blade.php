
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

			<!-- Sidebar content -->
			<div class="sidebar-content">

				<!-- Sidebar header -->
				<div class="sidebar-section">
					<div class="sidebar-section-body d-flex justify-content-center">
						<h5 class="sidebar-resize-hide flex-grow-1 my-auto">Navigation</h5>

						<div>
							<button type="button" class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
								<i class="ph-arrows-left-right"></i>
							</button>

							<button type="button" class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-mobile-main-toggle d-lg-none">
								<i class="ph-x"></i>
							</button>
						</div>
					</div>
				</div>
				<!-- /sidebar header -->


				<!-- Main navigation -->
				<div class="sidebar-section">
					<ul class="nav nav-sidebar" data-nav-type="accordion">

						<!-- Main -->
						<li class="nav-item-header pt-0">
							<div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Principal</div>
							<i class="ph-dots-three sidebar-resize-show"></i>
						</li>
						
						<li class="nav-item">
							<a href="{{ route('admin.dashboard') }}" class="nav-link active">
							<i class="fa fa-table" style= 'font-size:20px'  ></i>
								<span>
									Dashboard
									<!--<span class="d-block fw-normal opacity-50">No pending orders</span>-->
								</span>
							</a>
                            <a href="{{ route('admin.depositos') }}" class="nav-link">
							<i class="fa fa-money"  style= 'font-size:18px' ></i>
								<span>
									Depósitos
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>

							<a href="{{ route('admin.membresias') }}" class="nav-link">
							<i class='fas fa-medal' style= 'font-size:20px'></i>
                                 
								<span>
									Membresias
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>

							<a href="#" class="nav-link">
							<i class="fa fa-user"  style= 'font-size:18px' aria-hidden="true"></i>
								<span>
									Usuarios
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>



							<a href="{{ route('admin.participar') }}" class="nav-link">
							<i class='fa fa-handshake-o' style= 'font-size:18px'></i>
								<span>
									Participaciones
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>

							
							<a href="{{ route('admin.masivo') }}" class="nav-link">
							<i class="fa fa-money"  style= 'font-size:18px' aria-hidden="true"></i>
								<span>
									 Send Telegram
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>

</li>
					     <li class="nav-item-header pt-0">
							<div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">NFTS</div>
							<i class="ph-dots-three sidebar-resize-show"></i>
						</li>

						<li class="nav-item">

							<a href="{{route('admin.nfts')}}" class="nav-link">
							<i class="fa fa-user"  style= 'font-size:18px' aria-hidden="true"></i>
								<span>
								    Compras
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>

							<a href="{{route('admin.nfts.complete')}}" class="nav-link">
							<i class="fa fa-user"  style= 'font-size:18px' aria-hidden="true"></i>
								<span>
								    Nfts por usuarios
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>

                         </li>



 
                         <li class="nav-item-header pt-0">
							<div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Retiros</div>
							<i class="ph-dots-three sidebar-resize-show"></i>
						</li>

						<li class="nav-item">

							<a href="{{ route('admin.retiros') }}" class="nav-link">
							<i class="fa fa-money"  style= 'font-size:18px' aria-hidden="true"></i>
								<span>
									Retiros
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>
		
							<a href="{{route('admin.with.pend')}}" class="nav-link">
							<i class="fa fa-money"  style= 'font-size:18px' aria-hidden="true"></i>
								<span>
									Retiros Pendientes
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>
							<a href="{{route('admin.with.confi')}}" class="nav-link">
							<i class="fa fa-money"  style= 'font-size:18px' aria-hidden="true"></i>
								<span>
									Retiros Confirmados
									<!-- <span class="d-block fw-normal opacity-50">No pending orders</span> -->
								</span>
							</a>



						</li>


						<li class="nav-item-header pt-0">
							<div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Configuración</div>
							<i class="ph-dots-three sidebar-resize-show"></i>
						</li>

						<li class="nav-item">
							<a href="{{ route('admin.recargar.smart') }}" class="nav-link">
							<i class="fa fa-table" style= 'font-size:20px'  ></i>
								<span>
									Recarga de Smart contract
								
									
						    <a href="{{ route('admin.my.nfts') }}" class="nav-link">
							<i class="fa fa-table" style= 'font-size:20px'  ></i>
								<span>
									  Mi Colección nfts
								</span>
							</a>

                           </li>
					
					</ul>
				</div>
				<!-- /main navigation -->

			</div>
			<!-- /sidebar content -->
			
		</div>