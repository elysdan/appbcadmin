<?php

namespace App\Http\Controllers\Gateway\g108;

use App\Deposit;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Gateway\PaymentController;

class ProcessController extends Controller
{
    /*
     * Vogue Pay Gateway
     */

    public static function process($deposit)
    {
        $vogueAcc = json_decode($deposit->gateway_currency()->parameter);
        $send['v_merchant_id'] = $vogueAcc->merchant_id;
        $send['notify_url'] = route('ipn.g108');
        $send['cur'] = $deposit->method_currency;
        $send['merchant_ref'] = $deposit->trx;
        $send['memo'] = 'Payment';
        $send['store_id'] = $deposit->user_id;
        $send['custom'] = $deposit->trx;
        $send['Buy'] = $deposit->final_amo;
        $send['view'] = 'payment.g108';
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required'
        ]);

        $trx = $request->transaction_id;
        $req_url = "https://voguepay.com/?v_transaction_id=$trx&type=json";
        $vougueData = curlContent($req_url);
        $vougueData = json_decode($vougueData);
        $track = $vougueData->merchant_ref;

        $data = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        $vogueAcc = json_decode($data->gateway_currency()->parameter);
        if ($vougueData->status == "Approved" && $vougueData->merchant_id == $vogueAcc->merchant_id && $vougueData->total == $data->final_amo && $vougueData->cur_iso == $data->method_currency &&  $data->status == '0') {
            //Update User Data
            PaymentController::userDataUpdate($data->trx);
        }
    }
}
