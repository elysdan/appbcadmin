@extends(activeTemplate() .'layouts.app')

@section('style')

@stop

@section('content')

<div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Depósitos</h1>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
<!-- CONTENT AREA -->
<main>

<form method= 'post' action="{{route('user.deposit.report')}}" class="col-sm-12 col-md-12">
    <div class="row" style='margin-bottom:40px;'>

        <style>   
                #img_wallet{
                         width:200px;
                         height:200px;
                         
                }
                #img_wallet img{
                    width:180px;
                }
        </style>
       
        <div class="col-md-6 col-sm-12" >
            <div class="widget">
                <div class="justify-content-center align-items-center">
                     <div class='row' style='padding:0 30px;'> 
                          <select class='form-control' name='red' id='id_deposit'>
                                <option value='bep20'>USDT BEP20-BINANCE</option>
                                <option value='trc20'>USDT TRC20-TRON</option>
                          </select>
                   </div>
                   <center>
                    <div id='img_wallet' style='padding:0; margin:0'>
                              <img style='margin-top:10px; padding:0'  id='la_imagen' src='{{asset("dist/img/qr_bep20.png")}}'>
                    </div>

                    <div class='row justify-content-center align-items-center' style='margin-top:10px'>
                                    <div class='col-10'>
                                        <input type='text' readonly class='form-control' value ='{{$wallet_bep20}}' id='wallet' name='wallet'>
                                        <div style='display:none' id='mi_wallet'>{{$wallet_bep20}}</div>
                                    </div>

                                    <div class='col-1' style='padding:0;margin:0;' class='text-left'>
                                        <button type='button' class='btn btn-primary' onclick='copiar("mi_wallet")'><i class="fa fa-copy"></i></button>
                                    <div>
                    </div>               
                  </center>
                </div>
                <div class='row justify-content-center align-items-center'>
                        <!-- <span style='color:#f00; '><h6>RED: USDT-TRC20</h6></span> -->
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12" style='padding-top:20px'>
            <div class="widget">
                <div class="row justify-content-center align-items-center">
                    @csrf
                                  <input type="hidden" name="currency" class="edit-currency"
                                               value="USDT">
                                        <input type="hidden" name="method_code" class="edit-method-code"
                                               value="506">
                      
                        

                            <div class="col-xl-12 col-lg-12 col-sm-12">
                                <input type="text" class="form-control" id="hash" name="hash" placeholder="Ingrese TxID ">
                            </div>

                            <div class="col-xl-12 col-lg-12 col-sm-12" style='margin-top:10px'>
                                <input type="text" class="form-control" id="amount" name="amount" placeholder="Ingrese monto">
                            </div>

                   
                    
                         <button type="submit" data-currency="usdt" data-method_code="506"  class="btn btn-primary btn-block mt-3 ">Reporta tu depósito</button>
                </div>
            </div>
        </div>
        
    </div>
    </form>
    <div class="row">
        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table mb-4">
                                            <thead>
                                                    <tr>
                                                        <th class="text-center">@lang('Id')</th>
                                                        <th class="text-center">@lang('Fecha de depósito') </th>
                                                        <th class="text-center">@lang('Monto') </th>
                                                        <th class="text-center">@lang('Moneda') </th>
                                                        <th class="text-center">@lang('Hash')</th>
                                                        <th class="text-center">@lang('Estado')</th>
                                                    </tr>
                                            </thead>
                                    <tbody>
                                    @forelse($deposits as $key=>$data)
                                            <tr>
                                                 <td class='text-center'>{{$data->id}}</td>
                                                 <td class='text-center'>{{$data->created_at}}</td>
                                                 <td class='text-center'>{{number_format($data->amount,2,',','')}}</td>
                                                 <td class='text-center'>{{strtoupper($data->red)}} - {{$data->method_currency}}</td>
                                                 <td class='text-center'>
                                                      @if($data->red == 'trc20')
                                                        <a target='_blank' href='https://tronscan.org/#/transaction/{{$data->id_tx}}'>{{substr($data->id_tx,0,15)}} ..</a>
                                                      @else
                                                      <a target='_blank' href=' https://bscscan.com/tx/{{$data->id_tx}}'>{{substr($data->id_tx,0,15)}} ..</a>
                                                      @endif
                                                 </td>
                                                 <td class='text-center'>
                                                     @if($data->status == 0 )
                                                          Pendiente
                                                     @else 
                                                          @if($data->status == 2)
                                                               Rechazada
                                                          @else
                                                               Confirmada
                                                          @endif
                                                     @endif
                                                     </td>
                                            </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">@lang('No hay datos')</td>
                                        </tr>
                                    @endforelse
                                    </tbody>

                                </table>
          
                            </div>

                            <nav aria-label="...">
                    {{$deposits->links()}}
                    </nav>
        </div>
    </div>   

</main>
<!-- CONTENT AREA -->

</div>
</section>

</div>
 





@endsection
@push('js')
<script></script>

<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
         <script>
          
                 $('#id_deposit').change(function(){
                       valor = $(this).val();
                          

                       switch(valor){
                            case 'trc20':  
                                            $('#wallet').val('{{$wallet_deposito}}');
                                            $('#mi_wallet').html('{{$wallet_deposito}}')
                                            $('#la_imagen').attr('src','{{asset("dist/img/qr_trc20.png")}}');
                                break;
                            case 'bep20': 
                                           $('#wallet').val('{{$wallet_bep20}}');
                                           $('#mi_wallet').html('{{$wallet_bep20}}')
                                           $('#la_imagen').attr('src','{{asset("dist/img/qr_bep20.png")}}');
                                break;
                       }
                 })
        </script>



@endpush
3