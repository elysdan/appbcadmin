<?php

namespace App\Http\Controllers\Admin;

use App\Deposit;
use App\GeneralSetting;
use App\Trx;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\patentes;
use App\membresias;
use App\Http\Controllers\Controller;

class MembresiaController extends Controller
{
    public function index(){

        $username = @$_GET['userna'];
        $page_title = 'Membresias';
        $admin      = Auth::guard('admin')->user(); 
        $data['page_title'] = $page_title;
        $data['user']       = $admin;
        if($username == ''){
            $Membresias = patentes::
                          latest()->paginate(10);
            $total      = patentes::sum('precio');
        }else{
            $usuario    = User::where('username', $username)->first();
            $Membresias = patentes::where('user_id', @$usuario->id)->
                          latest()->paginate(10);   
            $total      = patentes::where('user_id', @$usuario->id)->sum('precio');
        }

        $data['paquete']    = membresias::where('status',1)->get();
        $data['total']      = $total;
        $data['Membresias'] = $Membresias;
        $data['empty_message'] = 'No deposit history available.';
        return view('admin.membresias.lista', $data);
        

     }

     public function buy(Request $request){

        $this->validate($request, [
            'member' => 'required|numeric',
            'username' => 'required'
         ]); 


         $user = User::where('username', $request->username)->first();

         if(!$user){
            $notify[] = ['error', 'Usuario invalido o no existe!']; 
             return back()->withNotify($notify);
         }
         $member = membresias::where('id', $request->member)->first();
         
         $resp = membre_activa($user->id, $request->member);
         if($resp > 0){
             $notify[] = ['error', 'El usuario ya tiene esta membresía activa']; 
             return back()->withNotify($notify);
         }
        
         //$user->balance -= $member->precio;
       //  $user->save();
           
         $pat = new patentes();
         $pat->user_id = $user->id;
         $pat->member_id = $request->member;
         $pat->precio    = $member->precio;
         $pat->origen    = 1;
         $pat->save();

         $titulo =  'Compra de membresía de '.round($pat->precio);
         transaccion($user, ($pat->precio * -1), $titulo, 'membresia' );
         
         $notify[] = ['success', 'Membresia activa']; 
         return back()->withNotify($notify);

     }
}
