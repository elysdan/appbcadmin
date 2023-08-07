<?php

namespace App\Http\Controllers;
use App\CompenstionPlan;
use App\GeneralSetting;
use App\Product;
use App\Trx;
use App\User;
use App\Share;
use App\UserExtra;
use Carbon\Carbon;
use App\Deposit;
use App\Buy_ticket;
use App\Ticket;
use App\sorteo;
use App\BvLog;
use App\MatrixSubscriber;
use App\UserMatrix;
use App\WithdrawMethod;
use App\NetworkContract;
use App\Pointpaid;
use Illuminate\Http\Request;
use App\GatewayCurrency;
use App\Lib\CoinPaymentHosted;
use Illuminate\Support\Facades\Auth;


class CronController extends Controller
{
    public function cron()
    {   
        // return   $time = Carbon::now()->toDateTimeString();
        set_time_limit(0);
        $gnl = GeneralSetting::first();
        $gnl->last_cron = Carbon::now()->toDateTimeString();
        $gnl->last_paid = Carbon::now()->toDateString();
        $gnl->save();

        $mes = date('m');
        $ano = date('Y');

        $eligibleUsers = UserExtra::where('bv_left', '>=', 1)->where('bv_right', '>=', 1)->join('users', 'users.id', 'user_extras.user_id')->where('paga_binario',1)->get();
       
        foreach ($eligibleUsers as $uex) {
             $user = $uex->user;
             //certifica si todavia lo tiene los puntos
             $valida = UserExtra::where('bv_left', '>=', 1)->where('bv_right', '>=', 1)->where('user_id', $uex->user_id)->count();
             echo "<br>  *****  ".$valida." ****** <br>";
        if($valida > 0)
        {         
           //verifica si puede cobrar
           $user = $uex->user;

           $total_reinv  = Share::where('user_id', $user->id)->where('moneda','3')->whereYear('created_at',$ano)->whereMonth('created_at', $mes)->sum('amount');
           $total_direct = Trx::where('user_id', $user->id)->where('moneda',3)->where('type','referral_commision')->whereYear('created_at',$ano)->whereMonth('created_at', $mes)->sum('amount_con');
           
           $inversionv = $total_reinv + $total_direct;
           echo $total_reinv;
           

           return;
             // y corta si no lo tiene 
           
            $max_profit = check_max_profit($user->id);
            $weak = $uex->bv_left < $uex->bv_right ? $uex->bv_left : $uex->bv_right;
            
            $we_l = $uex->left_retenido;
            $we_r = $uex->right_retenido;
    
            //new calculo 
            $weak_n = $we_l < $we_r ? $we_l : $we_r;
            
            
            echo $weak." ".$weak_n."<br>";
          
      

            $current_rank = CompenstionPlan::first();
             
             /*   $uex->bv_left -= $weak;
                $uex->bv_right -= $weak;
                $uex->save();
                echo "*****".$user->id." ***** <br>"; */
                
                $user_po = UserExtra::where('user_id', $user->id)->first();
                $user_po->bv_left -= $weak;
                $user_po->bv_right -= $weak;
                $user_po->left_retenido -= $weak_n;
                $user_po->right_retenido  -= $weak_n;
                 
                $user_po->save();
              
              $user_seg = UserExtra::where('user_id', $user->id)->first();
              
              if($user_seg->bv_left == 0 || $user_seg->bv_right == 0)
              {
                

            if ($current_rank == null) {
                $current_rank = CompenstionPlan::where('max_share', '<=', 1)->orderBy('max_share', 'desc')->first();
            }
            
                
 
            
            
            
            
         /*   if($user->paga_binario == 0){
                   $user->paga_binario = 1;
                   $user->save();
            }else{ */
            
             if ($current_rank) {
                  error_reporting(E_ALL);
                 ini_set('display_errors', '1');
                     if ($current_rank->binary_bonus > 0 ) {

                        $return_profit = $weak * $current_rank->binary_bonus / 100;
                    
                        $share_price = Product::first();

                        $bonus =  (monto_binary($weak_n) * 8) /100;

                        $payment = User::find($uex->user_id);

                        $shears = \App\Share::where('user_id', $payment->id)->where('moneda',3)->where('status', '!=' , 2)->orderBy('id')->get();
                        
                        $ace = $bonus*0.95; // 95% (5% later)
                        
                        echo $bonus."  y lo que se reparte es ".$ace;
                        
                        
                       
                   
                     if (haveBothSide($user->id)){
                        $tipo_pago = 1;
                        
                              
                                    $paid = new Pointpaid();
                                    $paid->user_id = $user->id;
                                    $paid->point = $weak;
                                    $paid->fecha_pagado = Carbon::now()->toDateString();
                                    $paid->save();
                          
                          
                          residualBonus($user->id,$bonus*0.01,'Residual Bonus From '.$user->username);
                       
                                foreach ($shears as $shear) {
                                        
                                        $usua = User::find($shear->user_id);
                                        $tipo_pago = $shear->moneda;
                                        $limit = $shear->max_earning;

                                        //convert_monto;

                                        $lmt = $ace;

                                        $bal = $shear->return_profit + $ace;

                                        if ($bal >= $limit) {
                                            $lmt = round($limit - $shear->return_profit,8);
                                            $bal = $shear->return_profit + $lmt;
                                        }

                                        echo "<br> el monto a cobrar es  ".$lmt."<br>";
                                
                                        if ($lmt <= 0) {
                                            break;
                                        }
                                        if ($bal == $shear->max_earning) {
                                            $shear->status = 2;
                                        }
                                        
                                        $shear->return_profit = $bal;
                                        if($usua->id != 43)
                                        {
                                            $shear->save();
                                            cierra_paquete($shear->id);
                                        }
                                        $ace -= $lmt;
                                        echo "resta esto ".$ace.'<br>';
                                      
                                                $usua->interest_wallet_usdt += $lmt;
                                                $usua->total_return_usdt += $lmt;
                                      
                                        if($usua->id != 43)
                                        {
                                             $usua->save();
                                        }

                                    //////// CREATE TRANSACTION

                                            $mon = "USDT";
                                     

                               echo  $details = 'Paid ' . $lmt . ' ' . $mon. ' For ' . $weak . ' BV.';
                                    
                                            if($usua->id != 43)
                                            {
                                                $retu = "";
                                               $retu = trx_tran_binay($lmt,  $usua, $details, $shear->moneda);
                                            
                                            
                                                if($retu != "")
                                                {
                                                        send_tele($user, 'matching_bonus', [
                                                                'amount' => formatter_money($lmt) . ' ' . $mon,
                                                                'paid_bv' => $weak,
                                                                'balance_now' => formatter_money($payment->interest_wallet_usdt) . ' ' . $gnl->cur_text,
                                                            ]);

                                                            send_sms($user, 'matching_bonus', [
                                                                'amount' => formatter_money($lmt) . ' ' . $mon,
                                                                'paid_bv' => $weak,
                                                                'ba
                                                                lance_now' => formatter_money($payment->interest_wallet_usdt) . ' ' . $gnl->cur_text,
                                                            ]); 
                                                }
                                            }

                                            echo '<hr><hr>';
                                            
                                    }

                        }//// if eligible(bothside)
                        echo "culmino completo";
                    
                        // $uex->bv_left -= $weak;
                        // $uex->bv_right -= $weak;
                        // $uex->save();
                }
            // }
            }
           }
         }
        }//foreach
    }
    
    public function actualizar_wallet(){
          $user = User::where('forma_p',1)->get();
          foreach($user as $data){
             $smart =   valida_wallet_eth();
             $data->smart = $smart;
             $data->save();
          }
    }


    public function  update_minimo(){

       $gnl = GeneralSetting::first();
       $maximo_fee = $gnl->maximo_fee;
       $gas_user = $gnl->gas_user;

       $url = "https://api.etherscan.io/api?module=gastracker&action=gasoracle&apikey=SKWTC1WX23ZHMR8S7AFMSE8628K9RFF4MM";;
       $gas = json_decode(file_get_contents($url));
       if($gas->status == 1){ $result =  $gas->result; $precio_gas = $result->FastGasPrice; }

       $url2 = "https://api.etherscan.io/api?module=stats&action=ethprice&apikey=SKWTC1WX23ZHMR8S7AFMSE8628K9RFF4MM";
       $precio = json_decode(file_get_contents($url2));
       if($precio->status == 1) { $result =  $precio->result;  $precio_eth = $result->ethusd; }
       $comision_promedio = ($gas_user * $precio_gas)/1000000000;
       $minimo_retiro = round(((100 *  $comision_promedio) / $maximo_fee),3);
       $mod  = WithdrawMethod::first();
       $mod->min_limit = $minimo_retiro; 
       $mod->save();

         echo '<table>
                   <tr>
                       <td>Gas limit/ user</td>
                       <td>'.$gas_user.'</td>
                   </tr>
                    <tr>
                       <td>Precio Gas Fast</td>
                       <td>'.$precio_gas.'</td>
                   </tr>
                    <tr>
                       <td>Fee maximo pagar</td>
                       <td>'. $maximo_fee.'</td>
                   </tr>
                    <tr>
                       <td>Precio Eth</td>
                       <td>'.$precio_eth.'</td>
                   </tr>
                    <tr>
                       <td>Fee promedio</td>
                       <td>'.$comision_promedio.'</td>
                   </tr>
                   <tr>
                       <td>Minimo retiro</td>
                       <td>'.$minimo_retiro.'</td>
                   </tr>

              </table>';
    }


    public function checkPayments($trx){
        $deposit = \App\Deposit::where('trx', $trx)->orderBy('id', 'DESC')->first();
        $response['status'] = $deposit->status;
        $response['src_status'] = $deposit->src_status;
        return json_encode($response);
    }


    public function loginas($id){
        Auth::loginUsingId($id);
        return redirect()->route('user.home');
    }

 public function checkRank()
 {
       
         $method = WithdrawMethod::where('id',2)->first();

       /*  $cps = new CoinPaymentHosted();
        $cps->Setup($method->val2, $method->val1);
        $result = $cps->GetRates();
        $btceth = number_format($result['result']['ETH']['rate_btc'],8,'.','');
        $btctrx = number_format($result['result']['TRX']['rate_btc'],8,'.',''); */


          $users = UserExtra::where('bv_history_left','>=','2000')->where('bv_history_right','>','2000')->orderBy('id')->get();

        foreach ($users as $userito) {
                
            $user = User::where('id',$userito->user_id)->where('condicion','0')->first();
                echo $user->username;
            $left = $this->find_rank($user->id, $user->rank->id, 1);

            $right = $this->find_rank($user->id, $user->rank->id, 2);
  
     
            $low = $user->top_left < $user->top_right ? $user->top_left : $user->top_right;
         
            $sum_r =  UserExtra::where('user_id', $user->id)->sum('bv_history_right');
            $sum_l =  UserExtra::where('user_id', $user->id)->sum('bv_history_left');

          
            echo (int) $user->rank->id;

            $probaleRank = \App\Rank::where('id', '<=',  $user->rank->id+1)->orderBy('id', 'DESC')->get();
            
            print_r(json_decode($probaleRank));
            
            
            foreach ($probaleRank as $r) {  
                
                switch($r->id){
                      case 1:
                                    return;
                      break;
                      case 2:
                               if($r->id ==  $user->rank->id +1)
                               {
                                   
                                    if($sum_r >= $r->binary_earning &&  $sum_l >= $r->binary_earning )
                                        {
                                           
                                           $user->rank_id = $r->id;
                                           $user->cambio_rank = Carbon::now();
                                                
                                        }
                               }
                      break;
                      case 3:
                               echo $r->id." :: ".$user->rank_id;
                               
                               echo  ' - '.$user->username;
                               
                                if($r->id == $user->rank->id+1)
                                  {
                                        echo $sum_r. " ".$sum_l;
                                        echo '<br>'.$r->binary_earning;
                                     
                                        if($sum_r >= $r->binary_earning &&  $sum_l >= $r->binary_earning )
                                        {
                                             if($left >= 1 and $right >= 1)
                                                {
                                                    $user->rank_id = $r->id;
                                                    $user->cambio_rank = Carbon::now();
                                                }
                                        }
                                  }
                      break;
                            
                      default:
                                    if($r->id == $user->rank->id+1)
                                    {
                                            if($sum_r >= $r->binary_earning &&  $sum_l >= $r->binary_earning )
                                            {
                                                if($left >= 1 and $right >= 1)
                                                {
                                                    $user->rank_id = $r->id;
                                                    $user->cambio_rank = Carbon::now();
                                                }
                                            }
                                    }     
                }                    

              $user->save();
/*
                if ($binarySum >= $r->binary_earning) {
                     
                    if($r->binary_earning == 2){
                        $user->rank_id = $r->id;
                    }else{
                        if($sum_r >= $r->binary_earning &&  $sum_l >= $r->binary_earning )
                        {
                             
                                $user->last_cron = Carbon::now();
                                if($user->rank_id < $r->id) {
                                    $user->rank_id = $r->id;
                                    updateTopRank($user->id,$r->id);
                                    echo '<br><br><br>##################'.$user->id.' ==> '.$r->id.'##################-<br><br><br>';
                                }
                            }
                    } */
                    break;
                //}
            }

            echo "<hr>";

        }
        echo 'LOOP DONE';
    }


    function  trontoether_s($tron, $btceth, $btctrx){
        $comi = 0;
        $tron = $tron - $comi;
     
        $eth_tron =  $btctrx / $btceth;
        $ether =  $eth_tron * $tron;
        return number_format($ether,6,'.','');
    }


    public function gas_station(){
          
          $url = "https://api.etherscan.io/api?module=gastracker&action=gasoracle&apikey=SKWTC1WX23ZHMR8S7AFMSE8628K9RFF4MM";;
          $ch = curl_init();
    
          curl_setopt($ch, CURLOPT_URL,$url);
          curl_setopt($ch, CURLOPT_POST, TRUE);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          echo $resul = curl_exec ($ch);
          curl_close ($ch);
          return $resul;
    }


    public function complet_pay(){
        $shares = Share::where('status',0)->where('act',0)->get();
        if($shares)
        {
             $product = product::where('moneda',2)->first();
             $vip     = networkcontract::where('moneda',2)->first();
             foreach($shares as $paquete)
             {  
                    $user = User::where('id',$paquete->user_id)->first();
                  
                    $res =   file_get_contents("https://api.trongrid.io/v1/transactions/".$paquete->id_tx."/events");
                    $res =json_decode($res);
                    if(@$res->data[0])
                    {
                         $total = $this->contar($res->data[0]);
                        if($total == 9)
                        {
                            $data = $res->data[0];   
                            $contr_user = $data->result->user;
                            $contr_status = $res->success;
                            $monto = $data->result->monto/1000000;
                                        if( $contr_status == "1" )
                                        {
                                            if($user->username == $contr_user)
                                            {
                                                if($monto == $paquete->amount)
                                                {
                                                  switch($paquete->type)
                                                  {
                                                      case 0 : $this->activa_paquete_vip($user, $paquete, $vip);  break;
                                                      case 1 : $this->activa_paquete($user, $paquete, $product); break;
                                                      case 2 : $this->activa_paquete_super($user, $paquete, $vip); break;
                                                  }
                                                }
                                            } 
                                        }
                        }else{
                            print_r($res);
                            echo "<br>";
                            echo   "estamos aqui :: ".$total;
                        }
                    }else{
                        /*   $paquete->status = 5;
                           $paquete->save();*/
                    }
             }
        }
        echo "Completed Rutina";
    }

    public function  complet_lotto(){
        $ticket = Buy_ticket::where('status',0)->get();
        if($ticket)
        {
             $sorteo = sorteo::where('status',0)->first();
             foreach($ticket as $paquete)
             {  
                    $user = User::where('id',$paquete->user_id)->first();
                    "https://api.trongrid.io/v1/transactions/".$paquete->id_tx."/events";
                    $res =   file_get_contents("https://api.trongrid.io/v1/transactions/".$paquete->id_tx."/events");
                    $res =json_decode($res);
                    if(@$res->data[0])
                    {
                         $total = $this->contar($res->data[0]);
                        if($total == 9)
                        {
                            $data = $res->data[0];   
                            $contr_user = $data->result->user;
                            $contr_status = $res->success;
                            $monto = $data->result->monto/1000000;
                                        if( $contr_status == "1" )
                                        {
                                            if($user->username == $contr_user)
                                            {
                                                 $monto_p = ($sorteo->price * $paquete->cantidad);

                                                if($monto == $monto_p){ 
                                                    $this->activar_ticket($user, $paquete, $sorteo); 
                                                }
                                            } 
                                        }
                        }else{
                            print_r($res);
                            echo "<br>";
                            echo   "estamos aqui :: ".$total;
                        }
                    }else{
                        /*   $paquete->status = 5;
                           $paquete->save();*/
                    }
             }
        }
      // echo "Completed Rutina";
    }

    function activar_ticket($user,$buy, $sorteo){
    
        $buy->status = 1;
        $buy->save(); 
        $sorteo->participantes += $buy->cantidad;
        $sorteo->save(); 
    
      $user->transactions()->create([
            'trx' => getTrx(),
            'user_id' => $user->id,
            'amount' => trontoether($buy->amount),
            'main_amo' => trontoether($buy->amount),
            'amount_con' => $buy->amount,
            'main_amo_con' => $buy->amount,
            'balance' => $user->balance,
            'title' =>  $buy->cantidad.' lotto Tickets Purchase ',
            'charge' => 0,
            'moneda' => $sorteo->moneda,
            'type' => 'ticket_buy'
        ]); 
       
         for($i = 0; $i<$buy->cantidad; $i++)
         {
                $tick = new Ticket();
                $tick->user_id =  $user->id;
                $tick->price =  $sorteo->price;
                $tick->sorteo = $sorteo->id;
                $tick->status = 0;
                $tick->id_tx = $buy->id_tx;
                $tick->save(); 
         }
      $details = $user->username . ' Buy ' . $buy->cantidad . ' lotto ticket.';
        $refer = User::find($user->ref_id);

        if ($refer &&  $refer->account_type == 1 && $user->generate_com == 1) {
            if( $this->gana_ticket($refer) > 0)
            {
                    $amount = ($buy->amount * 10) / 100;
                    echo "<br> ============================ <br>";
                    referralComission_compensation($user->id, formatter_money($amount), $details,2);
            }
        }
            
    }

    function activa_paquete($user,$paquete, $product){
      
        $share = $paquete->total_share;
        $total_price = $paquete->amount;

        $user->shares_trx += $share;
        $user->total_invest_trx += $total_price;     
        $user->save();

        $product->total_sell += $share;
        $product->save();

        $paquete->status = 1;
        $paquete->act    = 1;
        $paquete->save();

    $user->transactions()->create([
                'trx' => getTrx(),
                'user_id' => $user->id,
                'amount' => trontoether($total_price),
                'main_amo' => trontoether($total_price),
                'amount_con' => $total_price,
                'main_amo_con' => $total_price,
                'balance' => $user->balance,
                'title' => $share . ' Contract Purchase TRX',
                'charge' => 0,
                'moneda' => $product->moneda,
                'type' => 'sharetrx_buy',
            ]);  
      
        $current_rank = CompenstionPlan::where('min_share', '<=', $share)->where('max_share', '>=', $share)->first();
        if ($current_rank == null) {
            $current_rank = CompenstionPlan::where('max_share', '<=', $share)->orderBy('max_share', 'desc')->first();
        }
        $details = $user->username . ' Buy ' . $share . ' Contracts TRX.';
      
        if ($current_rank) {

            $refer = User::find($user->ref_id);

            if ($refer && $current_rank->ref_bonus > 0 && $refer->account_type == 1 && $user->generate_com == 1) {
                $amount = ($total_price * $current_rank->ref_bonus) / 100;
               echo  $amount = $amount;
               echo "<br> ============================ <br>";
               referralComission_compensation($user->id, formatter_money($amount), $details,2);
            }
        }
              
        if ($user->generate_com == 1) {
            updateBV($user->id, (point_trx() * $share) , $details);
        }

    }

    function activa_paquete_vip($user,$paquete, $product){
      
        $share = $paquete->total_share;
        $total_price = $product->price * $share;

        $user->network_contracts_trx += $share;
        $user->total_invest_trx += $total_price;     
        $user->save();

        $product->total_sell += $share;
        $product->save();

        $paquete->status = 3;
        $paquete->act    = 1;
        $paquete->save();

        $user->transactions()->create([
            'trx' => getTrx(),
            'user_id' => $user->id,
            'amount' => trontoether($total_price),
            'main_amo' => trontoether($total_price),
            'amount_con' => $total_price,
            'main_amo_con' => $total_price,
            'balance' => $user->balance,
            'title' => $share . ' VIP Contracts Purchase TRX',
            'charge' => 0,
            'moneda' => $paquete->moneda,
            'type' => 'sharetrx_buy',
        ]); 
       
      
        $refer = User::find($user->ref_id);
        $details = $user->username . ' Buy ' . $share . ' VIP Contracts TRX.';
         
        if ($refer && $refer->account_type == 1 && $user->generate_com == 1) {
            $amount = ($total_price * $product->ref_bonus) / 100;
            referralComission_compensation($user->id, $amount, $details,2);
        }

        if ($user->generate_com == 1) {
            updateBV($user->id, $share  * point_trx(), $details);
        }
 
    }

    function activa_paquete_super($user,$paquete, $product){
      
        $share = $paquete->total_share;
        $total_price = $paquete->amount * $paquete->total_share;;

        $user->network_contracts_trx += $share;
        $user->total_invest_trx += $total_price;     
        $user->save();

        $product->total_sell += $share;
        $product->save();

        $paquete->status = 3;
        $paquete->act    = 1;
        $paquete->save();

        $user->transactions()->create([
            'trx' => getTrx(),
            'user_id' => $user->id,
            'amount' => trontoether($total_price),
            'main_amo' => trontoether($total_price),
            'amount_con' => $total_price,
            'main_amo_con' => $total_price,
            'balance' => $user->balance,
            'title' => $share . ' VIP Contracts Purchase TRX',
            'charge' => 0,
            'moneda' => $product->moneda,
            'type' => 'sharetrx_buy',
        ]); 
       
        $refer = User::find($user->ref_id);
        $details = $user->username . ' Buy ' . $share . ' VIP Contracts TRX.';
         
        if ($refer && $refer->account_type == 1 && $user->generate_com == 1) {
          
            $amount = ($total_price * $product->ref_bonus) / 100;
            referralComission_compensation($user->id, $amount, $details,2);
        }

        if ($user->generate_com == 1) {
            updateBV($user->id, $share  * point_trx(), $details);
        }
 
      
    }

    function contar($obj){
        $i=0;
        foreach($obj as $value){
            $i++;
        }
        return $i;
    }

    public function complet_deposit(){
        $deposits = Deposit::where('status',0)->where('method_currency','TRX')->get();
        if($deposits)
        {
             $product = NetworkContract::where('moneda',2)->first();
             foreach($deposits as $paquete)
             {  
                
                   $user = User::where('id',$paquete->user_id)->first();
                   "https://api.trongrid.io/v1/transactions/".$paquete->id_tx."/events";
                    $res =   file_get_contents("https://api.trongrid.io/v1/transactions/".$paquete->id_tx."/events");
                    $res =json_decode($res);
                    if(@$res->data[0])
                    {
                         $total = $this->contar($res->data[0]);
                        if($total == 9)
                        {
                                    $data = $res->data[0];   
                                
                                    $contr_user = $data->result->user;
                                    $contr_status = $res->success;
                                                if( $contr_status == "1" )
                                                {
                                                    if($user->username == $contr_user)
                                                    {
                                                            $monto = $data->result->monto / 1000000;
                                                            $this->crea_deposito($user, $paquete, $monto);
                                                    } 
                                                }
                        }else{
                            print_r($res);
                            echo "<br>";
                            echo   "estamos aqui :: ".$total;
                        }
                    }else{
                           $paquete->status = 5;
                           $paquete->save();
                    }
             }
        }

        echo "Completed Rutina";

    }

    public function completa_paquete($id){
        $share = Share::where('id',$id)->first();
            if($share->max_earning == $share->return_profit)
            {
               echo  $share->status = 2;
               $share->save();
            }
    }

    function crea_deposito($user,$deposito, $monto_trx){

        $monto_eth = $deposito->amount;

        if($user->status == 0){
            $user->active = 1;
        }else{
            $user->balance += $monto_eth;
        }
        $user->save(); 
        $deposito->status = 1;
        $deposito->save();

        $gate = GatewayCurrency::where('method_code', $deposito->method_code)->where('currency', 'TRX')->first();
     
        $charge_trx = formatter_money($gate->fixed_charge + ($monto_trx * $gate->percent_charge / 100));
        $withCharge_trx = $monto_trx + $charge_trx;
        $final_amo_trx = formatter_money($withCharge_trx * $gate->rate);

        $trx_n = getTrx();
        Trx::create([
            'user_id' => $user->id,
            'amount' => formatter_money($deposito->amount),
            'main_amo' => formatter_money($deposito->amount),
            'charge' => 0,
            'amount_con' => $monto_trx,
            'main_amo_con' => $monto_trx - $charge_trx,
            'charge_con' => $charge_trx,
            'type' => 'deposit',
            'title' => 'Deposit Via ' . $gate->name,
            'trx' => $trx_n,
            'moneda' => 2,
            'balance' => $user->balance,
        ]);
    
        send_tele($user, 'DEPOSIT_SUCCESS', [
            'amount' => formatter_money($request->amount),
            'method' => $gate->name,
        ]); 
        
    }

                                function valida_wallet_eth($user){
                                        $smart = 1;
                                        $wallet_smart = "";
                                        try{
                                            $data = array('id'=>$user);
                                            $result = $this->send_smart('/eth/wallet', $data);
                                            $wallet_smart = @$result->result;
                                        }catch(exception $ex){  }

                                        if($wallet_smart == "" or $wallet_smart == '0x0000000000000000000000000000000000000000'){
                                            $smart = 0;
                                            }else{ $smart = 1; }
                                    return $smart;
                                }

                                function send_smart($sesion, $data){
                                    $arra = "";
                                    foreach ($data as $key => $value) {
                                        if($arra != "") $arra .= "&";
                                        $arra .= $key."=".$value;
                                    }
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL,"http://labts.procashdream.com/smart".$sesion);
                                    curl_setopt($ch, CURLOPT_POST, TRUE);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS,$arra);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    $resul = curl_exec ($ch);
                                    curl_close ($ch);
                                    $resu = json_decode($resul);
                                    return $resu;
                                }

                                function gana_ticket($user){
                                     $sort = sorteo::where('status',0)->first();
                                     if($sort == null) 
                                     return 0;
                                     $tick = ticket::where('sorteo',$sort->id)->where('user_id',$user->id)->count();
                                     if($tick <1 ) 
                                     return 0;
                                     else
                                     return 1;
                                }


                            public function puntos_cagada(){

                               $valor = BvLog::where('details', 'lauracarera2 Buy 857 Contracts TRX.')->get();

                                foreach($valor as $data){
                                    $posi = $data->position;
                                    $monto = $data->amount;
                                    $user = UserExtra::where('user_id', $data->user_id)->first();
                                    echo "Puntos ganados ".$monto."<br>";

                                    if($posi==2)
                                    {  
                                            echo "Right :: ";
                                            echo $user->bv_right;
                                            echo "<br>";
                                        echo  $user->bv_right -= $monto;
                                            echo '<br>';
                                    }else{
                                            echo "LEFT :: ";
                                            echo $user->bv_left;
                                            echo "<br>";
                                            echo $user->bv_left -= $monto;
                                            echo '<br>';
                                    }
                                            echo "<hr>";
                                      //  $user->save();
                                      //  $data->delete();
                                }
                                
                            }    


                         


                            public function puntos(){
                                $user = User::where('status',1)->where('balance_resrv','>',0)->get();
                                foreach($user as $data){
                                    $to_va = 0;
                                    $matrixs = MatrixSubscriber::where('user_id', $data->id)->where('status',1)->orderBy('matrix_plan_id')->get();
                                    foreach($matrixs as $val){
                                         echo  $id_ma = $val->matrix_plan_id;   
                                         echo " -- ";
                                          $total_apartado = UserMatrix::where('id_matrix',$val->id)->where('pos_id',2)->count();
                                          if($total_apartado > 0)
                                             { 
                                              $va = $this->cuanto_tiene($total_apartado, $id_ma);
                                              $to_va += $va;
                                              echo "total reserver ".$total_apartado." ".$va;   
                                             }
                                          else{
                                              $to_va += 0;
                                          }
                                              echo '<br>';
                                    }
                                    echo $data->id." - ".$data->username." reservado eth ".$data->balance_resrv." <---> reservado TRX ".$to_va;
                                     
                                    $data->balance_resrv_trx = $to_va;
                                    $data->save();
                                    echo '<hr>';
                                }
                            }
                            
                            

                            public function find_rank($user, $rank, $position){
                                $ref = User::where('ref_id', $user)->where('rank_id',$rank)->where('position',$position)->count();
                                return $ref; 
                            }


                            public function cuanto_tiene($total, $id_ma){
                                      switch($id_ma){
                                           case 1:   $valor = 875;    break;
                                           case 2:   $valor = 2250;   break;
                                           case 3:   $valor = 4500;   break;
                                           case 4:   $valor = 500;    break;
                                           default:  $valor = 0;
                                      }
                                     $balance = $total * $valor;
                                     return $balance;
                            }
                            
                            
                            
                            
                            public function primer_binario()
                                        {   
                                            // return   $time = Carbon::now()->toDateTimeString();
                                            set_time_limit(0);
                                        
                                    
                                            $eligibleUsers = UserExtra::where('bv_left', '>=', 1)->where('bv_right', '>=', 1)->join('users', 'users.id', 'user_extras.user_id')->where('paga_binario',0)->get();
                                           
                                            foreach ($eligibleUsers as $uex) {
                                                
                                                 $user = $uex->user;
                                                 $valida = UserExtra::where('bv_left', '>=', 1)->where('bv_right', '>=', 1)->where('user_id', $uex->user_id)->count();
                                                 
                                                 echo "<br>  *****  ".$valida." ****** <br>";
                                            if($valida > 0)
                                            {         
                                                             // y corta si no lo tiene 
                                                            $user = $uex->user;
                                                            $max_profit = check_max_profit($user->id);
                                                            $weak = $uex->bv_left < $uex->bv_right ? $uex->bv_left : $uex->bv_right;
                                                
                                                            $current_rank = CompenstionPlan::first();
                                                                $uex->bv_left -= $weak;
                                                                $uex->bv_right -= $weak;
                                                                $uex->save();
                                                                echo "*****".$user->id." ***** <br>";
                                                
                                                            if ($current_rank == null) {
                                                                $current_rank = CompenstionPlan::where('max_share', '<=', 1)->orderBy('max_share', 'desc')->first();
                                                            }
                                                            
                                                            
                                                            if($user->paga_binario == 0){
                                                                           $user->paga_binario = 1;
                                                                           $user->save();
                                                            }else{
                                                                        
                                                            }
                                                            echo "<hr>";
                                                }
                                             }
                                          
                                        }

}
