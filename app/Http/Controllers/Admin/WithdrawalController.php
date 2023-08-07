<?php

namespace App\Http\Controllers\Admin;
use App\GeneralSetting;
use App\Trx;
use App\User;
use App\pago;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Withdrawal;
use App\participate;
use App\Prew_withdraw;



class WithdrawalController extends Controller
{
      public function index(){
           $admin                  = Auth::guard('admin')->user(); 
           $general                = GeneralSetting::first();
           $usuarios               = User::whereNotNull('wallet_polygon')->where('usdt','>=',1)->paginate(50);
           $data['user']           = $admin;
           $data['usuarios']       = $usuarios;
           $data['page_title']     = 'Retiros';
           $data['empty_message']  = 'No hay usuarios para cobrar';
           $data['precio_token']   =  $general->precio_gdetoken;
           return view('admin.retiros.index', $data);
      }

      public function confirmadas(){
                 $admin                  = Auth::guard('admin')->user(); 
                 $pagos                  = pago::where('status',1)->paginate('15');
                 $data['user']           = $admin;
                 $data['page_title']     = 'Retiros';
                 $data['pago']           = $pagos;
                 $data['empty_message']  = 'No hay transacciones confirmadas';
                 return view('admin.retiros.pendi', $data);
      }

      public function pendientes(){

            $admin                  = Auth::guard('admin')->user(); 
            $pagos                  = pago::where('status',1)->paginate('15');
            $data['user']           = $admin;
            $data['page_title']     = 'Retiros';
            $data['pago']           = $pagos;
            $data['empty_message']  = 'No hay transacciones pendientes';
            return view('admin.retiros.conf', $data);

      }



      public function recargar_balance(){

            $admin                  = Auth::guard('admin')->user(); 
            $general                = GeneralSetting::first();
            $polygon                =  json_decode(smart_get('matic/balance/'.$general->wallet_polygon));
            $matic_p                =  round($polygon->result,6);
            $smart                  =  json_decode(smart_get('matic/balance/'.$general->polygon_smart));
            $matic_s                =  round($smart->result,6); 
            
            $data['wallet']         = $matic_p;
            $data['smart']          = $matic_s;   
             $data['wallet_owner']   = $general->wallet_polygon;
            $data['wallet_smart']   = $general->polygon_smart;
            $data['user']           = $admin;
            $data['page_title']     = 'Recargar Smart Contract';
            return view('admin.config.recarga', $data);

      }

      public function smart_carga(Request $request){

             $monto                 = $request->amount;
             $data                  = array('monto'=> $monto );
             $polygon               =  send_smart('matic/cargar_fondos', $data);
              
             if($polygon->result == 'ok')
             {
                  $notify[] = ['success', 'La transaccion ha sido enviada']; 
                  return back()->withNotify($notify);
             }else{
                  $notify[] = ['error', 'transaccion fallida por balance en la wallet']; 
                  return back()->withNotify($notify);
             }
      }

      public function pay(Request $request){
             
            $users                  =  @$request->user;
            $general                = GeneralSetting::first();
            if(!$users){
               $notify[] = ['error', 'Debe seleccionar por lo menos un usuario']; 
               return back()->withNotify($notify);
            }
            
            
           $i = 0;
          $pago =  $this->new_pago();
           foreach($users as $key=>$user){
            
                 $us = User::where('id', $key)->where('usdt','>=', 1)->whereNotNull('wallet_polygon')->first();
                 $precio_matic = dolar_matic();
              
                 
                 if($us){
                  
                        $monto         =  $us->usdt;
                        $fee           =  3;
                        $charge        =  ($monto *  $fee) / 100;
                        $gano          =  $monto - $charge;
                        $pay20         =  ($gano * 0.20);
                        $pay80         =  ($gano * 0.80);
                        $pay80_mat     =   number_format($pay80 / $precio_matic,6,'.','');
                        $dat_reng      =   (object)array(
                                                            'enviado' => $pay80_mat,
                                                            'precio'  => $precio_matic
                                            );
                                            
                                           
                        $this->renglon($pago, $us, $dat_reng);
                        $pagos_inline[] = array(
                                                'wallet'  => $us->wallet_polygon,
                                                'monto'   => round($pay80_mat,6)
                                                );
                        $i++;
                        $us->usdt -= $monto;
                        $us->save();
                 }
           }
           if($i > 0){
           //proceso de enviar transacciones
                $result =  $this->send_pay($pagos_inline);
                  
                  
                        if(@$result->res == 'ok')
                        {
                                          $hash = (string)$result->result;
                                          $pago->id_tx = $hash;
                                          $pago->status = 1;
                                          $pago->save();
                                          $notify[] = ['success', 'Transacción enviada!'];
                                          return back()->withNotify($notify);
                        }else{
                                          $notify[] = ['error', 'Servidor smart Contract pagado o blockchaim no responde!'];
                                          return back()->withNotify($notify);
                        }    

           }else{
                        $notify[] = ['error', 'Los usuarios no tienen wallet asiganda']; 
                        return back()->withNotify($notify);
           }
      }



      public function por_confirmar(){
            $admin                  = Auth::guard('admin')->user(); 
            $general                = GeneralSetting::first();
            $retiros                = Withdrawal::where('status',0)->paginate(100);
            $data['user']           = $admin;
            $data['usuarios']       = $usuarios;
            $data['page_title']     = 'Retiros Pendientes';
            $data['empty_message']  = 'No hay retiros por confirmar';
            $data['precio_token']   = $general->precio_gdetoken;
            return view('admin.retiros.index', $data);
       }

       public function historicos(){
            $admin                  = Auth::guard('admin')->user(); 
            $general                = GeneralSetting::first();
            $retiros                = Withdrawal::paginate(100);
            $data['user']           = $admin;
            $data['usuarios']       = $usuarios;
            $data['page_title']     = 'Retiros Pendientes';
            $data['empty_message']  = 'No hay retiros por confirmar';
            $data['precio_token']   = $general->precio_gdetoken;
            return view('admin.retiros.index', $data);
       }

      public function confirmarcion_pay(){
            $title = $user->username.' participation of '.$request->amount.' usd ';
            //pay_unilevel_retenido($user->ref_id,$request->amount, $title); 
      }

      public function retirar(){
          
      }

      public function new_pago(){
            $pay =  new pago();
            $pay->moneda  = 4;
            $pay->status  = 0;
            $pay->id_tx   = '';
            $pay->save();
            return $pay;
      }

      public function renglon($pago, $user, $datos){

            $tnxnum                 = getTrx();
            $pre                    = new Prew_withdraw();
            $pre->user_id           = $user->id;
            $pre->pago_id           = $pago->id;
            $pre->moneda            = 4;
            $pre->enviado_matic     = $datos->enviado;
            $pre->trx               = $tnxnum;
            $pre->balance           = $user->usdt;
            $pre->balance_matic     = $datos->enviado;
            $pre->precio_dolar      = $datos->precio;
            $pre->id_tx             = '';
            $pre->save();
      }

      private function pay_participaciones(){
        
      }

      function send_pay($valores){

            $usuarios = ""; $montos = "";$i=1;
           foreach($valores as $valor)
           {
                if($usuarios!=""){ $usuarios .= ",";  $montos   .= ","; } 
                $usuarios .= $valor['wallet'];
                $montos   .= $valor['monto'];
                $i++;
           }
           if($usuarios == "")
           {
                 return array('res'=>'1', 'result'=>'nothing');
           }
 
         $data = array('dire'=>$usuarios,  'monto'=> $montos );
         $result = send_smart('matic/pagar', $data);
         return $result;
  }

  public function confirmar(){
         
          //consultamos los retiros enviados en la blockchaim


              // este proceso ira a un cron jobs

                  /*
                        //calculamos valores
                        
                        $res                 = getTrx();
                        $retiro              = new Withdrawal();
                        $retiro->user_id     = $us->id;
                        $retiro->amount      = $monto;
                        $retiro->charge      = $charge;
                        $retiro->final_amo   = $gano;
                        $retiro->retenido    = $pay20;
                        $retiro->price_token = $general->precio_gdetoken;
                        $retiro->pagado       = $pay80;
                        $retiro->trx         = $res;
                        $retiro->currency    = 'GDEtoken';
                        $retiro->status      = 1;
                        $retiro->hash        = '';
                        $retiro->detail      = 'Retiro de '.$pay80_gde.' GDEtoken ('.$pay80.' USD)';
                        $retiro->save();

                        $trans           =  new trx();
                        $trans->trx      =  $res;
                        $trans->user_id  = $us->id;
                        $trans->amount   = $gano * -1;
                        $trans->main_amo = $gano * -1;
                        $trans->balance  = $us->usdt;
                        $trans->title    = $retiro->detail;
                        $trans->type     = 'retiro';
                        $trans->moneda   = 1;
                        $trans->save();
                       

                        //creamos las participaciones
                        $token = $pay20 / $general->precio_gdetoken;
                        $pat = new participate();
                        $pat->user_id       = $us->id;
                        $pat->inversion     = $pay20;
                        $pat->precio_cripto = $general->precio_gdetoken;
                        $pat->gdetoken      =  $token;
                        $pat->origen        = 1;
                        $pat->save();
                      
                        $titulo =   $token. ' gdetoken de participación precio '.number_format($general->precio_gdetoken,2,',','');
                        transaccion($us, ($request->amount * -1), $titulo, 'parti_rete' );
                        $us->gdetoken += $token;
                        $us->save();
                        $title = $us->username.' participation of '.$pay20.' usd ';

                        pay_unilevel($us->ref_id,$pay20, $us->username);
                        //

                        */
      
  }

  public function restaurar(){
                //Resturamos las transacciones fallidas en la  blockchaim

                 
         $pago = pago::where('id',$request->id_pago)->where('status','1')->first();
         $preview = Prew_withdraw::where('pago_id',$pago->id)->get();
         
               foreach($preview as $data){
                                                $pagos_inline[] = array(
                                                        'wallet'=>$data->user_id,
                                                        'monto'=>round($data->enviado_trx,6)
                                                        );
                                         }
               
               $result =  $this->send_pay(@$pagos_inline);
                                 if(@$result->res == 'ok')
                                 {
                                    $hash = (string)$result->result;
                                    $pago->id_tx = $hash;
                                    $pago->status = 1;
                                    $pago->save();
                                    $notify[] = ['success', 'La transaccion fue enviada satifactoriamente!'];
                                    return back()->withNotify($notify);
                                 }else{
                                    $notify[] = ['error', 'Servidor smart Contract apagado o blockchaim no responde!'];
                                    return back()->withNotify($notify);
                                 }    
  }

      


}
