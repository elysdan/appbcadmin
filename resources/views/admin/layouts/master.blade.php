<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Admin gdenetwork</title>
		<link rel="icon" type="image/png" href="{{asset('dist/img/favicon.png')}}"/>
	

<!--	<link href="{{asset('assets/fonts/inter/inter.css')}}" rel="stylesheet" type="text/css"> -->
<!--	<link href="{{asset('assets/icons/phosphor/styles.min.css')}}" rel="stylesheet" type="text/css"> -->
	<link href="{{asset('dist/css/ltr/all.min.css')}}" id="stylesheet" rel="stylesheet" type="text/css">
	
	<script src="https://kit.fontawesome.com/33e0e03e46.js" crossorigin="anonymous"></script>
 
	
	<script src="{{asset('assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
	
	<script src="{{asset('assets/js/vendor/visualization/d3/d3.min.js')}}"></script>
	<script src="{{asset('assets/js/vendor/visualization/d3/d3_tooltip.js')}}"></script>

	<script src="{{asset('dist/js/app.js')}}"></script>
	<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
	<!-- /theme JS files -->

 

  </head>
<body>

@include('admin.partials.topnav')

<div class="page-content">

@include('admin.partials.menu')


<div class="content-wrapper">
<div class="content-inner">
{{--
	@include('admin.partials.breadcrumb')
--}}
  

  @yield('content')

</div>

</div>

 
</body>
</html>
