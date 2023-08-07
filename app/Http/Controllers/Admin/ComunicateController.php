<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use App\WithdrawMethod;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\acciones;
use App\ConAccion;
use App\GeneralSetting;
use App\Withdrawal;
use App\Trx;
use App\Lib\CoinPaymentHosted;


class ComunicateController extends Controller
{
      function index(){
                $admin              = Auth::guard('admin')->user(); 
                $data['page_title'] = 'Envíos via Telegram';
                $data['user']       =  $admin;
                return view('admin.telegram.send', $data);
      }

      function send(Request $request){
             $mensaje = $request->mensaje;
             if($mensaje == ''){
                  $notify[] = ['error', 'Debe ingresar el mensjae a enviar.'];
                  return back()->withNotify($notify);
             }
             echo ' enviando los mensajes ... ';
            $usuario = User::where('status',1)->get();
            foreach($usuario as $data){
                    $user = $data;
                    send_tele($user, $mensaje);
            }
            
            $notify[] = ['success', 'Mensaje enviado.'];
            return back()->withNotify($notify);
      }

  
}

          