<?php

namespace App\Http\Controllers\Admin;

use App\Deposit;
use App\GeneralSetting;
use App\Trx;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepositController extends Controller
{

    public function index(){
                    
                    $username = @$_GET['username'];
                    $page_title = 'Depósitos';
                    $admin      = Auth::guard('admin')->user(); 
                    $data['page_title'] = $page_title;
                    $data['user']       = $admin;
                    if($username == '')
                    $deposits = Deposit::where('status', 0)->
                                latest()->paginate(10);
                    else{
                        $usuario = User::where('username', $username)->first();
                      
                           $deposits = Deposit::where('status', 0)->where('user_id', @$usuario->id)->
                                latest()->paginate(10);   
                    }
                    
                    $data['deposits'] = $deposits;
                    $data['empty_message'] = 'No deposit history available.';
                    return view('admin.deposit.deposit_list', $data);

    }

    public function confirmar($id){
        $admin      = Auth::guard('admin')->user(); 
        $data['user']       = $admin;
        $deposits = Deposit::where('id',$id)->first();
        $data['deposits'] = $deposits;
        $page_title = 'Deposit nro '.$id;
        $data['page_title'] = $page_title;
        $data['empty_message'] = 'No deposit history available.';
        return view('admin.deposit.detail', $data);
    }

    public function save(Request $request){
               
        $deposit = Deposit::where('id',$request->id_deposit)->first();
       
        if($deposit->status != 0 )
        {
            $notify[] = ['error', 'El depósito ya esta confirmado.'];
            return back()->withNotify($notify);
        }

        $deposit->status = $request->estado;
        
           if($deposit->red == 'trc20')
                 $link = 'https://tronscan.org/#/transaction/'.$deposit->id_tx;
            else
                 $link = 'https://bscscan.com/tx/'.$deposit->id_tx;
        $user = user::where('id', $deposit->user_id)->first();
        
       if($deposit->status == 1){
            $monto = $deposit->amount;
            $user->balance += $monto;
            $user->save();
            
           send_tele($user, '¡Depósito aprobado! - Se ha acreditado  '.round($monto,2).' USD a la cuenta de '.$user->username.'. ('.$link.')');
            
            $deposit->save();
            $notify[] = ['success', 'El depósito fue aprobado.'];
       }
        else{ 
             send_tele($user, '¡Depósito rechazado! para '.$user->username.' ('.$link.')');
             $deposit->save();
             $notify[] = ['success', 'El depósito fue rechazado.'];
        }
        return back()->withNotify($notify);
           
    }

    public function confirmar_linear($id, $status=false){
               
        $deposit = Deposit::where('id',$id)->first();
        if($deposit->status != 0 )
        {
            $notify[] = ['error', 'El depósito ya esta confirmado.'];
            return back()->withNotify($notify);
        }
        
        $deposit->status = $status;
        if($deposit->red == 'trc20')
                 $link = 'https://tronscan.org/#/transaction/'.$deposit->id_tx;
            else
                 $link = 'https://bscscan.com/tx/'.$deposit->id_tx;
        
        $user = user::where('id', $deposit->user_id)->first();
       if($deposit->status == 1){
            $monto = $deposit->amount;
            $user->balance += $monto;
            $user->save();
            $deposit->save();
            send_tele($user, '¡Depósito aprobado! - Se ha acreditado  '.round($monto,2).' USD a la cuenta de '.$user->username.'. ('.$link.')');
            $notify[] = ['success', 'El depósito fue aprobado.'];
       }
        else{
             send_tele($user, '¡Depósito rechazado! para '.$user->username.' ('.$link.')');
             $deposit->save();
             $notify[] = ['success', 'El depósito fue rechazado.'];
        }
        return back()->withNotify($notify);
           
    }


    public function deposit($valor = '')
    {   
         
             $title = 'Deposit History'; 
             $deposits = Deposit::where('status', '!=', 0)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));

        $page_title = $title;
        $empty_message = 'No deposit history available.';
        
         return view('admin.deposit_list', compact('page_title', 'empty_message', 'deposits'));
    }
    
    public function deposit_auditor ($valor = '')
    {   
            $title = 'Deposit Via Admin'; 
            $deposits = Trx::where('type','deposit')->join('users', 'users.id','trxes.user_id')->where('title','Deposit Via Admin')->latest()->paginate(15,['users.id', 'users.username', 'trxes.amount','trxes.amount_con', 'trxes.balance', 'trxes.created_at', 'trxes.moneda', 'trxes.title','trxes.user_id']);
            $page_title = $title;
            $empty_message = 'No deposit history available.';
            return view('admin.deposit.deposit_list', compact('page_title', 'empty_message', 'deposits'));
    }



    public function search(Request $request, $scope)
    {
        $search = $request->search;
        if (empty($search)) return back();
        $page_title = '';
        $empty_message = 'No search result was found.';

        $deposits = Deposit::with(['user', 'gateway'])->where(function ($q) use ($search) {
            $q->where('trx', $search)->orWhereHas('user', function ($user) use ($search) {
                $user->where('username', $search);
            });
        });
        switch ($scope) {
            case 'pending':
                $page_title .= 'Pending Deposits Search';
                $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 2);
                break;
            case 'approved':
                $page_title .= 'Approved Deposits Search';
                $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 1);
                break;
            case 'rejected':
                $page_title .= 'Rejected Deposits Search';
                $deposits = $deposits->where('method_code', '>=', 1000)->where('status', 3);
                break;
            case 'list':
                $page_title .= 'Deposits History Search';
                break;
        }
        $deposits = $deposits->paginate(config('constants.table.defult'));
        $page_title .= ' - ' . $search;

        return view('admin.deposit_list', compact('page_title', 'search', 'scope', 'empty_message', 'deposits'));
    }

    public function pending()
    {
        $page_title = 'Pending Deposits';
        $empty_message = 'No pending deposits.';
        $deposits = Deposit::where('method_code', '>=', 1000)->where('status', 2)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view('admin.deposit_list', compact('page_title', 'empty_message', 'deposits'));
    }

    public function approved()
    {
        $page_title = 'Approved Deposits';
        $empty_message = 'No approved deposits.';
        $deposits = Deposit::where('method_code', '>=', 1000)->where('status', 1)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view('admin.deposit_list', compact('page_title', 'empty_message', 'deposits'));
    }

    public function rejected()
    {
        $page_title = 'Rejected Deposits';
        $empty_message = 'No rejected deposits.';
        $deposits = Deposit::where('method_code', '>=', 1000)->where('status', 3)->with(['user', 'gateway'])->latest()->paginate(config('constants.table.default'));
        return view('admin.deposit_list', compact('page_title', 'empty_message', 'deposits'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $deposit = Deposit::where('method_code', '>=', 1000)->findOrFail($request->id);

        $user = User::find($deposit->user_id);
        $user->balance += $deposit->amount;
        $user->save();


        $deposit->update(['status' => 1]);

        $deposit->user->transactions()->save(new Trx([
            'amount' => $deposit->amount,
            'main_amo' => $deposit->amount+$deposit->charge,
            'charge' => $deposit->charge,
            'type' => 'deposit',
            'title' => 'Deposit Via ' . $deposit->gateway->name,
            'trx' => $deposit->trx,
            'balance' => $user->balance,
        ]));


        $general = GeneralSetting::first(['cur_sym']);

        send_email($deposit->user, 'DEPOSIT_APPROVE', [
            'trx' => $deposit->trx,
            'amount' => $general->cur_sym . formatter_money($deposit->amount),
            'receive_amount' => $general->cur_sym . formatter_money($deposit->amount),
            'charge' => $general->cur_sym . formatter_money($deposit->charge),
            'method' => $deposit->gateway->name,
        ]);

        send_sms($deposit->user, 'DEPOSIT_APPROVE', [
            'trx' => $deposit->trx,
            'amount' => $general->cur_sym . formatter_money($deposit->amount),
            'receive_amount' => $general->cur_sym . formatter_money($deposit->amount),
            'charge' => $general->cur_sym . formatter_money($deposit->charge),
            'method' => $deposit->gateway->name,
        ]);


        $notify[] = ['success', 'Deposit has been approved.'];
        return back()->withNotify($notify);
    }

   
}
