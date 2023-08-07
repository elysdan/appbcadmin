<?php

namespace App\Http\Controllers;
use App\BvLog;
use App\Deposit;
use App\GeneralSetting;
use App\Lib\GoogleAuthenticator;
use App\MatrixPlan;
use App\MatrixSubscriber;
use App\Plan;
use App\Rules\FileTypeValidate;
use App\Trx;
use App\User;
use App\UserExtra;
use App\UserLogin;
use App\UserMatrix;
use App\Withdrawal;
use App\WithdrawMethod;
use App\Share;
use App\GatewayCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use  Auth;
use  Session;
use Illuminate\Support\Str;
use App\Lib\CoinPaymentHosted;

class UserController extends Controller
{
    public function home()
    {
        $user = Auth::user();
        $data['page_title'] = "Dashboard";
        $data['total_deposit'] = Deposit::whereUserId($user->id)->where('status', 1)->where('isact', 0)->sum('amount');

        $data['total_withdraw'] = Withdrawal::whereUserId($user->id)->whereStatus(1)->sum('amount');
        $data['complete_withdraw'] = Withdrawal::whereUserId($user->id)->whereStatus(1)->count();
        $data['pending_withdraw'] = Withdrawal::whereUserId($user->id)->whereStatus(2)->count();
        $data['reject_withdraw'] = Withdrawal::whereUserId($user->id)->whereStatus(3)->count();

        $data['ref'] = User::where('ref_id', $user->id)->count();

        $data['pool_interest'] = Trx::whereUserId($user->id)->where('type', 'pool_interest')->sum('amount');
        $data['ref_com'] = Trx::whereUserId($user->id)->where('type', 'referral_commision')->sum('amount');
        $data['binary_com'] = Trx::whereUserId($user->id)->where('type', 'binary_comission')->sum('amount');
        $data['gana_matrix'] = Trx::whereUserId($user->id)->where('type', 'matrix_commission')->sum('amount');
        $data['residual_bonus'] = Trx::whereUserId($user->id)->where('type', 'residual_bonus')->sum('amount');

        return view(activeTemplate() . 'user.dashboard', $data);
    }

    public function profile()
    {
        $page_title = 'Profile';
        return view(activeTemplate() . 'user.profile', compact('page_title'));
    }

    public function profileUpdate(Request $request)
    {


        if (!$request->oldreq) {
            $request->validate([
                'firstname' => 'required|max:160',
                'lastname' => 'required|max:160',
                'address' => 'nullable|max:160',
                'city' => 'nullable|max:160',
                'state' => 'nullable|max:160',
                'zip' => 'nullable|max:160',
                'country' => 'nullable|max:160',
                'image' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            ]);

            $filename = auth()->user()->image;
            if ($request->hasFile('image')) {
                try {
                    $path = config('constants.user.profile.path');
                    $size = config('constants.user.profile.size');
                    $filename = upload_image($request->image, $path, $size, $filename);
                } catch (\Exception $exp) {
                    $notify[] = ['success', 'Image could not be uploaded'];
                    return back()->withNotify($notify);
                }
            }


            $oldreq = $request->except('_token', '_method');
            $oldreq['filename'] = $filename;
            $oldreq = json_encode($oldreq);

            if (auth()->user()->ts) {
                $tfa['type'] = 'google';
            } else {
                $tfa['type'] = 'email';
                $code = verification_code(6);
                $sig = hash_hmac('sha256', $code, auth()->user()->email);
                $tfa['hash'] = $sig;

                send_email(auth()->user(), 'profile_2fa', [
                    'code' => $code,
                ]);
            }

            $page_title = '2 Factor Authentication';
            return view(activeTemplate() . 'user.profile-2fa', compact('page_title', 'tfa', 'oldreq'));

        } else {

            $request->validate([
                'code' => 'required',
            ]);

            if (auth()->user()->ts) {
                $ga = new GoogleAuthenticator();
                $oneCode = $ga->getCode(auth()->user()->tsc);
                $userCode = $request->code;
                if ($oneCode != $userCode) {
                    $notify[] = ['error', '2FA NOT MATCHED!!!'];
                    return redirect()->route('user.profile')->withNotify($notify);
                }
            } else {
                $sig = hash_hmac('sha256', $request->code, auth()->user()->email);
                if ($request->hash != $sig) {
                    $notify[] = ['error', 'OTP NOT MATCHED!!!'];
                    return redirect()->route('user.profile')->withNotify($notify);
                }
            }
            $mainreq = json_decode($request->oldreq);
            auth()->user()->update([
                'firstname' => $mainreq->firstname,
                'lastname' => $mainreq->lastname,
                'image' => $mainreq->filename,
                'address' => [
                    'address' => $mainreq->address,
                    'city' => $mainreq->city,
                    'state' => $mainreq->state,
                    'zip' => $mainreq->zip,
                    'country' => $mainreq->country,
                ]
            ]);
            $notify[] = ['success', 'Your profile has been updated'];
            return back()->withNotify($notify);
        }
    }


    public function passwordChange()
    {
        $page_title = 'Password Change';
        return view(activeTemplate() . 'user.password', compact('page_title'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed|max:160|min:6'
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            $notify[] = ['error', 'Your old password doesnt match'];
            return back()->withNotify($notify);
        }
        auth()->user()->update([
            'password' => bcrypt($request->password)
        ]);
        $notify[] = ['success', 'Your password has been updated'];
        return back()->withNotify($notify);
    }

    public function show2faForm()
    {
        $gnl = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $secret);
        $prevcode = $user->tsc;
        $prevqr = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $prevcode);
        $page_title = 'Google 2FA Auth';

        return view(activeTemplate() . 'user.go2fa', compact('page_title', 'secret', 'qrCodeUrl', 'prevcode', 'prevqr'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);

        $ga = new GoogleAuthenticator();
        $secret = $request->key;
        $oneCode = $ga->getCode($secret);

        if ($oneCode === $request->code) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->tv = 1;
            $user->save();
            send_email($user, '2FA_ENABLE', [
                'code' => $user->ver_code
            ]);
            send_sms($user, '2FA_ENABLE', [
                'code' => $user->ver_code
            ]);

            $notify[] = ['success', 'Google Authenticator Enabled Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['danger', 'Wrong Verification Code'];
            return back()->withNotify($notify);
        }
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;

        if ($oneCode == $userCode) {
            $user->tsc = null;
            $user->ts = 0;
            $user->tv = 1;
            $user->save();
            send_email($user, '2FA_DISABLE');
            send_sms($user, '2FA_DISABLE');
            $notify[] = ['success', 'Two Factor Authenticator Disable Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->with($notify);
        }
    }

    public function depositHistory()
    {
        $page_title = 'Deposit History';
        $empty_message = 'No history found.';
        $logs = auth()->user()->deposits()->where('status', '!=', 0)->where('isact', 0)->with(['gateway'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'payment.deposit_history', compact('page_title', 'empty_message', 'logs'));
    }

    public function withdrawHistory()
    {
        $page_title = 'Withdraw History';
        $empty_message = 'No history found.';
        $logs = auth()->user()->withdrawals()->with(['method'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.withdraw_history', compact('page_title', 'empty_message', 'logs'));
    }

    public function withdraw()
    {
        $page_title = 'Withdraw';
        $method = WithdrawMethod::first();
        return view(activeTemplate() . 'user.withdraw', compact('page_title', 'method'));
    }

    public function withdrawInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);
        $withdraw = WithdrawMethod::first();

        if ($request->amount < $withdraw->min_limit || $request->amount > $withdraw->max_limit) {
            $notify[] = ['error', 'Please follow the limit'];
            return back()->withNotify($notify);
        }
        if ($request->amount > auth()->user()->balance) {
            $notify[] = ['error', 'You do not have sufficient balance'];
            return back()->withNotify($notify);
        }
        $charge = $withdraw->fixed_charge + (($request->amount * $withdraw->percent_charge) / 100);
        $withoutCharge = $request->amount - $charge;
        $final_amo = $withoutCharge;

        $data = new Withdrawal();
        $data->method_id = $withdraw->id;
        $data->user_id = auth()->id();
        $data->amount = formatter_money($request->amount);
        $data->charge = formatter_money($charge);
        $data->rate = 1;
        $data->currency = 'ETH';
        $data->delay = 0;
        $data->final_amo = $final_amo;
        $data->status = 0;
        $data->email_code = verification_code(6);
        $data->trx = getTrx();
        $data->save();
        Session::put('Track', $data->trx);

        $general = GeneralSetting::first();

        // send_email(auth()->user(), 'WITHDRAW_VERIFY', [
        //     'amount' => $general->cur_sym . ' ' . formatter_money($data->amount),
        //     'method' => $data->method->name,
        //     'code' => $data->email_code,
        //     'charge' => $general->cur_sym . ' ' . $withdraw->charge,
        // ]);

        return redirect()->route('user.withdraw.preview');

    }

    public function withdrawPreview()
    {
        $track = Session::get('Track');
        $data = Withdrawal::where('user_id', auth()->id())->where('trx', $track)->where('status', 0)->first();
        if (!$data) {
            return redirect()->route('user.withdraw');
        }
        $page_title = "Withdraw Preview";

        return view(activeTemplate() . 'user.withdraw_preview', compact('data', 'page_title'));
    }

    public function withdrawStore(Request $request)
    {

        $track = Session::get('Track');
        $withdraw = Withdrawal::where('user_id', auth()->id())->where('trx', $track)->orderBy('id', 'DESC')->first();

        if ($withdraw->status != 0) {
            $notify[] = ['error', 'Enough :) . Please....'];
            return redirect()->route('user.withdraw')->withNotify($notify);
        }

        $ga = new GoogleAuthenticator();
        $oneCode = $ga->getCode(auth()->user()->tsc);
        $userCode = $request->code;

        if ($oneCode != $userCode) {
            $notify[] = ['error', '2FA NOT MATCHED!!!'];
            return redirect()->route('user.withdraw')->withNotify($notify);
        }


        // if ($withdraw->email_code != $request->code)
        // {
        //     $notify[] = ['error', 'Invalid email authorize code.'];
        //     return redirect()->route('user.withdraw')->withNotify($notify);
        // }


        $withdraw_method = WithdrawMethod::first();

        $balance = auth()->user()->balance - $withdraw->amount;
        auth()->user()->update([
            'balance' => formatter_money($balance),
        ]);

        $withdraw->status = 2;
        $withdraw->save();

        $trx = new Trx();
        $trx->user_id = auth()->id();
        $trx->amount = $withdraw->amount;
        $trx->charge = formatter_money($withdraw->charge);
        $trx->main_amo = formatter_money($withdraw->final_amo);
        $trx->balance = formatter_money(auth()->user()->balance);
        $trx->type = 'withdraw';
        $trx->trx = $withdraw->trx;
        $trx->title = 'withdraw Via ' . $withdraw->method->name;
        $trx->save();


////////////////////////AUTOMATED
        if ($withdraw_method->status) {

            $cps = new CoinPaymentHosted();
            $cps->Setup($withdraw_method->val2, $withdraw_method->val1);
            $result = $cps->CreateWithdrawal($withdraw->final_amo, 'ETH', auth()->user()->etherium_wallet_code, '1');


            if ($result['error'] == 'ok') {

                $withdraw->status = 1;
                $withdraw->save();

                $general = GeneralSetting::first();
                send_email(auth()->user(), 'WITHDRAW_APPROVE', [
                    'trx' => $withdraw->trx,
                    'amount' => $general->cur_sym . formatter_money($withdraw->amount),
                    'receive_amount' => $general->cur_sym . formatter_money($withdraw->amount - $withdraw->charge),
                    'charge' => $general->cur_sym . formatter_money($withdraw->charge),
                    'method' => $withdraw->method->name,
                ]);

                send_sms(auth()->user(), 'WITHDRAW_APPROVE', [
                    'trx' => $withdraw->trx,
                    'amount' => $general->cur_sym . formatter_money($withdraw->amount),
                    'receive_amount' => $general->cur_sym . formatter_money($withdraw->amount - $withdraw->charge),
                    'charge' => $general->cur_sym . formatter_money($withdraw->charge),
                    'method' => $withdraw->method->name,
                ]);


                $notify[] = ['success', 'Withdraw Completed Successfully!'];
                return redirect()->route('user.home')->withNotify($notify);

            }
        }

////////////////////////AUTOMATED


        $general = GeneralSetting::first();
        send_email(auth()->user(), 'WITHDRAW_PENDING', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . ' ' . formatter_money($withdraw->amount),
            'method' => $withdraw->method->name,
            'charge' => $general->cur_sym . ' ' . $withdraw->charge,
        ]);
        send_sms(auth()->user(), 'WITHDRAW_PENDING', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . ' ' . formatter_money($withdraw->amount),
            'method' => $withdraw->method->name,
            'charge' => $general->cur_sym . ' ' . $withdraw->charge,
        ]);


        $notify[] = ['success', 'You withdraw request has been taken.'];
        return redirect()->route('user.home')->withNotify($notify);
    }


    public function transactions()
    {
        $page_title = 'Transactions';
        $logs = auth()->user()->transactions()->orderBy('id', 'DESC')->paginate(config('constants.table.default'));
        $empty_message = 'No transaction history';
        return view(activeTemplate() . 'user.transactions', compact('page_title', 'logs', 'empty_message'));
    }




    function loginHistory()
    {
        $data['page_title'] = "Login History";
        $data['history'] = UserLogin::where('user_id', Auth::id())->latest()->paginate(15);
        return view(activeTemplate() . '.user.login_history', $data);
    }


    function indexTransfer()
    {
        $page_title = 'Balance Transfer';
        return view(activeTemplate() . '.user.balance_transfer', compact('page_title'));
    }

    function balanceTransfer(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        $gnl = GeneralSetting::first();
        $user = User::find(auth()->id());
        $trans_user = User::where('username', $request->username)->orwhere('email', $request->username)->first();
        if ($trans_user == '') {
            $notify[] = ['error', 'Username Not Found'];
            return back()->withNotify($notify);
        }

        if ($trans_user->username == $user->username) {

            $notify[] = ['error', 'Balance Transfer Not Possible In Your Own Account'];
            return back()->withNotify($notify);

        }

        $charge = $gnl->bal_trans_fixed_charge + ($request->amount * $gnl->bal_trans_per_charge) / 100;
        $amount = $request->amount + $charge;
        if ($user->balance >= $amount) {

            $new_balance = $user->balance - $amount;
            $user->balance = $new_balance;
            $user->save();

            $trx = getTrx();

            Trx::create([
                'trx' => $trx,
                'user_id' => $user->id,
                'type' => 'balance_transfer',
                'title' => 'Balance Transferred To ' . $trans_user->username,
                'amount' => $request->amount,
                'main_amo' => $amount,
                'balance' => $user->balance,
                'charge' => $charge
            ]);


            send_email($user, 'BAL_SEND', [

                'amount' => formatter_money($request->amount) . '' . $gnl->cur_text,
                'name' => $trans_user->username,
                'charge' => formatter_money($charge) . ' ' . $gnl->cur_text,
                'balance_now' => formatter_money($new_balance) . ' ' . $gnl->cur_text,

            ]);

            send_sms($user, 'BAL_SEND', [
                'amount' => formatter_money($request->amount) . '' . $gnl->cur_text,
                'name' => $trans_user->username,
                'charge' => formatter_money($charge) . ' ' . $gnl->cur_text,
                'balance_now' => formatter_money($new_balance) . ' ' . $gnl->cur_text,
            ]);


            $trans_new_bal = $trans_user->balance + $request->amount;
            $trans_user->balance = $trans_new_bal;
            $trans_user->save();

            Trx::create([
                'trx' => $trx,
                'user_id' => $trans_user->id,
                'type' => 'balance_transfer',
                'title' => 'Balance receive From ' . $user->username,
                'amount' => $request->amount,
                'main_amo' => $request->amount,
                'balance' => $trans_new_bal,
                'charge' => 0
            ]);


            send_email($trans_user, 'bal_receive', [

                'amount' => formatter_money($request->amount) . '' . $gnl->cur_text,
                'name' => $user->username,
                'charge' => 0 . ' ' . $gnl->cur_text,
                'balance_now' => formatter_money($trans_new_bal) . ' ' . $gnl->cur_text,

            ]);

            send_sms($trans_user, 'bal_receive', [
                'amount' => formatter_money($request->amount) . '' . $gnl->cur_text,
                'name' => $user->username,
                'charge' => 0 . ' ' . $gnl->cur_text,
                'balance_now' => formatter_money($trans_new_bal) . ' ' . $gnl->cur_text,

            ]);

            $notify[] = ['success', 'Balance Transferred Successfully.'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Insufficient Balance.'];
            return back()->withNotify($notify);

        }
    }


    function searchUser(Request $request)
    {
        $trans_user = User::where('id', '!=', Auth::id())->where('username', $request->username)
            ->orwhere('email', $request->username)->count();
        if ($trans_user == 1) {
            return response()->json(['success' => true, 'message' => 'Correct User']);
        } else {
            return response()->json(['success' => false, 'message' => 'User Not Found']);
        }

    }


      public function earnings()
    {
        $page_title = 'Earnings';
        $logs = auth()->user()->transactions()->where('type', ['referral_commision', 'binary_comission', 'residual_bonus', 'pool_interest'])->orderBy('id', 'DESC')->paginate(config('constants.table.default'));
        $empty_message = 'No transaction history';
        return view(activeTemplate() . 'user.transactions', compact('page_title', 'logs', 'empty_message'));
    }



    public function referralCom()
    {
        $data['page_title'] = "Referral Commission Log";
        $data['logs'] = Trx::where('user_id', auth()->id())->where('type', 'referral_commision')->latest()->paginate(config('constants.table.default'));
        $data['empty_message'] = 'No data found';
        return view(activeTemplate() . '.user.referralCom', $data);
    }

    public function binaryCom()
    {
        $data['page_title'] = "Binary Commission Log";
        $data['logs'] = Trx::where('user_id', auth()->id())->where('type', 'binary_comission')->latest()->paginate(config('constants.table.default'));
        $data['empty_message'] = 'No data found';
        return view(activeTemplate() . '.user.binaryCom', $data);
    }

    public function residualCom()
    {
        $data['page_title'] = "Residual Commission Log";
        $data['logs'] = Trx::where('user_id', auth()->id())->where('type', 'residual_bonus')->latest()->paginate(config('constants.table.default'));
        $data['empty_message'] = 'No data found';
        return view(activeTemplate() . '.user.binaryCom', $data);
    }

    public function interestLog()
    {
        $data['page_title'] = "Interest Log";
        $data['logs'] = Trx::where('user_id', auth()->id())->where('type', 'balance_transfer')->latest()->paginate(config('constants.table.default'));
        $data['empty_message'] = 'No data found';
        return view(activeTemplate() . '.user.transactions', $data);
    }

    public function transferLog()
    {
        $data['page_title'] = "Pool Interest";
        $data['logs'] = Trx::where('user_id', auth()->id())->where('type', 'pool_interest')->latest()->paginate(config('constants.table.default'));
        $data['empty_message'] = 'No data found';
        return view(activeTemplate() . '.user.transactions', $data);
    }
    public function matrixLog()
    {
        $data['page_title'] = "Matrix Commission";
        $data['logs'] = Trx::where('user_id', auth()->id())->where('type', 'matrix_commission')->latest()->paginate(config('constants.table.default'));
        $data['empty_message'] = 'No data found';
        return view(activeTemplate() . '.user.transactions', $data);
    }

    public function binarySummery()
    {
        $data['page_title'] = "Binary Summery";
        $data['logs'] = UserExtra::where('user_id', auth()->id())->first();
        return view(activeTemplate() . '.user.binary_summery', $data);
    }

    public function bvlog()
    {
        $data['page_title'] = "BV LOG";
        $data['logs'] = BvLog::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(config('constants.table.default'));
        $data['empty_message'] = 'No data found';
        return view(activeTemplate() . '.user.bv_log', $data);
    }

    public function myRefLog()
    {
        $data['page_title'] = "My Referrals";
        $data['empty_message'] = 'No data found';
        $data['logs'] = User::where('ref_id', auth()->id())->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.my_ref', $data);
    }

    public function myTree()
    {
        $data['tree'] = showTreePage(Auth::id());
        $data['page_title'] = "My Tree";
        return view(activeTemplate() . 'user.my_tree', $data);
    }


    public function otherTree(Request $request, $username = null)
    {
        if ($request->username) {
            $user = User::where('username', $request->username)->first();
        } else {
            $user = User::where('username', $username)->first();
        }
        if ($user && treeAuth($user->id, auth()->id())) {
            $data['tree'] = showTreePage($user->id);
            $data['page_title'] = "Tree of " . $user->fullname;
            return view(activeTemplate() . 'user.my_tree', $data);
        }

        $notify[] = ['error', 'Tree Not Found or You do not have Permission to view that!!'];
        return redirect()->route('user.my.tree')->withNotify($notify);


    }


    public function accountActive()
    {

        $gnl = GeneralSetting::first(['active_charge', 'investor_active_charge']);
        if (auth()->user()->account_type == 1) {
            $charge = $gnl->active_charge;
        } else {
            $charge = $gnl->investor_active_charge;
        }
        $charge_in_eth = eth_rate() * $charge;
        if ($charge_in_eth > auth()->user()->balance) {
            $gate = GatewayCurrency::where('method_code', 506)->where('currency', 'ETH')->first();
            if (!$gate) {
                $notify[] = ['error', 'Invalid Gateway'];
                return back()->withNotify($notify);
            }
            $charge = formatter_money($gate->fixed_charge + ($charge_in_eth * $gate->percent_charge / 100));
            $withCharge = $charge_in_eth + $charge;
            $final_amo = formatter_money($withCharge * $gate->rate);

            $depo['user_id'] = Auth::id();
            $depo['method_code'] = $gate->method_code;
            $depo['method_currency'] = strtoupper($gate->currency);
            $depo['amount'] = formatter_money($charge_in_eth);
            $depo['charge'] = $charge;
            $depo['rate'] = $gate->rate;
            $depo['final_amo'] = $final_amo;
            $depo['btc_amo'] = 0;
            $depo['btc_wallet'] = "";
            $depo['trx'] = getTrx();
            $depo['try'] = 0;
            $depo['status'] = 0;
            $depo['isact'] = 1;
            $track = Deposit::create($depo);

            Session::put('Track', $track->trx);
            return redirect()->route('user.deposit.confirm');


        }

        if (auth()->user()->active_status == 0) {
            $user = auth()->user();
            $user->balance -= $charge_in_eth;
            $user->active_status = 1;
            $user->save();

            send_email($user, 'account_active', [

                'active_time' => show_datetime($user->updated_at),
            ]);
            send_sms($user, 'account_active', [
                'active_time' => show_datetime($user->updated_at),
            ]);


            $notify[] = ['success', 'Account activated successfully '];
            return back()->withNotify($notify);
        }

        $notify[] = ['error', 'Invalid Request'];
        return redirect()->route('user.deposit')->withNotify($notify);


    }

    public function accountActiveBTC()
    {

        $gnl = GeneralSetting::first(['active_charge', 'investor_active_charge']);
        if (auth()->user()->account_type == 1) {
            $amount = $gnl->active_charge;
        } else {
            $amount = $gnl->investor_active_charge;
        }
        $ethAmo = eth_rate() * $amount;



        $gate = GatewayCurrency::where('method_code', 506)->where('currency', 'ETH')->first();

        $depo['user_id'] = Auth::id();
        $depo['method_code'] = $gate->method_code;
        $depo['method_currency'] = 'BTC';
        $depo['amount'] = formatter_money($ethAmo);
        $depo['charge'] = 0;
        $depo['rate'] = $gate->rate;
        $depo['final_amo'] = $ethAmo;
        $depo['btc_amo'] = 0;
        $depo['btc_wallet'] = "";
        $depo['trx'] = getTrx();
        $depo['try'] = 0;
        $depo['status'] = 0;
        $depo['isact'] = 1;
        $track = Deposit::create($depo);
        Session::put('Track', $track->trx);
        return redirect()->route('user.deposit.confirm');
    }

   public function accelerationMatrix_fill($pl = "", $ma= ""){
       
            $id_plan = 1;
            $id_matrix = "";
            $data['plan_matrix'] = $pl;
            $data['page_title'] = 'Acceleration Matrix';
            $data['plans'] = MatrixPlan::where('status', 1)->orderBy('id')->get();
             
            if($ma==0)
                $data['matrix_ini'] = MatrixSubscriber::where('user_id', auth()->user()->id)->
                where('matrix_plan_id',$pl)->first();
            else
              $data['matrix_ini'] = MatrixSubscriber::where('user_id', auth()->user()->id)->
                                                    where('id', $ma)->
                                                    where('matrix_plan_id',$pl)->first();
        
            if($data['matrix_ini'] != null)
            {
                $id_matrix = $data['matrix_ini']->id;
                $id_plan   = $data['matrix_ini']->matrix_plan_id;
            }
           
            $data['lista_matrixes']  = MatrixSubscriber::where('user_id',auth()->user()->id)->where("matrix_plan_id", $id_plan)->get();
          
            $data['user_matrix'] = UserMatrix::where('id_matrix',$id_matrix)->get();

            $poci = array();
            $los_planes = array();
            if( $data['plans'] != null)
                {
                    foreach($data['plans'] as $valor){     
                        $cantidad = MatrixSubscriber::where('user_id', auth()->user()->id)
                                    ->where('matrix_plan_id',$valor->id)->count();
                                        $los_planes[] = array(
                                                                'id_plan'=>$valor->id,
                                                                'cantidad'=>$cantidad,
                                                                'precio'=>number_format($valor->price,2)
                                                            );
                    }
                }
        
            $poci = $this->data_pocisiones($data['user_matrix']);
       
                if( $data['user_matrix'] != null)
                {
                    $i = 1;
                        foreach($data['lista_matrixes'] as $valor)
                        {
                            $datos_matriz[] = array("posi"=>$i,
                                                "id_matrix"=>$valor->id);
                            $i++;
                        }
                }  

            $data['lis_mat'] = $datos_matriz;
            $data['poci'] = ($poci);
            $data['los_plan'] = $los_planes;
            $data['imagen_ini'] = $this->imagen_ma(auth()->user()->id);
            return view(activeTemplate() . 'user.user_matrix', $data);
   }

    public function accelerationMatrix()
    {
        $id_plan = 1;
        $id_matrix = "";
        $data['page_title'] = 'Acceleration Matrix';
        $data['plans'] = MatrixPlan::where('status', 1)->orderBy('id')->get();
        $data['matrix_ini'] = MatrixSubscriber::where('user_id', auth()->user()->id)->orderBy('matrix_plan_id')->first();
      
        if($data['matrix_ini'] != null)
        {
            $id_matrix = $data['matrix_ini']->id;
            $id_plan   = $data['matrix_ini']->matrix_plan_id;
        }

        $data['lista_matrixes']  = MatrixSubscriber::where('user_id',auth()->user()->id)->where("matrix_plan_id", $id_plan)->get();
        $data['user_matrix'] = UserMatrix::where('id_matrix',$id_matrix)->get();
   
        $poci = array();
        $los_planes = array();
        if( $data['plans'] != null)
            {
                  foreach($data['plans'] as $valor){     
                    $cantidad = MatrixSubscriber::where('user_id', auth()->user()->id)
                                ->where('matrix_plan_id',$valor->id)->count();
                                    $los_planes[] = array(
                                                            'id_plan'=>$valor->id,
                                                            'cantidad'=>$cantidad,
                                                            'precio'=>number_format($valor->price,2)
                                                        );
                  }
            }
      
           $poci = $this->data_pocisiones($data['user_matrix']);
        

            $datos_matriz = array();
            if( $data['user_matrix'] != null)
            {
                $i = 1;
                    foreach($data['lista_matrixes'] as $valor)
                    {
                        $datos_matriz[] = array("posi"=>$i,
                                              "id_matrix"=>$valor->id);
                        $i++;
                    }
            }  
         $data['lis_mat'] = $datos_matriz;
         $data['poci'] = ($poci);
         $data['los_plan'] = $los_planes;
         $data['plan_matrix'] = $id_plan;
         $data['imagen_ini'] = $this->imagen_ma(auth()->user()->id);
        return view(activeTemplate() . 'user.user_matrix', $data);
    }
    
    public function data_pocisiones($valores){
        for($i=0;$i<=6; $i++) { 
             $poci[] = array('id_user'=>'',
                              'id_matrix'=>'',
                              'imagen'=>'',
                              'username'=>''
                              ); 
            }


        if( $valores != null)
                {
                        foreach($valores as $valor){
                              
                            $usua = User::where('id',$valor->user_id)->first();
                          
                            $poci[$valor->position]['id_user'] = $valor->user_id;
                            $poci[$valor->position]['id_matrix'] = $valor->viene;
                            $poci[$valor->position]['imagen'] = $this->imagen_ma($valor->user_id); 
                            $poci[$valor->position]['username'] = $usua->username;                            
                        }
                } 
                return $poci;      
    }


    public function imagen_ma($user){
        
        $data = User::where("id",$user)->first();
        $imagen = $data->imagen;
        if($imagen=="") $imagen = "../../assets/images/default.png";
        else            $imagen = "../../asstes/images/use/profile".$imagen; 
        return $imagen;
    }



    public function accelerationMatrixPost(Request $request, $id)
    {
        $data = MatrixPlan::where('id', $id)->where('status', 1)->first();
        if ($data == null)
        {
            $notify[] = ['error', 'Invalid Request'];
             return back()->withNotify($notify);
        }

        $user = auth()->user();

        if ($user->balance < $data->price)
        {
            $notify[] = ['error', 'Your do not have Sufficient Balance.'];
            return back()->withNotify($notify);
        }
        
        $ma = MatrixSubscriber::where('user_id',$user->id)->where('matrix_plan_id', $data->id)->where('status',1)->first();
     
        if($ma != null)
        {
            $notify[] = ['error', 'You Already Subscribed.'];
            return back()->withNotify($notify);
        }

        $refer = $this->buscar_activo($user->id, $data->id);
        $id_ref    = $refer['id_ref'];
        $id_matrix = $refer['id_matrix']; 
        
        if($refer['res'] == "ok")   $id_ref = 0; 

        $matrix = new MatrixSubscriber();
        $matrix->user_id = $user->id;
        $matrix->matrix_plan_id = $data->id;
        $matrix->amount = $data->price;
        $matrix->viene  = $id_ref;
        $matrix->total += 1;
        $matrix->status = 1;
        //$matrix->save();
        $matriz_gen = 0;
        //$matriz_gen = $matrix->id;
        $actual = $user->balance;
        $user->balance -= $data->price;
        //$user->save();
        if($refer['res'] == "ok")    $this->ubica_pocision($user,$id_matrix, $data, $matriz_gen);  
        return "<hr>"; 
        $notify[] = ['success', 'Invest Successfully.'];
        return back()->withNotify($notify);
    }


    public function accelerationMatrixUpdate(Request $request, $id)
    {
        $data = MatrixPlan::where('id', $id)->where('status', 1)->first();
        if ($data == null)
        {
            $notify[] = ['error', 'Invalid Request'];
            return back()->withNotify($notify);
        }

        $user = auth()->user();
        if (!userMatrixCheckStatus($user->id))
        {
             $notify[] = ['error', 'You Already Subscribed.'];
             return back()->withNotify($notify);
        }

        $getMatrix = getMatrixPosition($data->id);

        $matrix = MatrixSubscriber::where('user_id', $user->id)->first();
        $matrix->total += 1;
        $matrix->status = 1;
        $matrix->save();

        $matrix = new UserMatrix();
        $matrix->user_id = $user->id;
        $matrix->pos_id = $getMatrix['pos_id'];
        $matrix->position = $getMatrix['position'];
        $matrix->matrix_plan_id = $data->id;
        $matrix->save();

        $details = $user->username . ' Re-Subscribe Acceleration Matrix ' . $data->name;
     
        $notify[] = ['success', 'Invest Successfully.'];
        return back()->withNotify($notify);
    }

    function buscar_activo($id_user, $paquete = ""){
          $data['res'] = "error";
          $data['id_ref'] = "0";
          $data['id_matrix'] = "";
          $users = User::find($id_user);
          $id_buscar = $users->ref_id;
  
        for($i= 0; $i< 30; $i++)
        {
            if($id_buscar == 0 or $id_buscar == "")  return $data;
      
            $mat = MatrixSubscriber::where('user_id',$id_buscar)->where('matrix_plan_id', $paquete)->where('status',1)->first();
            if($mat != null)
                {
                    $data['res']       = 'ok';
                    $data['id_ref']    = $id_buscar;
                    $data['id_matrix'] = $mat->id;
                    return $data;
                }
         
            $users = User::find($id_buscar);
            $id_buscar = $users->ref_id;
        }
        return $data;
    }

    function ubica_pocision($user, $id_matriz, $data, $viene){
       
         $total_posi = UserMatrix::where('id_matrix', $id_matriz)->count();
         
         if($total_posi < 6)
         {
            $lista_dis = $this->pos_disponibles($id_matriz);
            
            $pocision = $lista_dis[0];

            $pos_id = $this->nivel($pocision);
            $matrix = new UserMatrix();
            $matrix->user_id = $user->id;
            $matrix->pos_id = $pos_id;
            $matrix->position = $pocision;
            $matrix->matrix_plan_id = $data->id;
            $matrix->id_matrix = $id_matriz;  
            $matrix->viene = $viene;
          //  $matrix->save();
          //  $this->comision_matrix($user, $data, $pos_id, $id_matriz);
            
            

          echo $pos_id."<br>";
       
            if($pos_id  == 1)
             {  
                  
                     $resu =  $this->matriz_superior($id_matriz);

                      print_r($resu);
                     return;
                     if($resu['res'] == 'ok')
                     {
                        $otro_ma = $resu['id_matrix'];
                        $poci    = $resu['poci'];
                        if($poci == 2) $poci_f = 5;
                        if($poci == 1) $poci_f = 3;
                        else $poci_f =""; 
                        $this->generando_otra_poci($user, $otro_ma, $data, $viene, $poci_f);
                     }
             }
            else {
                    $resu = $this->matriz_primaria($id_matriz,$data->id, $pocision); 
                    print_r($resu);
                    return;  
                    if($resu['res'] == 'ok'){
                    $otro_ma = $resu['id_matrix'];
                    $this->generando_otra_poci($user, $otro_ma, $data, $viene);
                  }
            } 
                 
            if($total_posi >= 5)
            {
                $con = MatrixSubscriber::find($id_matriz);
                $con->status = 2;
                $con->save();
                 $id_user = $con->user_id; 
                 $user = User::find($id_user);
                 $this->ciclar_matrix($user,$data);
            } 
        
         }
         return "error";

    }
    

    function generando_otra_poci($user, $id_matriz, $data, $viene, $poci = ''){
        $total_posi = UserMatrix::where('id_matrix', $id_matriz)->count();
             
          return "es aqui donde vamos a trabajar";


        if($total_posi < 6)
        {
            if($poci == "")  $lista_dis = $this->pos_disponibles($id_matriz);
            else             $lista_dis = $this->pos_disponibles_2($id_matriz, $poci);
         
           $pocision = $lista_dis[0];
           $pos_id = $this->nivel($pocision);
           $matrix = new UserMatrix();
           $matrix->user_id = $user->id;
           $matrix->pos_id = $pos_id;
           $matrix->position = $pocision;
           $matrix->matrix_plan_id = $data->id;
           $matrix->id_matrix = $id_matriz;  
           $matrix->viene = $viene;
           //$matrix->save();
          // $this->comision_matrix($user, $data, $pos_id, $id_matriz);

           if($total_posi >= 5)
           {
               $con = MatrixSubscriber::find($id_matriz);
               $con->status = 2;
               $con->save();
               $id_user = $con->user_id; 
               $user = User::find($id_user);
             //  $this->ciclar_matrix($user,$data);
           }

        }
    }


    function matrix_pocision($pocision_generada){
        switch($pocision_generada){
            case 3 : $res = 1; break;
            case 4 : $res = 1; break;
            case 5 : $res = 2; break;
            case 6 : $res = 2; break;
            default : $res = "";
        }
        return $res;
    }

    function matriz_primaria($id_matrix, $paquete, $pocision){

            $find_poci = $this->matrix_pocision($pocision);
            $data['res']       = 'error';
            $data['id_matrix'] = '';
            $mat = UserMatrix::where('id_matrix',$id_matrix)->where('position', $find_poci)->first();
            if($mat != null)
            {
                $data['res'] = 'ok';
                $data['id_matrix'] = $mat->viene; 
            }
            return $data;
    }

    function matriz_superior($id_matrix){
        $data['res']       = 'error';
        $data['id_matrix'] = '';
        $data['poci'] = '';
        $mat = UserMatrix::where('viene',$id_matrix)->orderBy('position','asc')->first();
        if($mat != null)
        {
            $data['res'] = 'ok';
            $data['id_matrix'] = $mat->id_matrix; 
            $data['poci']  = $mat->position;
        }
        return $data;
    
    }

     
    function localiza_matriz_primaria($id_matriz, $lider){
         
        $query = "select *from pocisiones where id_user = '".$lider."' and 
                     matriz_viene = '".$id_matriz."' and nro_poicision > 6";
        $res = $this->con_direct($query);
         
        $id_matriz = $res[0]['id_matriz'];
        $this->principal_m = $id_matriz;
        return $id_matriz;
    }
    

    function nivel($poci){
        switch($poci){
              case 1: $res = 1; break;
              case 2: $res = 1; break;
              default: $res = 2;
        }
        return $res;
    }

    function ciclar_matrix($user, $data){
        
        $refer = $this->buscar_activo($user->id, $data->id);
        $id_ref    = $refer['id_ref'];
        $id_matrix = $refer['id_matrix'];
        if($refer['res'] == "ok")   $id_ref = 0; 
       
        $matrix = new MatrixSubscriber();
        $matrix->user_id = $user->id;
        $matrix->matrix_plan_id = $data->id;
        $matrix->amount = $data->price;
        $matrix->viene  = $id_ref;
        $matrix->total += 1;
        $matrix->status = 1;
        $matrix->save();
        $matriz_gen = $matrix->id;
        $user->balance -= $data->price; 
        $user->save();
          
        if($refer['res'] == "ok")    $this->ubica_pocision($user,$id_matrix, $data, $matriz_gen);    
      
    }

    function  pos_disponibles_2($id_matrix, $poci){
        $lis_poci = $this->las_pocisiones($id_matrix);
        $i=0;

        foreach($lis_poci as  $key => $value)
        {
            if($value=="")
            { 
               if($key >= $poci)  
                $data[] = $key ;
            }
            $i++; 
        }
 
        return $data;   
    }

    function  pos_disponibles($id_matrix){
        $lis_poci = $this->las_pocisiones($id_matrix);
        $i=0;
        foreach($lis_poci as  $key => $value) {
            if($value=="") $data[] = $key ;
            $i++; 
        }
        return $data;
    }

    function  las_pocisiones($id_matrix){
                $resu = UserMatrix::where('id_matrix', $id_matrix)->get();
                $data = array();
                        for($i=1; $i<=6;$i++){
                            $data[$i] = "";
                        }

                        if($resu != null)
                        {
                            foreach ($resu as $key => $value) {
                                $poci =  $value['position'];
                                $data[$poci] = 1;
                            }
                        }
            return $data;
     }
     
     function trx_compra($user,$data, $actual){
            $trxnum = getTrx();
            $trx = new Trx();
            $trx->user_id = $user->id;
            $trx->amount = $data->price;
            $trx->charge = 0;
            $trx->main_amo = formatter_money($data->price);
            $trx->balance = formatter_money($user->balance);
            $trx->type = 'Buy_Matrix';
            $trx->trx = $trxnum;
            $trx->title = 'Matrix reserve';
            $trx->save();
     }

     function comision_matrix($user, $data, $id_pos, $matrix = ""){
         $details = $user->username . ' Re-Subscribe Acceleration Matrix ' . $data->name;
         $dat_matr = MatrixSubscriber::where('id',$matrix)->first(); 
         $user_r = User::where('id', $dat_matr->user_id)->first();
         comision_final($user_r, $data,$id_pos,$details);

        return;
     }

  

}
