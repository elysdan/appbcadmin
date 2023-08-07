

@yield('content')


<footer>
        <section class="procash_bg-dark pb-5" style="margin-top: -2em; padding-top: 2em">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <ul class="procash_list">
                            <li class="procash_text-accent"><a href="{{route('home')}}">@lang('Home')</a></li>
                            <li class="procash_text-accent"><a href="{{route('quienes')}}">@lang('About us')</a></li>
                            <li class="procash_text-accent"><a href="{{route('producto')}}">@lang('Financial products')</a></li>
                            <li class="procash_text-accent"><a href="{{route('contact')}}">@lang('Contact')</a></li>
                            <li class="procash_text-accent"><a href="{{route('user.login')}}">@lang('Login')</a></li> 
                            <li class="procash_text-accent"><a href="{{route('user.register')}}">@lang('Sign up')</a></li>

                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <ul class="procash_list">
                        <li class="procash_text-accent"><a target='_blank' href="https://procashdream.com/legalidad.pdf">Legalidad</a></li>
                            <li class="procash_text-accent"> <a target='_blank' href="https://procashdream.com/TERMINOS%20Y%20CONDICIONES.pdf">@lang('Terms and Conditions')</a></li>
                           
                        </ul>
                    </div>
                    <div class="col-lg-4" style='padding-top:30px'>
                        <h6 class="procash_text-accent text-uppercase">@lang('CONTACT US')</h6>
                        <p class="text-light">info@procashdream.com</p>
                        <!--
                        <p class="text-light"><a target='_blank' href="https://wa.link/rseo74"><i class="fa fa-whatsapp" aria-hidden="true"></i> Escribenos por whatsapp</a></p>
                        -->
                    </div>
                </div>
            </div>
        </section>

<section class="container procash_copyright">
            <div class="row">
                <div class="col">
                    @lang('ProCash ©2021 All rigths Reserved.')
                </div>
            </div>
        </section>
    </footer>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    
</body>

</html>

     


<!--<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>ProCash</title>
<link rel="shortcut icon" href="{{ asset(config('constants.logoIcon.path') .'/favicon.png') }}" type="image/x-icon">
<link rel="icon" href="{{ asset(config('constants.logoIcon.path') .'/favicon.png') }}" type="image/x-icon">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> Principal {{ $general->sitename(__($page_title)) }}</title>

 <link rel="stylesheet" type="text/css" id="applicationStylesheet" href="css/home.css"/>
<script id="applicationScript" type="text/javascript" src="js/home.js"></script> 
</head>


    
    @yield('css')
    @stack('css')

<body>
<div class="container">
@yield('content')
</div>



</body>
</html>
-->
     
