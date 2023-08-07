<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Admin GDENETWORK</title>
	<link rel="icon" type="image/png" href="{{asset('dist/img/favicon.png')}}"/>

	<link href="{{asset('assets/fonts/inter/inter.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('assets/icons/phosphor/styles.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('dist/css/ltr/all.min.css')}}" id="stylesheet" rel="stylesheet" type="text/css">
	
	<script src="{{asset('assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
	<script src="{{asset('assets/js/vendor/visualization/d3/d3.min.js')}}"></script>
	<script src="{{asset('assets/js/vendor/visualization/d3/d3_tooltip.js')}}"></script>

	<script src="{{asset('dist/js/app.js')}}"></script>
	
	<!-- /theme JS files -->

  @stack('style-lib')
  @stack('style')
  @stack('css')

  </head>
<body>


<div class="page-content">

<div class="content-wrapper">

  @yield('content')

</div>

</div>

  <!-- Load toast -->
  @include('admin.partials.notify')

</body>
</html>
