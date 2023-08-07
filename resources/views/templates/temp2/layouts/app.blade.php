@extends(activeTemplate().'layouts.user-master')

@section('panel')
   

<div class="wrapper">
    @include(activeTemplate().'partials.topnav')
      

        @include(activeTemplate().'partials.sidenav')

           @include(activeTemplate().'partials.breadcrumb')

        @yield('content')
  
</div>

@endsection



