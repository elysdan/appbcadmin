@extends('admin.layouts.master')

@push('script')

@endpush

@section('content')

@php
      $nf = name_nfts($nfts->nfts_id);
@endphp
<div class="content">
    <div class="row">

         <div class="col-md-4 col-sm-12">
                    <div class="card text-left">
                        <img class="card-img-top" src="https://gdenetwork.club/dist/img/nfts/{{$nf->imagen}}" style='height:350px' alt="">
                        <div class="card-body">
                           <h4 class="card-title">{{$nf->Nombre}}</h4>
                           <h4 class="card-title">{{$nf->Precio}} USD</h4>
                        </div>
                    </div>
         </div>

         <div class="col-md-8 col-sm-12">   
                <form      @if($nfts->status == 0) action="{{route('admin.nfts.confirma')}}"  method='post' @endif>
                   @csrf
                   <input type="hidden" id='id' value='{{$nfts->id}}' name='id'>
                    <div class="card text-left">
                        <div class="card-body">
                        <h4 class="card-title">Detalle de la compra del nfts</h4>

                        <p>Username</p>
                        <p class="card-text">
                                <input type="text" class='form-control'  value='{{$usuario->username}}' readonly>
                        </p>

                        <p>Wallet Username</p>
                        <p class="card-text">
                                <input type="text" class='form-control' value='{{$usuario->wallet_polygon}}' readonly >
                        </p>

                        <p>Hash de la transacción</p>
                        <p class="card-text">
                                <input type="text" value='{{$nfts->hash}}'  class='form-control' value='' required name='hash' id ='hash' >
                        </p>

                        <p class="card-text">
                               @if($nfts->status == 0)
                                        <button  class='btn btn-success btn-lg'>
                                                Confirmar
                                        </button>
                                @endif
                        </p>
                        </div>
                    </div>
                </form>
         </div>
     
 
        
        
       
        </div>
    </div>
@endsection