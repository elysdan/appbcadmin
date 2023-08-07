<?php

namespace App\Http\Controllers\Admin;
use App\GeneralSetting;
use App\Trx;
use App\User;
use App\pago;
use App\Pre_withdraw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Withdrawal;
use App\participate;


class WithdrawalController extends Controller
{
      public function index(){
           $admin                  = Auth::guard('admin')->user(); 
           $general                = GeneralSetting::first();
           $usuarios               = User::where('wallet_wave','!=', '0')->where('usdt','>=',1)->paginate(100);
           $data['user']           = $admin;
           $data['usuarios']       = $usuarios;
           $data['page_title']     = 'Retiros';
           $data['empty_message']  = 'No hay usuarios para cobrar';
           $data['precio_token']   = $general->precio_gdetoken;
           return view('admin.retiros.index', $data);
      }

      public function pay(Request $request){
             
            $users = @$request->user;
            $general                = GeneralSetting::first();

            if(!$users){
            $notify[] = ['error', 'Debe seleccionar por lo menos un usuario']; 
            return back()->withNotify($notify);
           }

           foreach($users as $key=>$user){
            
                 $us = User::where('id', $key)->where('usdt','>=', 1)->where('wallet_wave','!=', '0')->first();

                 if($us){
                        //calculamos valores
                        $monto  =  $us->usdt;
                        $fee    =  0;
                        $charge =  $monto *  $fee;
                        $gano =  $monto - $charge;
                        $pay20  =  ($gano * 0.20);
                        $pay80  =  ($gano * 0.80);
                        $pay80_gde = number_format($pay80 / $general->precio_gdetoken,2,',','');
                        $us->usdt -= $monto;
                        $us->save();
                        
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
                        
                        send_tele($us, 'Ha recibido '.$pay80_gde.' GDEtoken por concepto de pago de comisiones.');

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
                 }
           }

           $notify[] = ['success', 'Retiros realizados con exito!']; 
           return back()->withNotify($notify);
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

      private function pay_participaciones(){
          
      }


}
