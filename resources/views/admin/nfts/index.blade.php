@extends('admin.layouts.master')

@section('content')
<style>
    .tag_img{
        width:50px;
        height:50px;
    }
    .text-right{
        text-align:right;
    }
</style>
    <div class="row">

        <div class="col-lg-12" style='margin:20px; margin-bottom:10px;'>
            
        
            <form action='{{route("admin.participar.buscar")}}'>
                    <div class="row align-items-center">

                                    <div class="col-8">
                                    </div>
                                    <div class="col-2 text-right">
                                       <!-- <button class='btn btn-success'> Nueva</button>  -->
                                    </div>
                    </div>
            </form>
          

            <div class="card" style='margin-top:5px;'>

                 
                <div class="table-responsive table-responsive-xl">
                    <table class="table align-items-center">
                        <thead>
                                <tr>
                                    <th scope="col" class='text-center'>Imagen</th>
                                    <th scope="col" class='text-left'>Nombre</th>
                                    <th scope="col" class='text-right'>Cantidad</th>
                                    <th scope='col' class='text-right'>Precio</th>
                                    <th scope="col" class='text-center'>Acción</th>
                                </tr>
                        </thead>
                        <tbody>
                        @forelse( $nfts as $data )
                            <tr>
                                <td class='text-center' style='width:50px'><img src='https://gdenetwork.club/dist/img/nfts/{{$data->imagen}}' class='tag_img'></td>
                                <td class='text-left'>{{ $data->Nombre}}</td>
                                <td class='text-right'>{{$data->Cantidad}}</td>
                                <td class='text-right'>{{round($data->Precio,2)}} USD</td>
                                <td class='text-center'><a href="{{route('admin.my.nfts.edit', $data->id)}}"><i class="fas fa-edit"></i><a></td>
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
                        {{ $nfts->appends($_GET)->links() }}
                    </nav>
                </div>
                
            </div>
        </div>
    </div>


   
 
@endsection


