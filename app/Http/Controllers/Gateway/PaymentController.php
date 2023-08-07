<?php

namespace App\Http\Controllers\Gateway;

use App\GeneralSetting;
use App\Trx;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GatewayCurrency;
use App\Deposit;
use App\WithdrawMethod;
use Illuminate\Support\Facades\Auth;
use Session;
use App\User;
use App\Gateway;
use App\Rules\FileTypeValidate;
use App\Lib\CoinPaymentHosted;

class PaymentController extends Controller
{
    
    public function deposit()
    {
        $data['user'] = auth()->user();
        $gatewayCurrency = GatewayCurrency::where('currency','USDT')->whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->get();
        $data['page_title'] = 'Deposit';
        $data['gatewayCurrency'] = $gatewayCurrency;
        $data['wallet_deposito'] = 'TX3uFdVy8BETN4ZpnSGhsdUJA9CrHXowUv';
        $data['wallet_bep20']    = '0xE391745ef37A34b8d8c7C91fd08f615b8535C8f1';
        $deposits = Deposit::where('user_id',auth()->user()->id)->latest()->paginate(15);
        $data['deposits'] = $deposits;
        return view(activeTemplate() . 'payment.deposit', $data);
    }

    public function reportar(Request $request){


                $this->validate($request, [
                    'hash'        => 'required',
                    'method_code' => 'required',
                    'currency'    => 'required',
                    'amount'      => 'required',
                    'red'         => 'required'
                ]); 

                $vali = Deposit::where('id_tx', $request->hash)->count();
                if($vali > 0 )
                {
                    $notify[] = ['error', 'El hash ya se encuentra registrado!'];
                    return back()->withNotify($notify);
                }
            
                $currency = $request->currency;
                $method_code  = $request->method_code;

                
                $gate = GatewayCurrency::where('method_code', $method_code)->where('currency', $currency)->first();
                if (!$gate) {
                    $notify[] = ['error', 'Invalid Gateway'];
                     return back()->withNotify($notify);
                }
              

                $charge       = 0;
                $withCharge   = $request->amount + $charge;
                $final_amo    = $withCharge;

                $depo = array();
                $depo['user_id']          = Auth::id();
                $depo['method_code']      = $gate->method_code;
                $depo['method_currency']  = strtoupper($gate->currency);
                $depo['amount']           = formatter_money($request->amount);
                $depo['charge']           = 0;
                $depo['rate']             = $gate->rate;
                $depo['final_amo']        = 0;
                $depo['btc_amo']          = 0;
                $depo['btc_wallet']       = "";
                $depo['red']              = $request->red;
                $depo['trx']              = getTrx();
                $depo['id_tx']            = $request->hash;
                $depo['try']              = 0;
                $depo['status']           = 0;

                $track =  Deposit::create($depo);
                  
              
                $notify[] = ['success', 'Transacción recibida'];
                return back()->withNotify($notify);
                

    }

    public function moneda(Request $request){
        $valor = $request->moneda;
        switch($valor)
        {
            case 1: $curre = "ETH";  break;
            case 2: $curre = "TRX";  break;
            case 3: $curre = "USDT"; break;
            case 4: $curre = "USDT"; break;
            default: $curre = "ETH";
        }

        $gatewayCurrency = GatewayCurrency::where('currency',$curre)->whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->first();
        return json_decode($gatewayCurrency, true);
    }

    public function depositInsert(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric',
            'method_code' => 'required',
            'currency' => 'required',
        ]); 
    
        $currency = $request->currency;
        $method_code  = $request->method_code;

        
        $gate = GatewayCurrency::where('method_code', $method_code)->where('currency', $currency)->first();
        
        if (!$gate) {
            $notify[] = ['error', 'Invalid Gateway'];
            return back()->withNotify($notify);
        }
        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please Follow Deposit Limit'];
            return back()->withNotify($notify);
        }

        $charge = formatter_money($gate->fixed_charge + ($request->amount * $gate->percent_charge / 100));
        $withCharge = $request->amount + $charge;
        $final_amo = formatter_money($withCharge * $gate->rate);

        $depo = array();
        $depo['user_id'] = Auth::id();
        $depo['method_code'] = $gate->method_code;
        $depo['method_currency'] = strtoupper($gate->currency);
        $depo['amount'] = formatter_money($request->amount);
        $depo['charge'] = $charge;
        $depo['rate'] = $gate->rate;
        $depo['final_amo'] = $final_amo;
        $depo['btc_amo'] = 0;
        $depo['btc_wallet'] = "";
        $depo['trx'] = getTrx();
        $depo['try'] = 0;
        $depo['status'] = 0;
        $track =  Deposit::create($depo);
        Session::put('Track', $track->trx); 
        return redirect()->route('user.deposit.confirm');
    }

    function deposit_troneth(request $request){
        session_start();
        $request->amount;
         
        $user = Auth::id();

        $monto_eth = $_SESSION['eth_monto'];
        $monto_trx = $_SESSION['trx_monto'];
      
        $gate = GatewayCurrency::where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        
        if (!$gate) {
            $notify[] = ['error', 'Invalid Gateway'];
            return back()->withNotify($notify);
        }
       
        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please Follow Deposit Limit'];
            return back()->withNotify($notify);
        }
       
        $charge = formatter_money($gate->fixed_charge + ($request->amount * $gate->percent_charge / 100));
        $withCharge = $request->amount + $charge;
        $final_amo = formatter_money($withCharge * $gate->rate);     
        
        $trx_n= getTrx();
        $depo = array();
        $depo['user_id'] = Auth::id();
        $depo['method_code'] = $gate->method_code;
        $depo['method_currency'] = strtoupper($gate->currency);
        $depo['amount'] = formatter_money($request->amount);
        $depo['charge'] = $charge;
        $depo['rate'] = $gate->rate;
        $depo['final_amo'] = $final_amo;
        $depo['btc_amo'] = 0;
        $depo['btc_wallet'] = "";
        $depo['trx'] = $trx_n;
        $depo['id_tx'] = $request->id_tx;
        $depo['try'] = 0;
        $depo['status'] = 0;
        $deposit =  Deposit::create($depo);
        
        
        $notify[] = ['success', 'DEPOSIT SECCESS'];
        return redirect()->route('user.deposit')->withNotify($notify);
    }
    

    public function depositFilter(Request $request){
           session_start();
           $_SESSION['eth_monto']  = $request->monto;
           $eth = $request->monto;
           $tron = $this->eth_tron($eth);
           $_SESSION['trx_monto'] = $tron;
           return $tron;
    }
    


    public function depositConfirm()
    {
        $track = Session::get('Track');
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        
        if (is_null($deposit)) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route('user.wallet')->withNotify($notify);
        }

        if ($deposit->status != 0) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route('user.wallet')->withNotify($notify);
        }

        if ($deposit->method_code >= 1000) {
            return redirect()->route('user.manualDeposit.confirm');
        }

        $xx = 'g' . $deposit->method_code;
        $new = __NAMESPACE__ . '\\' . $xx . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return redirect()->route('user.deposit')->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }
        
        $datos['user'] = Auth()->user();
        $datos['data'] = $data;
        $datos['deposit'] = $deposit;
        $datos['page_title'] = 'Payment Information';
      
        return view(activeTemplate() . $data->view, $datos);
    }

    public static function userDataUpdate($trx)
    {

        $precio = 1;
        $data = Deposit::where('trx', $trx)->first();

        if ($data->status == 0) {
            $data['status'] = 1;
            $data->price_dolar = $precio;
            $data->update();

         $dtrs = Trx::where('trx', $data->trx)->first();

         if($dtrs == null)
         {
            $user = User::find($data->user_id);
            if($data->status == 0){
                $user->active_status = 1;
            }else{
                $user->balance_btc += $data->amount;
            }

            $user->save();
            $gateway = $data->gateway;

            Trx::create([
                'user_id' => $data->user_id,
                'amount_con' => $data->amount,
                'main_amo_con' => $data->amount + $data->charge,
                'charge_con' => $data->charge,
                'type'   => 'deposit',
                'moneda' => 4,
                'price_dolar' => $precio,
                'title'  => 'Deposit Via ' . $gateway->name,
                'trx'    => $data->trx,
                'balance' => $user->balance_btc,
            ]);
            //realizamos el swap para  depositar
              
            $pasar = ($data->amount  * 2) / 100;
            $pasar = number_format($pasar,8, '.','');

            $ustd = $pasar * $precio;
            $user->balance_usdt += $usdt;
            $user->balance_btc -= $data->amount;
            $user->save();

            Trx::create([
                'user_id' => $data->user_id,
                'amount_con' => $pasar,
                'main_amo_con' => $pasar,
                'type'   => 'deposit',
                'moneda' => 4,
                'price_dolar' => 1,
                'title'  => 'Swap btc to usdt',
                'trx'    => $data->trx,
                'balance' => $user->balance_usdt,
            ]);

            $general = GeneralSetting::first(['cur_sym']);
            $amount = 'BTC ' . formatter_money($data->amount, $gateway->crypto());

            /*   
               send_email($user, 'DEPOSIT_SUCCESS', [
                     'amount' => $amount,
                     'method' => $gateway->name,
               ]);
               send_sms($user, 'DEPOSIT_SUCCESS', [
                    'amount' => $amount,
                    'method' => $gateway->name,
               ]);
            */

            $deposit = $data;

            send_email($user, 'DEPOSIT_APPROVE', [
                            'trx' => $deposit->trx,
                            'amount' => $general->cur_sym . formatter_money($deposit->amount),
                            'receive_amount' => $amount,
                            'charge' => $general->cur_sym . formatter_money($deposit->charge),
                            'method' => $deposit->gateway->name,
            ]);

           /* send_sms($user, 'DEPOSIT_APPROVE', [
                'trx' => $deposit->trx,
                'amount' => $general->cur_sym . formatter_money($deposit->amount),
                'receive_amount' => $amount,
                'charge' => $general->cur_sym . formatter_money($deposit->charge),
                'method' => $deposit->gateway->name,
            ]);*/
          }
        }
    }


    public function manualDepositConfirm(Request $request)
    {
        $track = Session::get('Track');
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $page_title = 'Deposit Confirm';
        return view(activeTemplate() . 'payment.manual_confirm', compact('page_title', 'data'));
    }

    public function manualDepositUpdate(Request $request)
    {


        $track = Session::get('Track');
        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        if ($data->status != 0 || !$data) {
            $notify[] = ['error', 'Invalid Deposit Request'];
            return redirect()->route('user.deposit')->withNotify($notify);
        }

        if ($request->hasFile('verify_image')) {
            try {
                $filename = upload_image($request->verify_image, config('constants.deposit.verify.path'));
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Could not upload your verification image'];
                return back()->withNotify($notify);
            }
        }

        $data->detail = $request->ud;
        $data->verify_image = $filename;
        $data->status = 2;
        $data->save();

        $general = GeneralSetting::first();


        send_email(auth()->user(), 'DEPOSIT_PENDING', [
            'trx' => $data->trx,
            'amount' => $general->cur_sym . ' ' . formatter_money($request->amount),
            'method' => $data->gateway_currency()->name,
            'charge' => $general->cur_sym . ' ' . $data->charge,
        ]);

        send_sms(auth()->user(), 'DEPOSIT_PENDING', [
            'trx' => $data->trx,
            'amount' => $general->cur_sym . ' ' . formatter_money($request->amount),
            'method' => $data->gateway_currency()->name,
            'charge' => $general->cur_sym . ' ' . $data->charge,
        ]);

        $notify[] = ['success', 'You have deposit request has been taken.'];
        return redirect()->route('user.deposit.history')->withNotify($notify);
    }
}
