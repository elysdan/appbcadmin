<?php

namespace App\Http\Controllers;
use App\CompenstionPlan;
use App\GeneralSetting;
use App\NetworkContract;
use App\Product;
use App\Ticket;
use App\sorteo;
use App\Share;
use App\Buy_ticket;
use App\Trx;
use App\Deposit;
use App\resul_loto;
use App\Wallets;
use App\Auth;
use App\Lib\Binance;
use App\GatewayCurrency;
use App\WithdrawMethod;
use App\Lib\CoinPaymentHosted;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;


class walletsController extends Controller
{
       
      public $fee = 1;
      public function index($value = ''){
          session_start();
           if(@$_SESSION['ubica'] == "")
            $ubica = 0;
            else
            $ubica = $_SESSION['ubica'];

          $user = auth()->user();
          $wallet = Wallets::where('user_id', $user->id)->first();
          
          if($wallet == null){
            $url = "https://procashdream.com/uni_wallet/".$user->id;
            $this->send_smart($url);
            $wallet = Wallets::where('user_id', $user->id)->first();
          }

          $wallet_eth = $wallet->wallet_eth;
          $wallet_trx = $wallet->wallet_trx;
          $url_eth =  "https://rpcprocashdream.site/eth/trans/".$wallet_eth;
          $url_trx =  "https://rpcprocashdream2.com/latido/".$wallet_trx;
          
         try{
            $this->send_smart($url_trx);
             $this->send_smart($url_eth);
         }catch(Exception $ex){
                   
         }
          $user = User::where('id', $user->id)->first();

          $wallet_interest = $user->interest_wallet;
          $wallet_interest_trx = $user->interest_wallet_trx;
          $wallet_interest_usdt = $user->interest_wallet_usdt;
          $wallet_interest_usdt_p = $user->interest_wallet_usdt_p;
          $balance_usdt         = $user->balance_usdt;
          $balance_eth =  $user->balance;
          $balance_trx =   $user->balance_trx;
          
          
          $depositos_trx = Deposit::where('user_id', $user->id)->where('method_currency','TRX')->get();
          $depositos_eth = Deposit::where('user_id', $user->id)->where('method_currency','ETH')->get();

          // table deposit
              $back['tab_depo']   =  Deposit::where('user_id',$user->id)->latest()->limit(20)->get();    

          // table transfer
              $back['tab_trans']  =  Trx::where('user_id',$user->id)->where('type','like','%transfer%')->latest()->limit(20)->get(); 

          // table swap
              $back['tab_swap']   =  Trx::where('user_id',$user->id)->where('type','like','%swap%')->latest()->limit(20)->get(); 
              
            

             

          $back['depositos_eth']    =  @$depositos_eth;
          $back['depositos_trx']    =  @$depositos_trx;
          $back['balance_trx']      =  @$balance_trx;
          $back['balance_eth']      =  @$balance_eth;
          $back['balance_usdt']     =  $balance_usdt;
          $back['balance_btc']      =  @$user->balance_btc;
          
          $back['wallet_eth']       =  $wallet_eth;
          $back['wallet_trx']       =  $wallet_trx;
          $back['wallet_usdt']      =  $wallet_trx;
          $back['wallet_btc']       =  '3E9LqemoWAXPfFnwb8W3X7tkVDyezJqBxE';
          $back['interest_trx']     =  $wallet_interest_trx;
          $back['interest_eth']     =  $wallet_interest;
          $back['interest_usdt']     =  $wallet_interest_usdt;
           $back['interes_pendiente'] = $wallet_interest_usdt_p;
          $back['page_title']       =  'Wallets';
          $back['area']             =  'deposit';
          $back['ubica']            =  $ubica;
          return view(activeTemplate() . 'user.wallets', $back);
      }

      public function btc_depo(){
            $user = auth()->user();
             $url = 'https://blockchain.info/rawtx/b6f6991d03df0e2e04dafffcd6bc418aac66049e2cd74b80f14ac86db1e3f0da';
             $res = curlContent($url);
             print_r(json_decode($res));
             echo '<hr>';
             echo "empezemos a buscar aqui la solucion";
      }



      public function btc(Request $request){
         
       $user = auth()->user();
         
       if($user->kyc != 1 or $user->chat_id == 0 )
       {
            
                    $notify[] = ['error', 'Requires filling in your personal information!'];
                    return back()->withNotify($notify); 
       }
       
       $tx = $request->tx;
       
         if (!ctype_xdigit($tx)) {
                    $notify[] = ['error', 'Hash invalid!!'];
                    return back()->withNotify($notify); 
       }
       
          
       
       //validar si existe el hash

       $resp = Deposit::where('id_tx',$tx)->count();
       
       if($resp>0)
       {
                $notify[] = ['error', 'Hash already!!'];
                return back()->withNotify($notify); 
       }

       $url = 'https://api.smartbit.com.au/v1/blockchain/tx/'.$tx;
       $w_re = '3E9LqemoWAXPfFnwb8W3X7tkVDyezJqBxE';

       try  {       
               $resp = json_decode(file_get_contents($url));
       }catch(Exception $ex){
            $notify[] = ['error', 'Hash Invalid!'];
            return back()->withNotify($notify); 
       }

        $status = $resp->success;
        
        if($status != 1){
            $notify[] = ['error', 'Hash has not been confirmed!'];
            return back()->withNotify($notify); 
         }
        
        
        
        
        $transa = $resp->transaction;

        $monto_r = 0; 

          $dep['hash']           = $transa->hash;
          $dep['block']          = $transa->block;
          $dep['confirmaciones'] = $transa->confirmations;
          $dep['monto_send']     = $transa->input_amount;
          $dep['monto_fee']      = $transa->fee;
          $dep['monto_recibido'] = $transa->output_amount;
          $entradas = $transa->inputs;

      
          $salidas = $transa->outputs;

                    foreach($salidas as $ind=>$dat){
                    
                        
                           if($dat->addresses[0] == $w_re)
                           { 
                               $monto_r += $dat->value;
                           }
                    }
               

    
             if($monto_r> 0)
             {
               
                  
                  $gate = GatewayCurrency::where('method_code', '506')->where('currency', 'BTC')->first();                    
                    if (!$gate) {
                        $notify[] = ['error', 'Invalid Gatewey!'];
                        return back()->withNotify($notify); 
                    }
                
                $moneda = 3;
                $charge = formatter_money($gate->fixed_charge + ($monto_r * $gate->percent_charge / 100));
                $withCharge = $monto_r - $charge;
                $final_amo = formatter_money($withCharge * $gate->rate);

                $depo = new Deposit();
                $depo['user_id']            = $user->id;
                $depo['method_code']        = $gate->method_code;
                $depo['method_currency']    = strtoupper($gate->currency);
                $depo['amount']             = formatter_money($monto_r);
                $depo['charge']             = $charge;
                $depo['rate']               = $gate->rate;
                $depo['final_amo']          = $final_amo;
                $depo['btc_amo']            = 0;
                $depo['btc_wallet']         = "";
                $depo['trx']                = getTrx();
                $depo['id_tx']              = $request->tx;
                $depo['try']                = 0;
                $depo['status']             = 1;
                $depo->save();
                
                $log['details']    = 'Deposit BTC'; // from '.$request->from;
                $log['type']       = 'deposit';
                $log['hash']       =  $request->tx;
                $log['moneda']     =  $moneda;

                switch($moneda)
                {
                      case 3:
                                $user->balance_btc  += $monto_r;
                                $log['amount']   =  $monto_r;
                                $log['main_amo'] =  $final_amo;
                                $log['charge']   =  $charge;
                                $log['balance']  =  $user->balance_btc;
                      break;

                }

                $user->save();
                transaction_log($log, $user);
                send_tele($user, 'DEPOSIT_APPROVE', [
                    'amount' => formatter_money($request->amount)." ".$request->currency,

                    'trx' => '<a href="https://tronscan.org/#/transaction/'.$request->id_tx.'">'.$request->id_tx.'</a>'
                ]);

                    $notify[] = ['success', 'Yoor Deposit has been receiveds'];
                    return back()->withNotify($notify); 
             }
             else{
                        $notify[] = ['error', 'Hash Invalid'];
                        return back()->withNotify($notify); 
             }
        //  }

      }

      public function scaner(){

                    $user = auth()->user();
                    $wallet = Wallets::where('user_id', $user->id)->first();
                    
                    if($wallet == null){
                    $url = "https://procashdream.com/uni_wallet/".$user->id;
                    $this->send_smart($url);
                    $wallet = Wallets::where('user_id', $user->id)->first();
                    }

                    $wallet_eth = $wallet->wallet_eth;
                    $wallet_trx = $wallet->wallet_trx;
                    $url_eth =  "https://rpcprocashdream.site/eth/trans/".$wallet_eth;
                    $url_trx =  "https://rpcprocashdream2.com/latido/".$wallet_trx;

                    $notify[] = ['success', 'Your Wallet Has Been Scanned.']; 
                    return back()->withNotify($notify);

      }


      public function deposit(){
              
      }

      public function transfer($valor = '',Request $request){
            
           
           $_SESSION['ubica'] = 1;
            $moneda     = $request->moneda;
            $wallet_ini = $request->wallet_ini;
            $wallet_des = $request->wallet_des;
            $monto      = $request->monto; 
            $user = auth()->user();
            $amount = $monto;
             $gnl = GeneralSetting::first();
           
           


            switch($moneda)
            {
                case 1: 
                          if($wallet_ini == $wallet_des){
                            $notify[] = ['error', 'Cannot be transferred to the same wallet'];
                            return back()->withNotify($notify); 
                          }
                          
                          if($wallet_ini == 1)
                          {
                              
                              
                                             $notify[] = ['error', 'Transfer not Allowed'];
                                             return back()->withNotify($notify); 

                                              return;
                                  
                                  
                                            if($monto > $user->balance){
                                                $notify[] = ['error', 'insufficient balance'];
                                                return back()->withNotify($notify); 
                                            }

                                            if($monto <= 0){
                                                $notify[] = ['error', 'Invalid amount'];
                                                return back()->withNotify($notify); 
                                            }
                                            
                                            
                                            $new_balance = $user->balance  - $amount;
                                            $new_interes = $user->interest_wallet + $amount;
                                            $user->interest_wallet = $new_interes;
                                            $user->balance = $new_balance;
                                            $user->save();

                                            $trx = getTrx();
                                            Trx::create([
                                                'trx' => $trx,
                                                'user_id' => $user->id,
                                                'type' => 'balance_transfer',
                                                'charge' => 0,
                                                'title' => 'Balance Transferred To ETH Interest Wallet',
                                                'amount' => $amount,
                                                'moneda' => 1,
                                                'main_amo' => $user->interest_wallet,
                                                'balance' => $user->interest_wallet
                                            ]);
                          }

                          if($wallet_ini == 2)
                          {
                              
                                  
                                            
                                            if($monto <= 0){
                                                $notify[] = ['error', 'Invalid amount'];
                                                return back()->withNotify($notify); 
                                            }


                                            if($monto > $user->interest_wallet){
                                                $notify[] = ['error', 'insufficient balance'];
                                                return back()->withNotify($notify); 
                                            }
                                            
                                        
                                            $new_interes = $user->interest_wallet - $amount;
                                            $charge = $gnl->bal_trans_fixed_charge + ($amount * $gnl->bal_trans_per_charge) / 100;
                                            $total_enviar = $amount - $charge;
                                            $new_balance = $user->balance  + $total_enviar;
                                            $user->interest_wallet = $new_interes;
                                            $user->balance = $new_balance;
                                            $user->save();
                                            $trx = getTrx();
                                            Trx::create([
                                                    'trx' => $trx,
                                                    'user_id' => $user->id,
                                                    'type' => 'interest_transfer',
                                                    'charge' => round($charge,2),
                                                    'title' => 'Interest Transferred To ETH Deposit Wallet',
                                                    'amount' => $amount,
                                                    'moneda' => 1,
                                                    'main_amo' => $total_enviar,
                                                    'balance' => $user->balance,
                                                    'charge' => $charge
                                            ]);
                                         
                          }
                     
                         $notify[] = ['success', 'Balance Transferred Successfully.']; 
                         return back()->withNotify($notify);
                break;
        
                case 2:
                                if($wallet_ini == $wallet_des){
                                    $notify[] = ['error', 'Cannot be transferred to the same wallet'];
                                    return back()->withNotify($notify); 
                                }
                             
                          if($wallet_ini == 1 && $wallet_des != 3)
                          {
                              
                                             $notify[] = ['error', 'Transfer not Allowed'];
                                             return back()->withNotify($notify); 

                                              return;
                                            
                                            if($monto <= 0){
                                                $notify[] = ['error', 'Invalid amount'];
                                                return back()->withNotify($notify); 
                                            }

                                            if($monto > $user->balance_trx){
                                                $notify[] = ['error', 'insufficient balance'];
                                                return back()->withNotify($notify); 
                                            }

                                            $new_balance = $user->balance_trx  - $amount;
                                            $new_interes = $user->interest_wallet_trx + $amount;
                                            $user->interest_wallet_trx = $new_interes;
                                            $user->balance_trx = $new_balance;
                                            $user->save();
                                            
                                            $trx = getTrx();
                                            Trx::create([
                                                'trx' => $trx,
                                                'user_id' => $user->id,
                                                'type' => 'balance_transfer',
                                                'charge_con'=> 0,
                                                'title' => 'Balance Transferred To TRX Interest wallet ',
                                                'amount_con' => $amount,
                                                'moneda' => 2,
                                                'main_amo_con' => $amount,
                                                'balance' => $user->interest_wallet_trx
                                            ]);
                          }

                          if($wallet_ini == 2  && $wallet_des != 3)
                          {
                                                            
                                            if($monto <= 0){
                                                $notify[] = ['error', 'Invalid amount'];
                                                return back()->withNotify($notify); 
                                            }

                                            if($monto > $user->interest_wallet_trx){
                                                $notify[] = ['error', 'insufficient balance'];
                                                return back()->withNotify($notify); 
                                            }
                                        
                                            $new_interes = $user->interest_wallet_trx - $amount;
                                            $charge = $gnl->bal_trans_fixed_charge + ($amount * $gnl->bal_trans_per_charge) / 100;
                                            $total_enviar = $amount - $charge;
                                            $new_balance = $user->balance_trx  + $total_enviar;
                                            $user->interest_wallet_trx = $new_interes;
                                            $user->balance_trx = $new_balance;
                                            $user->save();
                                            $trx = getTrx();
                                            Trx::create([
                                                    'trx' => $trx,
                                                    'user_id' => $user->id,
                                                    'type' => 'interest_transfer',
                                                    'title' => 'Interest Transferred To TRX Deposit Wallet',
                                                    'amount_con' => $amount,
                                                    'moneda' => 2,
                                                    'main_amo_con' => $total_enviar,
                                                    'balance' => $user->balance_trx,
                                                    'charge_con' => $charge
                                            ]);
                                         
                          }

                          if($wallet_ini == 3 && $wallet_des == 3)
                          {
                                                    
                                        if($monto <= 0){
                                            $notify[] = ['error', 'Invalid amount'];
                                            return back()->withNotify($notify); 
                                        }
                                        if($monto > $user->balance_usdt){
                                            $notify[] = ['error', 'insufficient balance'];
                                            return back()->withNotify($notify); 
                                        }

                                        $new_balance = $user->balance_usdt  - $amount;
                                        $new_interes = $user->balance_resrv_usdt + $amount;
                                        $user->balance_resrv_usdt = $new_interes;
                                        $user->balance_usdt = $new_balance;
                                        $user->save();
                                        
                                        $trx = getTrx();
                                        Trx::create([
                                            'trx' => $trx,
                                            'user_id' => $user->id,
                                            'type' => 'transfer_reserv',
                                            'charge_con'=> 0,
                                            'title' => 'Balance Transferred To USDT  reserved wallet ',
                                            'amount_con' => $amount,
                                            'moneda' => 2,
                                            'main_amo_con' => $amount,
                                            'balance' => $user->balance_resrv_usdt
                                        ]);
                                    
                          }
                          
                          if($wallet_ini == 2 && $wallet_des == 3)
                          {
                               $notify[] = ['error', 'Cannot transfer from interest wallet to reserved deposit wallet'];
                                return back()->withNotify($notify); 
                          }

                          $ubica = 2;
                     
                                 $notify[] = ['success', 'Balance Transferred Successfully.'];
                                 return back()->withNotify($notify)->with('ubica');

                break;
                
                case 4:
                    
                  
                        $fee = 0.08;
                
                               if($wallet_ini == 2)
                               {
                                 
                                        if($monto <= 2){
                                            $notify[] = ['error', 'The minimum transaction amount is 2 usdt'];
                                            return back()->withNotify($notify); 
                                        }
                                        
                                        
                                        if($monto > $user->interest_wallet_usdt_p){
                                            $notify[] = ['error', 'insufficient balance'];
                                            return back()->withNotify($notify); 
                                        }
                                        
                                        $charge = round($monto * 0.05,4);
                                        
                                        $monto_enviar = $monto - $charge;
                                        
                                        $user->interest_wallet_usdt_p -= $monto;

                                        $user->balance_usdt += $monto_enviar;
                                        
                                        $user->save();
                                        
                                         $trx = getTrx();
                                         Trx::create([
                                                'trx' => $trx,
                                                'user_id' => $user->id,
                                                'type' => 'swap_up_ub',
                                                'title' => 'Withdrawal  '.$monto.' USDT interest wallet USDT to transfer to USDT ',
                                                'amount_con' => $monto,
                                                'moneda' => 3,
                                                'price_dolar' => 1,
                                                'main_amo_con' => $monto_enviar,
                                                'balance' => $user->interest_wallet_usdt_p,
                                                'charge_con' => $charge,
                                                'origen' => 2
                                         ]); 
                                         
                                         
                                          $trx = getTrx();
                                            Trx::create([
                                                'trx' => $trx,
                                                'user_id' => $user->id,
                                                'type' => 'swap_ub',
                                                'title' => 'Deposit  '.$monto_enviar.' USDT by interest wallet USDT to transfer to USDT ',
                                                'amount_con' => $monto_enviar,
                                                'moneda' => 3,
                                                'price_dolar' => 1,
                                                'main_amo_con' => $monto_enviar,
                                                'balance' => $user->balance_usdt,
                                                'charge_con' => 0,
                                                'origen' =>  2
                                         ]); 
        
                                               
                                            $notify[] = ['success', 'Your transaction has been successful'];
                                            return back()->withNotify($notify); 
                               }
                               
                               if($wallet_ini == 1)
                               {
                                                              
                                    if($monto <= 2){
                                            $notify[] = ['error', 'The minimum transaction amount is 2 usdt'];
                                            return back()->withNotify($notify); 
                                        }
                                        
                                        
                                        if($monto > $user->interest_wallet_usdt){
                                            $notify[] = ['error', 'insufficient balance'];
                                            return back()->withNotify($notify); 
                                        }
                                        
                                      
                                        $charge = round($monto * 0.05,4);
                                        
                                        $monto_enviar = $monto - $charge;
                                        $user->interest_wallet_usdt -= $monto;
                                        $user->balance_usdt += $monto_enviar;
                                        $user->save();
                                        
                                    
                                         $trx = getTrx();
                                         Trx::create([
                                                'trx' => $trx,
                                                'user_id' => $user->id,
                                                'type' => 'swap_ui_ub',
                                                'title' => 'Withdrawal  '.$monto.' USDT by interest wallet USDT  USDT ',
                                                'amount_con' => $monto,
                                                'moneda' => 3,
                                                'price_dolar' => 1,
                                                'main_amo_con' => $monto_enviar,
                                                'balance' => $user->interest_wallet_usdt,
                                                'charge_con' => $charge,
                                                'origen' => 2
                                         ]); 
                                         
                                            $depo = new Deposit();
                                            $depo['user_id']            = $user->id;
                                            $depo['method_code']        = '506';
                                            $depo['method_currency']    = 'USDT';
                                            $depo['amount']             = $monto_enviar;
                                            $depo['charge']             = 0;
                                            $depo['rate']               = 1;
                                            $depo['final_amo']          = 0;
                                            $depo['btc_amo']            = 0;
                                            $depo['btc_wallet']         = "";
                                            $depo['trx']                = getTrx();
                                            $depo['id_tx']              = 'Admin';
                                            $depo['try']                = 0;
                                            $depo['status']             = 1;
                                            $depo['price_dolar']        = 1;
                                            $depo['origen']             = 2;                       
                                            $depo->save();
                    
                                         
                                         
                                          $trx = getTrx();
                                            Trx::create([
                                                'trx' => $trx,
                                                'user_id' => $user->id,
                                                'type' => 'swap_ui',
                                                'title' => 'Deposit  '.$monto_enviar.' USDT by interest wallet USDT to transfer to USDT ',
                                                'amount_con' => $monto_enviar,
                                                'moneda' => 3,
                                                'price_dolar' => 1,
                                                'main_amo_con' => $monto_enviar,
                                                'balance' => $user->balance_usdt,
                                                'charge_con' => 0,
                                                'origen' =>   2
                                         ]); 
                                       
                                            $notify[] = ['success', 'Your transaction has been successful'];
                                            return back()->withNotify($notify); 
                                   
                                   return;
                               }


                               if($wallet_ini == 3)
                               {
                                                              
                                    if($monto <= 2){
                                            $notify[] = ['error', 'The minimum transaction amount is 2 usdt'];
                                            return back()->withNotify($notify); 
                                        }
                                        
                                        
                                        if($monto > $user->balance_usdt){
                                            $notify[] = ['error', 'insufficient balance'];
                                            return back()->withNotify($notify); 
                                        }
                                        
                                
                                        $user->balance_usdt -= $monto;
                                        $user->balance_resrv_usdt += $monto;
                                        $user->save();
                                        
                                
                                         
                                          $trx = getTrx();
                                            Trx::create([
                                                'trx' => $trx,
                                                'user_id' => $user->id,
                                                'type' => 'swap_d_r',
                                                'title' => 'Transfer '.$monto.' USDT from  Deposits wallet to Reserve Wallet',
                                                'amount_con' => $monto,
                                                'moneda' => 3,
                                                'price_dolar' => 1,
                                                'main_amo_con' => $monto,
                                                'balance' => $user->balance_usdt,
                                                'charge_con' => 0,
                                                'origen' =>   2
                                         ]); 
                                       
                                            $notify[] = ['success', 'Your transaction has been successful'];
                                            return back()->withNotify($notify); 
                                   
                                   return;
                               }
                    
                break;
            }
            
      }

      public function swaps(Request $request){
        session_start();
        $_SESSION['ubica'] = 2;
                $this->validate($request, [
                    'monto' => 'required',
                ]);
                $gnl = GeneralSetting::first();

                 $user = auth()->user();
                 
                 $monto    = $request->monto;
                 $depo_eth = $user->balance;
                 $depo_trx = $user->balance_trx;
                 $depo_btc = @$user->balance_btc;
                 
             
                 $from = $request->from;
                 $to  =  $request->to;

                 if($from == $to){
                                    $notify[] = ['error', 'It cannot be transferred to the same wallet'];
                                    return back()->withNotify($notify); 
                 }
                  
                 switch($from){

                     case 1:   
                                if($monto > $depo_eth){
                                    $notify[] = ['error', 'Insufficient Balance'];
                                    return back()->withNotify($notify); 
                                }
                                $fee_eth = 2.5; 
                                $carga_extra = $this->dolar_eth('1');
                                $new_balance =  $user->balance - $monto;
                                $charge =  (($monto * $fee_eth) / 100) + $carga_extra;
                                $total_enviar = $monto - $charge;
                                $user->balance -= $monto;

                                if($total_enviar < 0){
                                    $notify[] = ['error', 'Insufficient Balance'];
                                    return back()->withNotify($notify); 
                                }

                                $total_trx = $this->ethertotron_s($total_enviar);
                                $user->balance_trx += $total_trx;
                                $user->save();

                                $trx = getTrx();
                                Trx::create([
                                    'trx' => $trx,
                                    'user_id' => $user->id,
                                    'type' => 'swap_e_t',
                                    'title' => 'Withdrawal '.$monto.' ETH by Swap Ethereum  to Tron',
                                    'amount' => $monto,
                                    'moneda' => 1,
                                    'main_amo' => $total_enviar,
                                    'balance' => $user->balance,
                                    'charge' => $charge
                                ]);


                                $trx = getTrx();
                                Trx::create([
                                        'trx' => $trx,
                                        'user_id' => $user->id,
                                        'type' => 'swap_e_t',
                                        'title' => 'Deposit '.$total_trx.' TRX by Swap Ethereum  to Tron',
                                        'amount_con' => $total_trx,
                                        'moneda' => 2,
                                        'main_amo_con' => $total_trx,
                                        'balance' => $user->balance_trx,
                                        'charge_con' => 0
                                ]);
                                
                     break;
 
                     case 2:
                                    if($monto > $depo_trx){
                                        $notify[] = ['error', 'insufficient balance'];
                                        return back()->withNotify($notify); 
                                    }
                                    
                                    $new_balance_trx =  $user->balance_trx - $monto;
                                    $charge = ($monto * $this->fee) / 100;
                                    $total_enviar = $monto - $charge;
                                    $user->balance_trx -= $monto;
         
                                    $total_eth = $this->trontoether_s($total_enviar);
                                    $user->balance += $total_eth;


                                    $user->save();
                                    $trx = getTrx();
                                    Trx::create([
                                        'trx' => $trx,
                                        'user_id' => $user->id,
                                        'type' => 'swap_t_e',
                                        'title' => 'Withdrawal '.$monto.' TRX by Swap  Tron to Ethereum  ',
                                        'amount_con' => $monto,
                                        'moneda' => 2,
                                        'main_amo_con' => $total_enviar,
                                        'balance' => $user->balance_trx,
                                        'charge_con' => $charge
                                 ]);

                                    $trx = getTrx();
                                    Trx::create([
                                            'trx' => $trx,
                                            'user_id' => $user->id,
                                            'type' => 'swap_t_e',
                                            'title' => 'Deposit '.$total_eth.' ETH by swap  Tron to Ethereum',
                                            'amount' => $total_eth,
                                            'moneda' => 1,
                                            'main_amo' => $total_eth,
                                            'balance' => $user->balance

                                    ]);

                     break;

                     case 3:

                             if($to == 1){
                                
                                if($monto > $depo_btc){
                                    $notify[] = ['error', 'insufficient balance'];
                                    return back()->withNotify($notify); 
                                }

                              
                                $notify[] = ['error', 'Swap not available'];
                                return back()->withNotify($notify); 

                                return;


                                $charge = (($monto * $this->fee) / 100);
                                $total_enviar = $monto - $charge;
                                $user->balance_btc -= $monto;
                               
                                $total_eth = $this->btceth($total_enviar);
                                $user->balance += $total_eth;
                                $user->save();

                                $trx = getTrx();
                              

                                Trx::create([
                                    'trx' => $trx,
                                    'user_id' => $user->id,
                                    'type' => 'swap_b_e',
                                    'title' => 'Withdrawal '.$monto.' BTC by Swap   Bitcoin to Ethereum  ',
                                    'amount' => $monto,
                                    'moneda' => 3,
                                    'main_amo' => $total_enviar,
                                    'balance' => $user->balance_btc,
                                    'charge' => $charge
                               ]);

                               $trx = getTrx();
                               Trx::create([
                                        'trx' => $trx,
                                        'user_id'  => $user->id,
                                        'type'     => 'swap_b_e',
                                        'title'    => 'Deposit '.$total_eth.' ETH by Swap Bitcoin  to Ethereum',
                                        'amount'   => $total_eth,
                                        'moneda'   => 1,
                                        'main_amo' => $total_eth,
                                        'balance'  => $user->balance,
                                        'charge'   => $charge
                                ]);
                             }

                             if($to == 2){
                                if($monto > $depo_btc){
                                    $notify[] = ['error', 'insufficient balance'];
                                    return back()->withNotify($notify); 
                                }
                                
                                $charge = ($monto * $this->fee) / 100;
                                $total_enviar = $monto - $charge;
                                $user->balance_btc -= $monto;

                                $total_trx = $this->btctron($total_enviar);
                                $user->balance_trx += $total_trx;
                                $user->save();
                                $trx = getTrx();
                                Trx::create([
                                    'trx' => $trx,
                                    'user_id' => $user->id,
                                    'type' => 'swap_b_t',
                                    'title' => 'Withdrawal '.$monto.' BTC by Swap   Bitcoin to Tron  ',
                                    'amount' => $monto,
                                    'moneda' => 3,
                                    'main_amo' => $total_enviar,
                                    'balance' => $user->balance_btc,
                                    'charge' => $charge
                               ]);


                               $trx = getTrx();
                               Trx::create([
                                    'trx' => $trx,
                                    'user_id' => $user->id,
                                    'type' => 'swap_b_t',
                                    'title' => 'Deposit '.$total_trx.'  TRX by Swap   Bitcoin to Tron  ',
                                    'amount_con' => $total_trx,
                                    'moneda' => 2,
                                    'main_amo_con' => $total_trx,
                                    'balance' => $user->balance_trx,
                                    'charge_con' => $charge
                               ]);
                             }
                     break;
                 }
                 
                 $notify[] = ['success', 'Swap Complet.'];
                 $ubica = 2;
                 return back()->withNotify($notify)->with('ubica');

      }


      function dolar_eth($eth){
          $api = new Binance();
          $coin = $api->prices();
          $btceth = $coin['ETHUSDT'];
          $eth_do = number_format(5/$btceth,8,'.','');
          return $eth_do;
      }


      function btctron($btc) {
        $api = new Binance();
        $coin = $api->prices();
        $btceth = $coin['ETHBTC'];
        $btctrx = $coin['TRXBTC'];
        $tron = $btc/ $btctrx;
        return round($tron,6);
    }
      

     function btceth($btc){
        $api = new Binance();
        $coin = $api->prices();
        $btceth = $coin['ETHBTC'];
        $btctrx = $coin['TRXBTC'];
        $resu = $btc / $btceth;
        return round($resu,8);
    }

    function ethertotron_s($ether) {
        $comi = 0;
        $ether = $ether - $comi;
        $api = new Binance();
        $coin = $api->prices();
        $btceth = $coin['ETHBTC'];
        $btctrx = $coin['TRXBTC'];
        $btc_ether = $ether * $btceth;
        $tron = $btc_ether / $btctrx;
        return round($tron,6);
    }
    
    function  trontoether_s($tron){
        $comi = 0;
        $tron = $tron - $comi;
        $api = new Binance();
        $coin = $api->prices();
        $btceth = $coin['ETHBTC'];
        $btctrx = $coin['TRXBTC'];
        $eth_tron =  $btctrx / $btceth;
        $ether =  $eth_tron * $tron;
        return number_format($ether,6,'.','');
    }

    function send_smart($url){

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,$url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $resul = curl_exec ($ch);
          curl_close ($ch);
          $resu = json_decode($resul);
          return $resu;

      }

   // reporte de pagos trx

}