<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class='nav-item'>  
           <a href='nav-link' style='margin-top:5px'>Balance {{number_format($user->balance,2,',','.')}} USD</a>
      </li>
      
     
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
     {{--
        <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>
  --}}

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          
          <i class="fa fa-user"></i>
             <!-- <span class="badge badge-warning navbar-badge">15</span> -->
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">
                                       @if( @$user->firstname )
                                             {{$user->firstname}} {{$user->lastname}}
                                       @else
                                              {{@$user->username}}
                                       @endif</span>

          <div class="dropdown-divider"></div>
          <a href="{{(route('user.profile'))}}" class="dropdown-item">
            <i class="fas fa-user mr-2"></i>
            <span class="float-right text-muted text-sm"> Mi perfil</span>
          </a>
         
          <div class="dropdown-divider"></div>
          <a href="{{route('user.logout')}}" class="dropdown-item">
            <i class="fas fa-sign-out-alt mr-2"></i> 
            <span class="float-right text-muted text-sm">@lang('Logout')</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
     
    </ul>
  </nav>
      <script>

                    function lenguaje(code) {
                            window.location.href = "{{url('/')}}/change-lang/" + code;
                        }

                      function copiar(id_elemento) {

                      
                                // Crea un campo de texto "oculto"
                                var aux = document.createElement("input");
                                // Asigna el contenido del elemento especificado al valor del campo
                                aux.setAttribute("value", document.getElementById(id_elemento).innerHTML);
                                // Añade el campo a la página
                                document.body.appendChild(aux);
                                // Selecciona el contenido del campo
                                aux.select();
                                // Copia el texto seleccionado
                                document.execCommand("copy");

                                // Elimina el campo de la página
                                document.body.removeChild(aux);
                                console.log("texto copiado");
                                $(".copiado").show('slow');

                                setTimeout('ocultar_copia()',3000);
                         }
                       function ocultar_copia(){
                            $(".copiado").hide('slow');
                         }
    </script>

<div class='copiado  text-center bg-success'> @lang('Copiado')!</div>
    <style>

.copiado{
    display:none;
    position:absolute;
    top:0; left:0;
    top:175px;
    left:50%;
    width:100px;
    padding:10px;
    border-radius:4px;
    z-index:9999999999;
    opacity:0.5;
    margin-left:-50px;
}

</style>