@extends('admin.layouts.master')


@section('content')

<div class="page-content">


          @include('admin.partials.breadcrumb')

          <div class="content-wrapper">

            <div class="container-fluid p-0">

              @yield('panel')

            </div>

          </div>

          <footer class="footer"></footer>

      </div>

  </div>

</div>

@endsection