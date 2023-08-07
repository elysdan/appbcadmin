@extends('admin.layouts.master')

@push('script')

@endpush

@section('content')

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<style>
   .text-right{
        text-align: right;
   }
</style>
<script>
	res = "false"
	 $(function(){
          $('.data_sele').change(function() {
                total_retiro();
           });

		    $('#data_sele_pri').change(function() {
                res = $(this).prop("checked");
                res =  res.toString();
                if(res == 'false') 
                   $(".data_sele").bootstrapToggle('off')
                else
                   $(".data_sele").bootstrapToggle('on')
		    })
        
	 });


  

     function total_retiro(){
         total = 0;
        
      $("#tab_w tbody tr").each(function(index, fila) {

            obj   = $(this).find('div').find(".data_sele").prop("checked");
            monto = fila.children[6].innerHTML;
            valor = parseFloat(monto);
             if(obj == true)
             {
               total += parseFloat(valor);
             }
        });
       $("#total_retirar").val(total.toFixed(2));
       
     }
	  
	    	
	   //   
</script>
<div class="content">
    <div class="row" >
     
    <form action="{{route('admin.retiros.pay')}}" method='post'>
    @csrf
        <div class="col-lg-12" style=' padding:20px; margin-bottom:10px;'>
            

            <div class="card" style='margin-top:5px;'>

                <div class="table-responsive table-responsive-xl">
                    <table id= 'tab_w' class="table align-items-center">
                        <thead>
                        <tr>
                            <th scope="col" class='text-center'>USERNAME</th>
                            <th scope="col" class='text-left'>WALLET</th>
                            <th scope="col" class='text-right'>USDT</th>
                            <th scope="col" class='text-right'>PRECIO MATIC</th>
                            <th scope='col' class='text-right'>FEE</th>
                            <th scope="col" class='text-right'>MATIC</th>
                            <th scope="col" class='text-right'>PAGO 80%</th>
                            <th scope="col" class='text-right'>RETIENE 20%</th>
                            <th scope="col" class='text-center'><input type='checkbox' id='data_sele_pri' data-toggle="toggle" data-on="SEND" data-off="NO" data-onstyle="success" data-offstyle="danger" id='' name= ''></th>
                        </tr>
                        </thead>
                        <tbody class='tbody'>
                            
                            @php
                                  $precio_token = dolar_matic();
                            @endphp
                            
                            @forelse( $usuarios as $parti )
                                         @php
                                                    $por = 3;
                                                    $fee = ($parti->usdt * $por) / 100;
                                                    $pago  = $parti->usdt - $fee;
                                                    $pay80 = number_format($pago * 0.8,3,'.','');
                                                    $pay20 = number_format($pago * 0.2,3,'.','');
                                         @endphp
                                <tr>
                                    <td class='text-center'><a href='#'>{{@$parti->username}}</a></td>
                                    <td class='text-left'>{{@$parti->wallet_polygon}}</td>
                                    <td class='text-right'>{{round(@$parti->usdt,2)}}</td>
                                    <td class='text-right'>{{round($precio_token,3)}}</td>
                                    <td class='text-right'>{{round(@$fee,3)}}</td>
                                    <td class='text-right'>{{round(@$pago / $precio_token,2)}}</td>
                                    <td class='text-right'>{{round(@$pay80 / $precio_token,2)}}</td>
                                    <td class='text-right'>{{round(@$pay20 / $precio_token,2)}}</td>
                                    
                                    <td class='text-center'><input type='checkbox'  class='data_sele' data-width="100" data-onstyle="success" data-offstyle="danger"
    									data-toggle="toggle" data-on="SEND" data-off="NO" name="user[{{$parti->id}}]" ></td>        
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
                        {{$usuarios->appends($_GET)->links()}}
                    </nav>
                </div>
                
            </div>
            <div class='col-lg-12'> 
                  <div class='row'>
                    <label>Monto a pagar MATIC</label>
                 </div> 
                  <div class='row'>
                     <div class="col-2"><input type='text' class='form-control text-right' id = 'total_retirar' value = '0'></div>
                     <div class='col-auto'> <button class='btn btn-warning'>Retirar</button></div>
                 </div>
                   
                 
            </div>
          </form>
        </div>
    </div>
    </div>
 
@endsection


