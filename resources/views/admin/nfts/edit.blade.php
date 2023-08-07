@extends('admin.layouts.master')

@push('script')

@endpush

@section('content')

<div class="content">
    <div class="row">

         <div class="col-md-4 col-sm-12">
                    <div class="card text-left">
                        <img class="card-img-top" src="https://gdenetwork.club/dist/img/nfts/{{$nfts->imagen}}" style='height:350px' alt="">
                      
                    </div>
         </div>

         <div class="col-md-8 col-sm-12">   
                <form    action="{{route('admin.nfts.update')}}"  method='post'>
                   @csrf
                   <input type="hidden" id='id' value='{{$nfts->id}}' name='id'>
                    <div class="card text-left">
                        <div class="card-body">
                        <h4 class="card-title">Información</h4>

                        <p>Nombre</p>
                        <p class="card-text">
                                <input type="text" class='form-control'  value='{{$nfts->Nombre}}' name = 'nombre'>
                        </p>

                      <p>Cantidad</p>
                        <p class="card-text">
                                <input type="text" class='form-control' value='{{$nfts->Cantidad}}'  name = 'cantidad' >
                        </p>

                        <p>Precio</p>
                        <p class="card-text">
                                <input type="text" class='form-control'  value='{{$nfts->Precio}}' name = 'precio'>
                        </p>

                        <p>Disponible</p>
                        <p class="card-text">
                                <input type="text" class='form-control'  value='{{disponible($nfts->id)}}' readonly >
                        </p>

                        <p>Descripción</p>
                        <p class="card-text">
                               <textarea class='form-control' name="descripcion" id="descripcion" cols="20" rows="10">{{$nfts->Descripcion}}</textarea>
                        </p>

                        <p class="card-text">
                                        <button  class='btn btn-success btn-lg'>
                                                Save
                                        </button>
                        </p>
                        </div>
                    </div>
                </form>
         </div>
     
 
        
        
       
        </div>
    </div>
@endsection