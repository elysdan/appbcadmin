<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use App\WithdrawMethod;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\GeneralSetting;
use App\Withdrawal;
use App\Trx;
use App\pago;
use Carbon\Carbon;
use App\Prew_withdraw;
use App\Lib\CoinPaymentHosted;


class WithdrawMethodController extends Controller
{
    public function WithdrawRequestCreate($frecue)
    {
         $date = Carbon::now()->toDateString();
        $page_title = 'Create Withdrawal Etehreum';
        $method = WithdrawMethod::where('id',3)->first();
        $withdraws = User::where('interest_wallet_usdt','>=', 200)->where('pay','1')->where('forma_p','1')->where('pay_frecuencia',$frecue)->where('pay_user','1')->where('smart',1)->where('status','1')->where('kyc','1')->orderBy('interest_wallet_usdt')->paginate(200,['id','username','interest_wallet','interest_wallet_usdt','etherium_wallet_code','forma_p']);
        $resp   =  $this->send_smart('/eth/balance', array('dire'=>'0xbAa37930a2B24bc17F92f26c86D8A514539A7a44'));
        $saldo_contracto    =  @$resp->result;
        $forma_pago = 1;
        return view('admin.withdraw.index', compact('page_title','withdraws', 'method','saldo_contracto', 'forma_pago', 'frecue'));
    }
    
     public function WithdrawRequestCreate_f()
    {
          session_start();
        $frecuencia = $_GET['frecuencia'];
        $_SESSION['frecue'] = $frecuencia;
        $page_title = 'Create Withdrawal Etehreum';
        $method = WithdrawMethod::where('id',3)->first();
        $withdraws = User::where('interest_wallet_usdt','>=', 200)->where('pay','1')->where('forma_p','1')->where('pay_frecuencia',$frecuencia)->where('pay_user','1')->where('smart',1)->where('status','1')->where('kyc','1')->orderBy('interest_wallet_usdt')->paginate(200,['id','username','interest_wallet','interest_wallet_usdt','etherium_wallet_code','forma_p']);
        $resp   =  $this->send_smart('/eth/balance', array('dire'=>'0xbAa37930a2B24bc17F92f26c86D8A514539A7a44'));
        $saldo_contracto    =  @$resp->result;
        $forma_pago = 1;
        return view('admin.withdraw.index', compact('page_title','withdraws', 'method','saldo_contracto', 'forma_pago', 'frecue'));
    }
    
    
    
    public function WithdrawRequestCreate_usdt($frecue)
    {
          $date = Carbon::now()->toDateString();
        $page_title = 'Create Withdrawal Theter-USDT';
        $method = WithdrawMethod::where('id',3)->first();
        $withdraws = User::where('interest_wallet_usdt','>=', 10)->where('pay','1')->where('pay_user','1')->where('pay_frecuencia', $frecue)->where('forma_p',3)->where('next_pay', '<=', $date)->where('wallet_usdt','<>','0')->where('status','1')->where('kyc','1')->orderBy('interest_wallet_usdt')->paginate(200,['id','username','interest_wallet','interest_wallet_usdt','etherium_wallet_code','forma_p']);
        $resp   =  $this->send_smart('/usdt/saldo', array('dire'=>'TLgDku879wxak8bKXe2Dz1km2azRmkNGr1'));
        $saldo_contracto    =  @$resp->result;
        $forma_pago = 3;
        return view('admin.withdraw.index', compact('page_title','withdraws', 'method','saldo_contracto', 'forma_pago', 'frecue'));
    }
    
     public function WithdrawRequestCreate_usdt_f()
    {
        $frecuencia = $_GET['frecuencia'];
        $page_title = 'Create Withdrawal Theter-USDT';
        $method = WithdrawMethod::where('id',3)->first();
        $withdraws = User::where('interest_wallet_usdt','>=', 10)->where('pay','1')->where('pay_user','1')->where('pay_frecuencia', $frecuencia)->where('forma_p',3)->where('wallet_usdt','<>','0')->where('status','1')->where('kyc','1')->orderBy('interest_wallet_usdt')->paginate(200,['id','username','interest_wallet','interest_wallet_usdt','etherium_wallet_code','forma_p']);
        $resp   =  $this->send_smart('/usdt/saldo', array('dire'=>'TLgDku879wxak8bKXe2Dz1km2azRmkNGr1'));
        $saldo_contracto    =  @$resp->result;
        $forma_pago = 3;
        return view('admin.withdraw.index', compact('page_title','withdraws', 'method','saldo_contracto', 'forma_pago'));
    }



    public function WithdrawRequestCreate_tron($frecue)
    {
          /*session_start();
         $frecue = @$_SESSION['frecuencia'];
        if($frecue == "")
        $frecue = 1; */
        
        $date = Carbon::now()->toDateString();
        $page_title = 'Create Withdrawal Tron(TRX)';
        $method = WithdrawMethod::where('id',1)->first();
        $method2 = WithdrawMethod::where('id',2)->first();

        $withdraws = User::whereRaw(
                          "
                          (interest_wallet_usdt >= 10 and
                           status = 1 and wallet_tron <> '41' and kyc = 1 and forma_p = 2 and pay = 1 and pay_user = 1 and pay_frecuencia = '".$frecue."' and next_pay <= '".$date."' )
                          "
                       )->orderBy('interest_wallet_usdt')->paginate(50,['id','username','interest_wallet','interest_wallet_usdt','etherium_wallet_code','forma_p']);
                       
                       
        $resp   =  $this->send_smart('/trx_web/balance', array('_banco_'=>'TFYwQ7cdWXMXUfSeCMCUV5XCNTfjxrM3Ma','apikey'=>'zOe@9x0YF$bdTpvZ'));
        $saldo_contracto    =  @$resp->result;
        $forma_pago = 2;
        $precio_eth  = price_ether_btc();
        $precio_tron = price_tron_btc();
        $i = 0;
        return view('admin.withdraw.index', compact('page_title','withdraws', 'method','saldo_contracto','forma_pago', 'frecue'));
    }
    
    
     public function WithdrawRequestCreate_tron_f()
    {
          session_start();
         $frecuencia = $_GET['frecuencia'];
          $_SESSION['frecue'] = $frecuencia;
        $page_title = 'Create Withdrawal Tron(TRX)';
        $method = WithdrawMethod::where('id',1)->first();
        $method2 = WithdrawMethod::where('id',2)->first();

        $withdraws = User::whereRaw(
                          "
                          (interest_wallet_usdt >= 10 and
                           status = 1 and wallet_tron <> '41' and kyc = 1 and forma_p = 2 and pay = 1 and pay_user = 1 and pay_frecuencia = '".$frecuencia."')
                          "
                       )->orderBy('interest_wallet_usdt')->paginate(50,['id','username','interest_wallet','interest_wallet_usdt','etherium_wallet_code','forma_p']);
                       
                       
        $resp   =  $this->send_smart('/trx_web/balance', array('_banco_'=>'TKSSoisLQk9Lt2wxtGvSv2ZhreRAJcqStx', 'apikey'=>'zOe@9x0YF$bdTpvZ'));
        $saldo_contracto    =  @$resp->result;
        $forma_pago = 2;
        $precio_eth  = price_ether_btc();
        $precio_tron = price_tron_btc();
        $i = 0;
        return view('admin.withdraw.index', compact('page_title','withdraws', 'method','saldo_contracto','forma_pago'));
    }





    public function create_pago($moneda){
        $pago = new pago();
        $pago->moneda = $moneda;
        $pago->status = 0;
        $pago->id_tx = '';
        $pago->save();
        return $pago;
    }


    public function re_send(Request $request){
        
         $pago = pago::where('id',$request->id_pago)->where('status','1')->first();
         
         $preview = Prew_withdraw::where('pago_id',$pago->id)->get();
         
               foreach($preview as $data){
                                                $pagos_inline[] = array(
                                                        'id_user'=>$data->user_id,
                                                        'amountSend_con'=>round($data->enviado_trx,6)
                                                        );
                                         }
               
               $result =  $this->send_pay_tron(@$pagos_inline);
                                 if(@$result->res == 'ok')
                                 {
                                    $hash = (string)$result->result;
                                    $pago->id_tx = $hash;
                                    $pago->status = 1;
                                    $pago->save();
                                    $notify[] = ['success', 'Withdraw Completed Successfully!'];
                                    return back()->withNotify($notify);
                                 }else{
                                    $notify[] = ['error', 'Servidor smart Contract pagado o blockchaim no responde!'];
                                    return back()->withNotify($notify);
                                 }    
    }


    public function up_hash(Request $request){
        $pago = pago::where('id',$request->id_pago_h)->where('status','1')->first();
        $pago->id_tx = $request->hash;
        $pago->save();
        $notify[] = ['success', 'HASH BEEN CHANGE!'];
        return back()->withNotify($notify);
    }
    
    public function porc_frecuencia($valor){
        
        switch($valor){
            case 2: $res = 8; break;
            case 3: $res = 5; break;
            default: $res = 10;
        }
        return $res;
    }
    
    public function WithdrawRequestStore_usdt(Request $request)
    {
        
       
            $method = WithdrawMethod::where('id',3)->first();
            set_time_limit(0);
            $request->validate([
                'user' => 'required',
            ]);
              
         
              $por_fee = $this->porc_frecuencia($_POST['frecuencia']);
              
            foreach($request->user as $user_id => $a){
                                $user = User::findOrFail($user_id);

                                            $monto_retitar = $user->interest_wallet_usdt;
                                            $charge_ori = round(($monto_retitar * $por_fee)/100,2);
                                            $retiro_ori = round($monto_retitar - $charge_ori,2);
                                        
                                        	$amountSend = $retiro_ori;
                                            $tnxnum = getTrx();
                                            
                                            if($user->wallet_usdt == 'TXQr5keCvQdtgijf58frhR4Taxq6D9B9RR')
                                            {
                                                echo 'Wallet Detectada';
                                            }else
                                            {
                                                 $pagos_inline[] = array(
                                                                'id_user'=>$user->id,
                                                                'interest_wallet'=> $monto_retitar,
                                                                'charge'=> $charge_ori,
                                                                'tnx_num'=>$tnxnum,
                                                                'amountSend'=>$retiro_ori,
                                                                'moneda'=>3
                                                                );
                                            }

            }  
              
          
             
                 $i = 0;
                 if(count($pagos_inline)>0)
                 {
                     
                  // [interest_wallet] => 10.0000 [charge] => 1 [tnx_num] => GG98F8CB7YEQ [amountSend] => 9 [moneda] => 3
                   
                    foreach(@$pagos_inline as $data){
                        
                        $data = (object)$data;
                        
                                                $user = User::where('id',$data->id_user)->first();
                                                $pago = $this->create_pago(3);
                                                  
                                                $pre = new Prew_withdraw();
                                                $pre->pago_id = $pago->id;
                                                $pre->user_id = $user->id;
                                                $pre->moneda  =  3;
                                                $pre->solicitud_eth = $data->interest_wallet;
                                                $pre->carga_eth     = $data->charge;
                                                $pre->enviado_eth   = $data->amountSend;
                                                $pre->solicitud_trx = 0;
                                                $pre->carga_trx     = 0;
                                                $pre->enviado_trx   = 0;
                                                $pre->trx           = $data->tnx_num;
                                                $pre->id_tx         = '';
                                                $pre->balance       =   0;
                                                $pre->balance_trx   =   0;
                                                $pre->balance_usdt  =  $data->interest_wallet;
                                                $pre->precio_dolar  = 1;
                                                
                                                $result  =  $this->send_pay_usdt($user->wallet_usdt, $data->amountSend);
                                                
                                              
                                                if(@$result->res == 'ok')
                                                {
                                                     $user->interest_wallet_usdt -= $data->interest_wallet; 
                                                     $user->save();
                                                     
                                                     $hash = (string)$result->result;
                                                     $i++;
                                                     $pre->hash_usdt = $hash;
                                                     $pago->status = 1;
                                                     $pago->id_tx = $hash;
                                                     
                                                }else{
                                                  //  echo "no pagop";
                                                    $pago->status = 2;
                                                    
                                                }
                                                $pre->save();
                                                $pago->save();
                                                   
                                             
                     }
                       
                         
                        
                     if($i>0)
                     {
                            $notify[] = ['success', 'Withdraw Completed Successfully!'];
                            return back()->withNotify($notify);
                     }
                     else{
                         
                          $notify[] = ['error', 'This payment will be pending!'];
                          return back()->withNotify($notify);
                     }
                   
                  

                 }else{
                            Prew_withdraw::where('pago_id',$pago->id)->delete();
                            $pago->delete();
                            $notify[] = ['error', 'Los susuarios no tienen wallets asignadas'];
                            return back()->withNotify($notify);
                 }
                 return;

    }
    
    
    public function WithdrawRequestStore_tron(Request $request)
    {
        
       
           $method = WithdrawMethod::where('id',3)->first();
        
            set_time_limit(0);
            $request->validate([
                'user' => 'required',
            ]);
            
            
                
           $por_fee = $this->porc_frecuencia($_POST['frecuencia']);
           
          
           
           $pago = $this->create_pago(2);
           
                   $precio_eth  = dolar_eth();
                   $precio_tasa = round(dolar_tron(),6);
          
            foreach($request->user as $user_id => $a){

                                $user = User::findOrFail($user_id);

                                $bala_eth = $user->interest_wallet;

                              $monto_retitar = $user->interest_wallet_usdt;
                              
        							       $monto_cambio = ($monto_retitar / $precio_tasa);
        							       
        							       
        							    
        							       
        							       $exchange = round($monto_cambio * 0.98,6);
        							       $fee = round(($exchange  * $por_fee) / 100,8);
        							       $retirable = $exchange - $fee;

                                            $wdamo    =  $exchange;
                                            $wdamo_tx = $exchange;
                                            $charge     = $fee;
                                            $amountSend = $retirable;
                                            
                                    
                                            $charge_ori = round(($monto_retitar * $por_fee) / 100,2);
                                            $retiro_ori = round($monto_retitar - $charge_ori,2);
                                        
                                        
                            	$amountSend = $amountSend;
                                $tnxnum = getTrx();
                                
                        try{
                            $data = array('id'=>$user->id,'apikey'=>'zOe@9x0YF$bdTpvZ' );
                            $result = $this->send_smart('/trx_web/wallet', $data);
                                    
                                     if(@$result->result != "T9yD14Nj9j7xAB4dbGeiX9h8unkKHxuWwb")
                                      {
                                            if(@$result->result != "") {

                                            $pagos_inline[] = array(
                                                                'id_user'=>$user->id,
                                                                'interest_wallet'=> $monto_retitar,
                                                                'wdamo'=> $wdamo,
                                                                'charge'=> $charge_ori,
                                                                'wdamo_trx'=>round($wdamo,6),
                                                                'wdamo_eth'=>0,
                                                                'wdamo_usdt'=>$monto_retitar,
                                                                'wdamo_con'=> $wdamo,
                                                                'charge_con'=> round($charge,6),
                                                                'amountSend_con'=>round($amountSend,6),
                                                                'tnx_num'=>$tnxnum,
                                                                'precio_dolar'=>$precio_tasa,
                                                                'amountSend'=>$retiro_ori,
                                                                'moneda'=>2
                                                                );

                                                $pre = new Prew_withdraw();
                                                $pre->pago_id = $pago->id;
                                                $pre->user_id = $user->id;
                                                $pre->moneda  =  2;
                                                $pre->solicitud_eth = $monto_retitar;
                                                $pre->carga_eth     = $charge_ori;
                                                $pre->enviado_eth   = $retiro_ori;
                                                $pre->solicitud_trx = round($wdamo,6);
                                                $pre->carga_trx     = round($charge,6);
                                                $pre->enviado_trx   = round($amountSend,6);
                                                $pre->trx           = $tnxnum;
                                                $pre->id_tx         = '';
                                                $pre->balance       =   0;
                                                $pre->balance_trx   =   0;
                                                $pre->precio_dolar  = $precio_tasa;
                                                $pre->balance_usdt  = $monto_retitar;
                                              
                                                $pre->save();
                                            }
                                      }   
                        }catch(Exception $err){
                                  $notify[] = ['error', 'Servidor smart Contract pagado!'];
                                  return back()->withNotify($notify);
                        }
        }
        
         
          
                 if(count(@$pagos_inline)>0)
                 {
                     
                    $preview = Prew_withdraw::where('pago_id',$pago->id)->get();
                    foreach($preview as $data){
                                                $user = User::where('id',$data->user_id)->first();
                                                $user->interest_wallet_usdt -= $data->balance_usdt; 
                                                $user->save();
                                            }
                             
                
                        $result =  $this->send_pay_tron(@$pagos_inline);
               
                        if(@$result->res == 'ok')
                        {
                            $hash = (string)$result->result;
                            $pago->id_tx = $hash;
                            $pago->status = 1;
                            $pago->save();
                            $notify[] = ['success', 'Withdraw Completed Successfully!'];
                            return back()->withNotify($notify);
                        }else{
                            $pago->status = 2;
                            $pago->save();
                            $notify[] = ['error', 'This payment will be pending!'];
                            return back()->withNotify($notify);
                        }

                 }else{
                    Prew_withdraw::where('pago_id',$pago->id)->delete();
                    $pago->delete();
                 }
                 return;

    }



    public function WithdrawRequestStore(Request $request)
    {
        
       
         
            set_time_limit(0);
            $request->validate([
                'user' => 'required',
            ]);
            
            
             $por_fee = $this->porc_frecuencia($_POST['frecuencia']);
           
            $precio_eth  = dolar_eth();
            $precio_tasa = round(dolar_tron(),2);

            $method = WithdrawMethod::where('id',3)->first();
            
            
            foreach($request->user as $user_id => $a){
    
                                $user = User::findOrFail($user_id);
                                
                                           $monto_retitar = $user->interest_wallet_usdt;
        							       $monto_cambio = ($monto_retitar / $precio_eth);
        							       $exchange = round($monto_cambio * 0.98,6);
        							       $fee = round(($exchange  * $por_fee) / 100,8);
        							       $retirable = $exchange - $fee;

                                if($method->max_limit <  $monto_retitar){
                                    $wdamo_et =  $exchange;
                                    $wdamo = $method->max_limit;
                                    
                            	}else{
                                    $wdamo_et =  $exchange;
                                    $wdamo =  $exchange;
                                }

                            	$charge = $fee;
                            	$amountSend = $retirable;
                                $tnxnum = getTrx();
                                
                                $charge_ori = round(($monto_retitar * $por_fee/100),2);
                                $retiro_ori = round($monto_retitar - $charge_ori,2);
                                
                     
                        try{
                          $data = array('id'=>$user->id);
                          $result = $this->send_smart('/eth/wallet', $data);
                           if(@$result->result != "0x0000000000000000000000000000000000000000")
                          {
                             if(@$result->result != "") {
                              $pagos_inline[] = array(
                                                 'id_user'=>$user->id,
                                                 'interes_wallet'=> $exchange,
                                                 'interes_wallet_usdt'=> $monto_retitar,
                                                 'wdamo'=>$wdamo,
                                                 'wdamo_eth'=>$wdamo_et,
                                                 'wdamo_trx'=>0,
                                                 'charge'=> $charge,
                                                 'charge_ori' => $charge_ori,
                                                 'tnx_num'=>$tnxnum,
                                                 'amountSend'=>$amountSend,
                                                 'send_ori' => $retiro_ori
                                                );
                             }
                          }

                        }catch(Exception $err){
                                  $notify[] = ['error', 'Servidor smart Contract Apagado!'];
                                  return back()->withNotify($notify);
                        }


                    // fin

////////////////////////AUTOMATED
        }
                     

          
                     $result =  $this->send_pay(@$pagos_inline);  
                  
                       if (@$result->res == 'ok') {

                             $hash = (string)$result->result;
                       

                            foreach(@$pagos_inline as $valor){

                                         $wdamo      = $valor['interes_wallet_usdt'];
                                         $wdamo_eth  = $valor['interes_wallet_usdt'];
                                         $wdamo_trx  = $valor['interes_wallet_usdt'];
                                         $charge     = $valor['charge_ori'];
                                         $amountSend = $valor['send_ori'];
                                         $tnxnum     = $valor['tnx_num'];
                                        // echo $wdamo." - ".$charge." - ".$amountSend;

                                         $user = User::findOrFail($valor['id_user']);

                                        $withdraw = new Withdrawal();
                                        $withdraw->method_id   = $method->id;
                                        $withdraw->user_id     = $user->id;
                                        $withdraw->amount      = formatter_money($wdamo);
                                        $withdraw->charge      = formatter_money($charge);
                                        $withdraw->rate        = 1;
                                        $withdraw->currency    =  'ETH';
                                        $withdraw->delay       = 0;
                                        $withdraw->final_amo   = $amountSend;
                                        $withdraw->status      = 2;
                                        $withdraw->email_code  = verification_code(6);
                                        $withdraw->trx = $tnxnum;
                                        $withdraw->save();

                                        $user->interest_wallet_usdt -= $wdamo;
                                        $user->save();

                                        $trx = new Trx();
                                        $trx->user_id = $user->id;
                                        $trx->amount = formatter_money($wdamo);
                                        $trx->charge = formatter_money($charge);
                                        $trx->main_amo = formatter_money($amountSend);
                                        $trx->balance = formatter_money($user->interest_wallet);
                                        $trx->type = 'withdraw';
                                        $trx->trx = $tnxnum;
                                        $trx->hash =  $hash;
                                        $trx->title = 'withdraw Via Smart Contract';
                                        $trx->save();

                                        $withdraw->status = 1;
                                        $withdraw->save();

                                  
                                        $general = GeneralSetting::first();
                                        send_tele($user, 'WITHDRAW_APPROVE', [
                                            'trx' => $hash,
                                            'amount' => $general->cur_sym . formatter_money($withdraw->amount)." ( ".$withdraw->currency.") ",
                                            'receive_amount' => $general->cur_sym . formatter_money($withdraw->amount - $withdraw->charge)." ( ".$withdraw->currency.") ",
                                            'charge' => $general->cur_sym . formatter_money($withdraw->charge)." ( ".$withdraw->currency.") ",
                                            'method' => $withdraw->method->name,
                                        ]);

                                        send_sms($user, 'WITHDRAW_APPROVE', [
                                            'trx' => $hash,
                                            'amount' => $general->cur_sym . formatter_money($withdraw->amount),
                                            'receive_amount' => $general->cur_sym . formatter_money($withdraw->amount - $withdraw->charge),
                                            'charge' => $general->cur_sym . formatter_money($withdraw->charge),
                                            'method' => $withdraw->method->name,
                                        ]);
                                
                             }
                         
                                $notify[] = ['success', 'Withdraw Completed Successfully!'];
                                return back()->withNotify($notify);
                         }
                         else{

                               $notify[] = ['error', 'Withdraw no pudo ser completado!'];
                                return back()->withNotify($notify);
                         }
    }


    public function methods()
    {
        $page_title = 'Withdraw Methods';
        $empty_message = 'Withdraw Methods not found.';
        $methods = WithdrawMethod::orderByDesc('status')->orderBy('id')->paginate(config('constants.table.default'));
        return view('admin.withdraw.methods', compact('page_title', 'empty_message', 'methods'));
    }


    public function create()
    {
        $page_title = 'New Withdraw Method';
        return view('admin.withdraw.create', compact('page_title', 'saldo_contracto'));
    }


    public function store(Request $request)
    {
        $validation_rule = [
            'name' => 'required|max: 60',
            'image' => 'required|image',
            'image' => [new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'rate' => 'required|gt:0',
            'delay' => 'required',
            'currency' => 'required',
            'min_limit' => 'required|gt:0',
            'max_limit' => 'required|gte:0',
            'fixed_charge' => 'required|gte:0',
            'percent_charge' => 'required|between:0,100',
            'instruction' => 'required|max:64000',

            'ud.*' => 'required',
        ];
        
        $request->validate($validation_rule, [], ['ud.*' => 'All user data']);
        $filename = '';
        
        if ($request->hasFile('image')) {
            try {
                $filename = upload_image($request->image, config('constants.withdraw.method.path'), config('constants.withdraw.method.size'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $method = WithdrawMethod::create([
            'name' => $request->name,
            'image' => $filename,
            'rate' => $request->rate,
            'delay' => $request->delay,
            'min_limit' => $request->min_limit,
            'max_limit' => $request->max_limit,
            'fixed_charge' => $request->fixed_charge,
            'percent_charge' => $request->percent_charge,
            'currency' => $request->currency,
            'description' => $request->instruction,
            'user_data' => $request->ud ?: [],
        ]);

        $notify[] = ['success', $method->name . ' has been added.'];
        return redirect()->route('admin.withdraw.method.methods')->withNotify($notify);
    }



    public function edit($id)
    {
        $page_title = 'Update Withdraw Method';
        $method = WithdrawMethod::findOrFail($id);
        return view('admin.withdraw.edit', compact('page_title', 'method'));
    }



    public function update(Request $request, $id)
    {
        $validation_rule = [
            'name' => 'required|max: 60',
            'image' => 'nullable|image',
            'image' => [new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'min_limit' => 'required|gt:0',
            'max_limit' => 'required|gte:0',
            'fixed_charge' => 'required|gte:0',
            'percent_charge' => 'required|between:0,100',
        ];
        $request->validate($validation_rule, [], ['ud.*' => 'All user data']);

        $method = WithdrawMethod::findOrFail($id);
        $filename = $method->image;
        if ($request->hasFile('image')) {
            try {
                $filename = upload_image($request->image, config('constants.withdraw.method.path'), config('constants.withdraw.method.size'), $method->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $method->update([
            'name' => $request->name,
            'image' => $filename,
            'min_limit' => $request->min_limit,
            'max_limit' => $request->max_limit,
            'fixed_charge' => $request->fixed_charge,
            'percent_charge' => $request->percent_charge,
            'val1' => $request->val1,
            'val2' => $request->val2,
        ]);

        $notify[] = ['success', $method->name . ' has been updated.'];
        return back()->withNotify($notify);
    }


    public function activate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $method = WithdrawMethod::findOrFail($request->id);
        $method->update(['status' => 1]);
        $notify[] = ['success', $method->name . ' has been activated.'];
        return redirect()->route('admin.withdraw.method.methods')->withNotify($notify);
    }


    public function deactivate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $method = WithdrawMethod::findOrFail($request->id);
        $method->update(['status' => 0]);
        $notify[] = ['success', $method->name . ' has been deactivated.'];
        return redirect()->route('admin.withdraw.method.methods')->withNotify($notify);
    }


    function send_pay($valores){
        
              $usuarios = "";
              $montos = "";
              $i=1;
             foreach($valores as $valor)
             {
                  if($usuarios!=""){ $usuarios .= ",";  $montos   .= ","; }

                $usuarios .= $valor['id_user'];
                $montos   .= $valor['amountSend'];
                //$montos    .= "0.00002";
                $i++;
             }
         if($usuarios == "")
              {
                   return array('res'=>'1', 'result'=>'nothing');
                }
                 
           $data = array('dire'=>$usuarios,  'monto'=> $montos );
           $result = $this->send_smart('/eth/pagar', $data);
           return $result;
    }
   
    
    function calcula_tron($ether, $btceth, $btctrx) {
        $btc_ether = $ether * $btceth;
        $tron = $btc_ether / $btctrx;
        return round($tron,6);
    }


    function calcula_ether($tron, $btceth, $btctrx) {
        $eth_tron =  $btctrx / $btceth;
        $ether =  $eth_tron * $tron;
        return round($ether,8);
    }
 
    
    
     function send_pay_usdt($usuarios, $montos){
                 $data = array('dire'=>$usuarios,  'monto'=> $montos );
                 
                 $result = $this->send_smart('/usdt/pagar', $data);
                 return $result;
     }
    
    
    
    function send_pay_tron($valores){
         
        $usuarios = "";
        $montos = "";
        $i=1;
        $total = count($valores);
        
        foreach($valores as $valor)
        {
                if($usuarios!=""){ $usuarios .= ",";  $montos   .= ","; }
                $usuarios .= $valor['id_user'];
                $montos   .= $valor['amountSend_con'];
                
                $ren = new_renglon($i, $total, 9,175);
                if($resp != ""){
                $usuarios .= ",".$ren->id_user;
                $montos   .= ",".$res->monto; 
                } 

            $i++;
        }
       
        if($usuarios == "")
          {
             return array('res'=>'1', 'result'=>'nothing');
          }
          
        return;

        $data = array('_loteria_'=>$usuarios,  '_caleta_'=> $montos,'apikey'=>'zOe@9x0YF$bdTpvZ' );
        $result = $this->send_smart('/trx_web/pagar', $data);
        return $result;

     }



                            function send_smart($sesion, $data){

                                                        $arra = "";
                                                        foreach ($data as $key => $value) {
                                                            if($arra != "") $arra .= "&";
                                                            $arra .= $key."=".$value;
                                                        }

                                                        $ch = curl_init();
                                                        curl_setopt($ch, CURLOPT_URL,"https://promise.procashdream.com/".$sesion);
                                                        curl_setopt($ch, CURLOPT_POST, TRUE);
                                                        curl_setopt($ch, CURLOPT_POSTFIELDS,$arra);
                                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                        $resul = curl_exec ($ch);
                                                        curl_close ($ch);
                                                        $resu = json_decode($resul);
                                                        return $resu;
                                                }


}
