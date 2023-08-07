@extends('admin.layouts.master')

@section('content')
    <div class="row">

        <div class="col-lg-12" style='margin:20px; margin-bottom:10px;'>
            
            <div class="row">

                    <div class="col-8" style='padding-bottom:3px;'>
                    <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#new_member">+ Membresia</a>
                    </div>
            </div>

            <form action='{{route("admin.membresias.buscar")}}'>
                    <div class="row align-items-center">

                                    <div class="col-8">
                                        <input type="text"  name='userna' class="form-control" >
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
                            <th scope="col" class='text-center'>Membresia</th>
                            <th scope="col" class='text-right'>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse( $Membresias as $membresia )
                            <tr>
                                <td class='text-center'>{{ show_datetime($membresia->created_at) }}</td>
                                <td class='text-center'><a href="#">{{el_ref($membresia->user_id)->username }}</a></td>
                                <td class='text-center'>Membresia de {{number_format($membresia->precio,0,',','')}}</td>
                            
                                 <td class='text-right'>
                                       {{number_format($membresia->precio,2,',','')}}             
                                </td>
                             

                              
                                
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
                        {{ $Membresias->appends($_GET)->links() }}
                    </nav>
                </div>
                
            </div>
        </div>
    </div>


    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="new_member" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar membresia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

                <form action="{{route('admin.membresias.buy')}}" method='post'>
                @csrf
                    <div class="modal-body">

                                  <div class="form-group">
                                    <label for="">Username</label>
                                     <input type="text" name='username' id='username' class="form-control">
                                  </div>
                                  <div class="form-group" style='margin-top:5px margin-bottom:10px;'>
                                    <label for="">Membresia</label>
                                     <select name="member" id="member" class="form-control">
                                           <option value=''>Seleccione</option>
                                           @foreach($paquete as $data)
                                                <option value='{{$data->id}}'>{{$data->nombre}} {{round($data->precio)}}</option>
                                           @endforeach
                                          
                                     </select>
                                  </div>

                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>

    </div>
  </div>
</div>





   
 
@endsection


