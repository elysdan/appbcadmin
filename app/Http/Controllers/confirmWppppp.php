<?php

namespace App\Http\Controllers;
use App\CompenstionPlan;
use App\GeneralSetting;
use App\Product;
use App\Trx;
use App\User;
use App\Share;
use App\pago;
use App\Prew_withdraw;
use App\UserExtra;
use Carbon\Carbon;
use App\Deposit;
use App\Buy_ticket;
use App\Ticket;
use App\sorteo;
use App\Withdrawal;
use App\Http\base58;
use App\WithdrawMethod;
use App\NetworkContract;
use Illuminate\Http\Request;
use App\GatewayCurrency;
use Illuminate\Support\Facades\Auth;

class confirmW extends Controller
{
    
      public function validar_matic(){
          
                    set_time_limit(0);
                    $lotes = pago::where('status',1)->where('moneda',4)->get();
                    
                    foreach($lotes as $pag){
                         $resp = $this->valida_hash($pag->id_tx);
                         
                         if($resp->result->status == 1){
                                $this->send_register($pag);
                         }else{
                                 echo 'has faild';
                         }
                           
                       
                           
                         echo '<hr>';
                        
                    }
      }
      
      
      public function valida_hash($hash){
          
            
              $url  = "https://api.polygonscan.com/api";
              $url .= "?module=transaction";
              $url .= "&action=gettxreceiptstatus";
              $url .= "&txhash=".$hash;
              $url .= "&apikey=1MMHVQU8YHRCXWPGHTA7N5UBHCD5HD3AC1";
              $res =   file_get_contents($url);
               $res =json_decode($res);
              return $res;
                        
      }
      
      
   
      
    

      public function send_register($pago){
          
           $method = WithdrawMethod::where('id',2)->first();
           $pago->status = 3;
           $pago->save();
           $pay = Prew_withdraw::where('pago_id',$pago->id)->get();   
           $hash = (string)$pago->id_tx;

           foreach($pay as $pa){
                                     
                            $wdamo            = $pa['solicitud'];
                            $charge           = $pa['carga'];
                            $amountSend       = $pa['monto'];
                            $enviado          = $pa['enviado_matic'];
                            $interes_wallet   = $pa['balance'];
                            $user             = User::findOrFail($pa['user_id']);
                            $tnxnum           = $pa['trx'];
                     
                            
                            $tnxnum =  getTrx();
                            $withdraw = new Withdrawal();
                            $withdraw->user_id = $user->id;
                            $withdraw->amount = ($wdamo);
                            $withdraw->charge = ($charge);
                            $withdraw->rate = 1;
                            $withdraw->currency = 'USD';
                            $withdraw->delay = 0;
                            $withdraw->final_amo = $amountSend;
                            $withdraw->status = 1;
                            $withdraw->email_code = verification_code(6);
                            $withdraw->trx = $tnxnum;
                            $withdraw->save();

                            $trx = new Trx();
                            $trx->user_id     = $user->id;
                            $trx->amount      = $wdamo;
                            $trx->balance     = (0);
                            $trx->charge      = $charge;
                            $trx->main_amo    = $amountSend;
                            $trx->main_amo    = $enviado;
                            $trx->type        = 'withdraw';
                            $trx->trx         = $tnxnum;
                            $trx->hash        = $hash;
                            $trx->moneda      = 4;
                            $trx->price_dolar = @$pa['precio_dolar'];
                            $trx->title = 'withdraw Via Smart Contract TRX';
                            $trx->save();
                
                            $titulo =   $token. ' gdetoken de participaci贸n precio '.number_format($general->precio_gdetoken,2,',','');
                            transaccion($us, ($request->amount * -1), $titulo, 'parti_rete' );
                            $us->gdetoken += $token;
                            $us->save();
                            $title = $us->username.' participation of '.$pay20.' usd ';
                            pay_unilevel($us->ref_id,$pay20, $us->username);
           }
        
      }
      
        public function send_register_usdt($pago){
           $method = WithdrawMethod::where('id',3)->first();
       
           $pag = pago::where('id',$pago)->first();
           $pag->status = 3;
           $pag->save();
           
           $pay = Prew_withdraw::where('pago_id',$pago)->get();   
           $hash = (string)$pag->id_tx;

           foreach($pay as $pa){
           
                            $wdamo            = $pa['solicitud_eth'];
                            $charge           = $pa['carga_eth'];
                            $amountSend       = $pa['enviado_eth'];
                            $wdamo_con        = $pa['solicitud_trx'];
                            $charge_con       = $pa['carga_trx'];
                            $amountSend_con   = $pa['enviado_trx'];
                            $interes_wallet   = $pa['balance'];
                            $wdamo_trx        = $pa['balance'];
                            $wdamo_eth        = $pa['balance_trx'];
                            $tnxnum           = $pa['trx'];
                            $user = User::findOrFail($pa['user_id']);
                     
                            
                            $tnxnum =  getTrx();
                            $withdraw = new Withdrawal();
                            $withdraw->method_id = $method->id;
                            $withdraw->user_id = $user->id;
                            $withdraw->amount = formatter_money($wdamo);
                            $withdraw->charge = formatter_money($charge);
                            $withdraw->rate = 1;
                            $withdraw->currency = 'USDT';
                            $withdraw->delay = 0;
                            $withdraw->final_amo = $amountSend;
                            $withdraw->status = 1;
                            $withdraw->email_code = verification_code(6);
                            $withdraw->trx = $tnxnum;
                            $withdraw->save();

                            $trx = new Trx();
                            $trx->user_id = $user->id;
                            $trx->amount = $wdamo;
                            $trx->balance = formatter_money(0);
                            $trx->charge_con = formatter_money($charge);
                            $trx->main_amo_con = formatter_money($amountSend);
                            $trx->amount_con = $wdamo;
                            $trx->type = 'withdraw';
                            $trx->trx = $tnxnum;
                            $trx->hash =  $hash;
                            $trx->moneda = 3;
                            $trx->price_dolar = @$pa['precio_dolar'];
                            $trx->title = 'withdraw via Theter';
                            $trx->save();
                
                      $general = GeneralSetting::first();
                        send_tele($user, 'WITHDRAW_TRON_APPROVE', [
                            'trx' => $hash,
                            'amount' =>  formatter_money($trx->amount)." USDT)",
                            'receive_amount' =>  formatter_money($withdraw->final_amo)." USDT )",
                            'charge' =>  formatter_money($withdraw->charge)." USDT)",
                            'method' => $withdraw->method->name,
                        ]);

                        send_sms($user, 'WITHDRAW_TRON_APPROVE', [
                            'trx' => $hash,
                            'amount' => $general->cur_sym . formatter_money($withdraw->amount),
                            'receive_amount' => $general->cur_sym . formatter_money($withdraw->amount - $withdraw->charge),
                            'charge' => $general->cur_sym . formatter_money($withdraw->charge),
                            'method' => $withdraw->method->name,
                        ]);
                      
             
           }
        
      }
      
      
      

      function contar($obj){
        $i=0;
        foreach($obj as $value){
            $i++;
        }
        return $i;
    }
}