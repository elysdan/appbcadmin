@extends('admin.layouts.master')

@section('content')


<div class="row" style='padding:10px'>

<div class="col-lg-12 ">
    <div class="card">

          <div class="card-header" style='margin:10px;'>
          <div class="row">
                        <div class="col-12">
                           Username : <span class='mo_user'> {{el_ref($deposits->user_id)->username}} </div>
                    </div>
          </div>

          <div class="card-body" >
          <form method='post' action='{{route("admin.deposit.save")}}'>
          @csrf
                    <div class="row">
                        <div class="col-4">
                               <div class='col-6'> 
                                     <h6> Fecha : </h6> 
                               </div><span class='mo_fecha'> 
                                <div>
                                        {{$deposits->created_at}}
                                </div>
                        </div>
                        <div class="col-4">
                        <div class='col-6'> 
                                     <h6> Monto: </h6> 
                               </div><span class='mo_fecha'> 
                                <div>
                                        {{$deposits->amount}}
                                </div>
                               
                        </div>
                        <div class="col-4">
                                     <h6> Red: </h6> 
                                <span class='mo_fecha'> 
                                <div>
                                        {{$deposits->red}}
                                </div>
                        </div>
                    </div>
                    <div class="row" style='margin-top:10px'>
                    <div class='col-6'> 
                                     <h6>   Hash : </h6> 
                               </div><span class='mo_fecha'> 
                                <div>
                                @switch($deposits->red)
                                                @case('trc20')
                                                <a target='_blank' href='https://tronscan.org/#/transaction/{{$deposits->id_tx}}'>{{$deposits->id_tx}}</a>
                                                @break
                                                @case('bep20')
                                                <a target='_blank' href='https://bscscan.com/tx/{{$deposits->id_tx}}'>{{$deposits->id_tx}}</a>
                                                @break
                                        @endswitch
                                  
                                </div>
                    </div>

                    <div class='row' style='margin-top:15px; margin-bottom:25px'>
                          <label>Estado</label>
                          <select name='estado'  @if($deposits->status > 0) disabled @endif class='form-control'>
                                 <option value='0' @if($deposits->status == 0) selected @endif>Pendiente</option>
                                 <option value='1' @if($deposits->status == 1) selected @endif>Confirmado</option>
                                 <option value='2' @if($deposits->status == 2) selected @endif>Rechazado</option>
                            <select>
                   </div>
                   <input type='hidden' name='id_deposit' value='{{$deposits->id}}'>

                   <div class='row' style='margin-top:15px'>
                               <button @if($deposits->status > 0) disabled @endif class="btn btn-success" >Confirmar</button>
                   </div>

                </div>
          </div>
               
              
                </form>
    </div>
</div>
</div>
        

@endsection


