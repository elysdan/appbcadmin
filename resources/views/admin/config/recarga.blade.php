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
            monto = fila.children[5].innerHTML;
            monto = monto.split(" ");
            valor = parseFloat(monto[0]);
             if(obj == true)
             {
               total += valor;
             }
        });
       $("#total_retirar").val(total);
       
     }
	  
	    	
	   //   
</script>
<div class="content">
    <div class="row">

         <div class="col-md-4 col-sm-12">
                    <div class="card text-left">
                        <img class="card-img-top" src="{{asset('dist/img/polygon.jpg')}}" style='height:350px' alt="">
                        <div class="card-body">
                           <h4 class="card-title">Wallet Propetaria</h4>
                               <p>{{@$wallet_owner}}</p>
                             <p class="card-text">Balance <b>{{@$wallet}}</b> Matic</p>
                            <h4 class="card-title">Smart Contract</h4>
                             <p class="card-text">Balance <b>{{@$smart }}</b> Matic</p>
                        </div>
                    </div>
         </div>

         <div class="col-md-8 col-sm-12">   
                <form action="{{route('admin.smart.recarga')}}" method='post'>
                   @csrf
                    <div class="card text-left">
                        <div class="card-body">
                        <h4 class="card-title">Agregue la cantidad de matic a recargar</h4>
                        <p>{{@$wallet_smart}}</p>
                        <p class="card-text">
                                <input type="text" class='form-control' required name='amount' id= 'amount'>
                        </p>
                        <p class="card-text">
                                <button class='btn btn-success btn-lg'>
                                            Recargar
                                </button>
                        </p>
                        </div>
                    </div>
                </form>
         </div>
     
 
        
        
       
        </div>
    </div>

 
@endsection


