<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('user.home')}}" class="brand-link">
      <img src="{{asset('dist/img/gdelogo.png')}}" alt="GDEtoken" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">GDE NETWORK</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="">
        </div>
        <div class="info">
          <a href="#" class="d-block">
                                       @if( @$user->firstname )
                                             {{$user->firstname}} {{$user->lastname}}
                                       @else
                                              {{@$user->username}}
                                        @endif
                                    </a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
   

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        

          <li class="nav-item">
            <a href="{{route('user.home')}}" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
                   <p>
                        Inicio 
                  </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('user.member')}}" class="nav-link">
              <i class="nav-icon fas fa-medal"></i>
              
              <p>
                Membresias
              </p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="{{route('user.invest')}}" class="nav-link">
              <i class="nav-icon fas fa-cart-plus"></i>
              <p>
                  Participaciones
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('user.deposit')}}" class="nav-link">
            <i class="nav-icon fas fa-landmark"></i>
                   <p>
                        Depósitos
                  </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('user.my.tree')}}" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Binario
                <span class="right badge badge-danger">New</span>
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{route('user.my.ref')}}" class="nav-link">
              <i class="nav-icon  fas fa-user-friends"></i>

             
              <p>
                Mis Referidos
              </p>
            </a>
          </li>

     
   
     
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>