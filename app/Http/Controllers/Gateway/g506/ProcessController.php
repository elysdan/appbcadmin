<?php

namespace App\Http\Controllers\Gateway\g506;

use App\Deposit;
use App\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Auth;
use Illuminate\Http\Request;
use Session;

class ProcessController extends Controller
{
    /*
     * coinbase Gateway 506
     */

    public static function process($deposit)
    {
               
        if (!$deposit->btc_amo || !$deposit->btc_wallet) {

            $basic = GeneralSetting::first();
            $coinbaseAcc = json_decode($deposit->gateway_currency()->parameter);

            $url = 'https://api.commerce.coinbase.com/charges';
            $array = [
                'name' => Auth::user()->username,
                'description' => "Pay to " . $basic->sitename,
                'local_price' => [
                    'amount' => $deposit->final_amo,
                    'currency' => 'USDT'
                ],
                'metadata' => [
                    'trx' => $deposit->trx
                ],
                'pricing_type' => "fixed_price",
                'redirect_url' => route('user.deposit'),
                'cancel_url' => route('user.deposit')
            ];
            
        
            $yourjson = json_encode($array);
            $ch = curl_init();
            $apiKey = $coinbaseAcc->api_key;
            $header = array();
            $header[] = 'Content-Type: application/json';
            $header[] = 'X-CC-Api-Key: ' . "$apiKey";
            $header[] = 'X-CC-Version: 2018-03-22';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $yourjson);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($result);


            if (@$result->error == '') {

               // if ($deposit->method_currency == 'USDT') {

                  /*  $cryptoAmo    = $result->data->pricing->usdt->amount;
                    $cryptoWallet = $result->data->addresses->usdt; */
               
              //  }else { 
                      
                    
                    
                    print_r($result->data);
                    return;
                    
                    $cryptoAmo = $result->data->pricing->tether->amount;
                    $cryptoWallet = $result->data->addresses->tether;
              //  } 

                $deposit['btc_amo']    = $cryptoAmo;
                $deposit['btc_wallet'] = $cryptoWallet;

                $deposit->update();
            } else {
                $send['error'] = true;
                $send['message'] = 'Some Problem Occured. Try Again';
            }
        }
             
        $send['amount'] = $deposit->btc_amo;
        $send['sendto'] = $deposit->btc_wallet;
        $send['img'] = cryptoQR($deposit->btc_wallet, $deposit->btc_amo);
        $send['currency'] = "$deposit->method_currency";
        $send['view'] = 'payment.crypto';
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $postdata = file_get_contents("php://input");
        $res = json_decode($postdata);
        $xx =  @$res->event->type;
        $file = fopen("75.txt", "a");
        fwrite($file, "$xx \n");
        fclose($file);

        $data = Deposit::where('trx', $res->event->data->metadata->trx)->orderBy('id', 'DESC')->first();
        $coinbaseAcc = json_decode($data->gateway_currency()->parameter);
      /*  $headers = apache_request_headers();
        $sentSign = $headers['X-Cc-Webhook-Signature'];
        $sig = hash_hmac('sha256', $postdata, $coinbaseAcc->secret);
        if ($sentSign == $sig) { */

            if ($res->event->type == 'charge:confirmed' && $data->status == 0) {
                PaymentController::userDataUpdate($data->trx);
            }

                /*  */
    //    }
    }
}
