@extends('admin.layouts.master')

@section('content')
<style>
    .text-right{
        text-align:center;
    }
</style>
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
                            <th scope="col" class='text-left'>Username</th>
                            <th scope="col" class='text-left'>Nfts</th>
                            <th scope="col" class='text-right'>Precio Nfts</th>
                        </tr>
                        </thead>
                        
                        <tbody>
                                @forelse( $usernfts as $data )
                                    <tr>
                                        <td class='text-center'>{{ show_datetime($data->created_at) }}</td>
                                        <td class='text-left'><a href="#">{{el_ref($data->user_id)->username }}</a></td>
                                        <td class='text-left'>{{name_nfts($data->nfts_id)->Nombre}}</td>
                                        <td class='text-right'>{{round($data->Precio,2)}} USD </td> 
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
                        {{ $usernfts->appends($_GET)->links() }}
                    </nav>
                </div>
                
            </div>
        </div>
    </div>


   
 
@endsection


