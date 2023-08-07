<?php

namespace App\Http\Controllers\Admin;

use App\Deposit;
use App\GeneralSetting;
use App\Trx;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\participate;
use App\Http\Controllers\Controller;

class ParticipacionController extends Controller
{
    public function index(){
        $username           = @$_GET['userna'];
        $page_title         = 'Participaciones';
        $admin              = Auth::guard('admin')->user(); 
        $data['page_title'] = $page_title;
        $data['user']       = $admin;
        if($username == ''){
              $Participar   = participate::
                              latest()->paginate(10);
              $total        = participate::sum('inversion');
        }
        else{
              $usuario = User::where('username', $username)->first();
               $Participar     = participate::where('user_id', @$usuario->id)->
                                 latest()->paginate(10);  
               $total          = participate::where('user_id', @$usuario->id)->sum('inversion'); 
        }
        $general = GeneralSetting::first();
        $data['precio_actual'] = $general->precio_gdetoken;
        $data['Participar']    = $Participar;
        $data['total']         = $total;
        $data['empty_message'] = 'No deposit history available.';
        return view('admin.participaciones.lista', $data);

     }
}
