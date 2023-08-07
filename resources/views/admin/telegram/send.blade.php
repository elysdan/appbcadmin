@extends('admin.layouts.master')

@section('content')
    <div class="row">

        <div class="col-lg-12" >
            
        
        

            <div class="card" style='margin:10px; padding:10px;'>

                   <form method="post" action="{{route('admin.masivo.send')}}">
                   @csrf
                       <div class="form-group">
                              <label for="" style='margin-bottom:10px;'>Mensaje</label>
                              <textarea name="mensaje" style='height:100px; padding:5px' class='form-control' id=""></textarea>
                       </div>                    
    
                 
               
                <div class="card-footer py-4">
                        <div class='form-group' style='margin-top:10px; '>
                              <button class='btn btn-success'> Enviar Mensaje </button>
                        </div>
                 
                </div>

                </form>
                
            </div>
        </div>
    </div>


   
 
@endsection


