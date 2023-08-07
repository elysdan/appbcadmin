@extends('admin.layouts.master')

@section('content')
    <div class="row">

        <div class="col-lg-12" style='margin:20px; margin-bottom:10px;'>
            
        
            <form action='{{route("admin.deposit.buscar")}}'>
                    <div class="row align-items-center">

                                    <div class="col-8">
                                        <input type="text" id="inputPassword6" name='username' class="form-control" >
                                    </div>
                                    <div class="col-auto">
                                    <button class='btn btn-success'>Buscar</button>
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
                            <th scope="col" class='text-center'>Moneda</th>
                            <th scope="col" class='text-right'>Amount</th>
                            <th scope="col" class='text-center'>Hash</th>
                            <th scope="col" class='text-center'>Estado</th>
                            <th scope="col" class='text-center'>Edit</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                        @forelse( $deposits as $deposit )
                            <tr>
                                <td class='text-center'>{{ show_datetime($deposit->created_at) }}</td>
                                <td class='text-center'><a href="#">{{el_ref($deposit->user_id)->username }}</a></td>
                               <td class='text-center'> USDT  @switch($deposit->red)
                                        @case('trc20')
                                                TRC20-TRON
                                        @break
                                        @case('bep20')
                                                BEP20-BINANCE
                                        @break
                                    </tr>
                                @endswitch
                              
                                 </td>
                                 <td class='text-right'>
                                       {{number_format($deposit->amount,2,',','')}}             
                                </td>
                                <td class='text-center'>
                                        @switch($deposit->red)
                                                @case('trc20')
                                                <a href='https://tronscan.org/#/transaction/{{$deposit->id_tx}}'>{{substr($deposit->id_tx,0,15)}}</a>
                                                @break
                                                @case('bep20')
                                                <a href='https://bscscan.com/tx/{{$deposit->id_tx}}'>{{substr($deposit->id_tx,0,15)}}</a>
                                                @break
                                        @endswitch
                                </td>

                                <td class='text-center'>
                                        @switch($deposit->red)
                                                @case(0)
                                                       Pendiente @break;
                                                @case(1) 
                                                      Confirmado @break;
                                                @case(2) 
                                                      Rechazado @break;
                                        @endswitch
                                </td>
                                
                                <td class='text-center'>

                                    <a href="{{route('admin.deposit.detail',$deposit->id) }}" style='margin-left:0px;' >
                                           <i class="fa fa-edit " style='font-size:20px'></i>
                                    </a>

                                    <a href="{{route('admin.deposit.confirma',[$deposit->id, '1']) }}" style='margin-left:30px;' >
                                        <i class="fa fa-check text-success" style='font-size:24px' aria-hidden="true"></i>
                                    </a>
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
                        {{ $deposits->appends($_GET)->links() }}
                    </nav>
                </div>
                
            </div>
        </div>
    </div>


    {{-- VIEW MODAL --}}
    <div id="viewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ver Depósito </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method='post' action=''>
                <div class="modal-body">
                    <div class="row">
                           Username : <span class='mo_user'>
                    </div>
                    <div class="row">
                        <div class="col-4">
                                 Fecha : <span class='mo_fecha'>
                        </div>
                        <div class="col-4">
                                 monto : <span class='mo_monto'>
                        </div>
                        <div class="col-4">
                                 RED : <span class='mo_red'>
                        </div>
                    </div>
                    <div class="row">
                           Hash : <a class='mo_link' href="#"><span class='mo_hash'></span></a>
                    </div>
                    <div>
                          <label>Estado</label>
                          <select name='estado' class='form-control'>
                                 <option>Pendiente</option>
                                 <option>Confirmado</option>
                                 <option>Rechazado</option>
                            <select>
                   </div>
                </div>
                <div class="modal-footer">
                
                                 <input type='text' name='id_deposit' value=''>
                                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                 

                </div>
                <button type="button" class="btn btn-dark" data-dismiss="modal">Guardad</button>            
                   </form>
            </div>
        </div>
    </div>
   
 
@endsection


