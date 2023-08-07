@extends('admin.layouts.master')

@section('content')
    <div class="row">

        <div class="col-lg-12" style='margin:20px; margin-bottom:10px;'>
            
        
            <form action='{{route("admin.participar.buscar")}}'>
                    <div class="row align-items-center">

                                    <div class="col-8">
                                        <input type="text" id="inputPassword6" name='userna' class="form-control" >
                                    </div>
                                    <div class="col-auto">
                                    <button class='btn btn-success'>Buscar</button>
                                    </div>
                                    <div class="col-3">
                                    <input type='text' readonly class='form-control' style='text-align:right' value='Total {{@$total}}'>
                                    </div>
                        
                    </div>
            </form>
          

            <div class="card" style='margin-top:5px;'>

          
                 
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center">
                        <thead>
                        <tr>
                            <th scope="col" class='text-center'>Fecha</th>
                            <th scope="col" class='text-center'>Username</th>
                            <th scope="col" class='text-center'>Inversion</th>
                            <th scope="col" class='text-center'>Precio GDEtoken</th>
                            <th scope="col" class='text-right'>GDEtoken</th>
                            <th scope="col" class='text-right'>GDEtoken P. Actual</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse( $Participar as $parti )
                            <tr>
                                <td class='text-center'>{{ show_datetime($parti->created_at) }}</td>
                                <td class='text-center'><a href="#">{{el_ref($parti->user_id)->username }}</a></td>
                                <td class='text-center'>{{number_format($parti->inversion,2,',','')}} </td>
                                <td class='text-center'>{{number_format($parti->precio_cripto,2,',','')}} </td>
                                <td class='text-center'>{{number_format($parti->gdetoken,2,',','')}} </td>
                                <td class='text-center'>{{number_format($parti->gdetoken * $precio_actual,2,',','')}} </td>        
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ $empty_message }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4">
                    <nav aria-label="...">
                        {{ $Participar->appends($_GET)->links() }}
                    </nav>
                </div>
                
            </div>
        </div>
    </div>


   
 
@endsection


