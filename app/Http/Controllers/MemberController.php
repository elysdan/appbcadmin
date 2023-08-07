<?php

namespace App\Http\Controllers;
use App\CompenstionPlan;
use App\GeneralSetting;
use App\NetworkContract;
use App\Product;
use App\super_vip;
use App\Share;
use App\Trx;
use App\Pago;
use App\Deposit;
use App\Withdrawal;
use App\auth;
use App\UserExtra;
use App\Pointpaid;
use App\User;
use App\BvLog;
use App\GatewayCurrency;
use Carbon\Carbon;
use App\Cripto_price;
use App\membresias;
use App\patentes;
use App\participate;

//use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
      function index(){
           $data['user'] = Auth()->user();
           $data['page_title'] = 'Membership';
           $member = membresias::where('status', 1)->get();
           $data['member'] = $member;
           return view(activeTemplate() . 'user.Member', $data);
      }

      function invest(){
           $general =GeneralSetting::first();
                $data['user']            =  Auth()->user();
                $data['page_title']      =  'Participations';
                $data['minimo']          =  $general->minimo;
                $data['precio_gdetoken'] =  $general->precio_gdetoken;
                $data['precio_actual']   =  $general->precio_gdetoken;
                $data['participate']     =  participate::where('user_id', Auth()->user()->id)->get();
                return view(activeTemplate() . 'user.invest', $data);
      }

      function buy(Request $request){

                $this->validate($request, [
                   'member' => 'required|numeric'
                ]); 
                $user = Auth()->user();
                $ref = el_ref($user->ref_id);
                $member = membresias::where('id', $request->member)->first();
                if($user->balance <  $member->precio)
                {
                    $notify[] = ['error', 'Insufficient balance']; 
                    return back()->withNotify($notify);
                }
                $user->balance -= $member->precio;
                $user->save();

                $socio_activo = socio_activo($ref->id);                  


                $pat = new patentes();
                $pat->user_id = $user->id;
                $pat->member_id = $request->member;
                $pat->precio    = $member->precio;
                $pat->save();

                $titulo =  'Compra de membresía de '.$pat->precio;
                transaccion($user, ($pat->precio * -1), $titulo, 'membresia' );

                if($socio_activo > 0){
                     $por = 7;
                     $directo = ($member->precio * 7) /100;
                     $ref->usdt += $directo;
                     $ref->save();
                     $titulo = 'comisión del '.$por.'% compra membresía ('.$member->precio.')';  
                     earning($ref, $directo, $titulo,  'membresia' );
                }

                     $puntos  = ($member->precio);
                     $puntos  = round($puntos);
                     $details = $user->username.' to '.$puntos. ' points';    

                     updateBV($user->id, $puntos, $details);
                     

                $notify[] = ['success', 'Buy complete']; 
                return back()->withNotify($notify);
      }

      function participate(Request $request){

                    $this->validate($request, [
                         'amount' => 'required|numeric'
                    ]); 

                    $general =GeneralSetting::first();
                    $user = Auth()->user();

                    if($request->amount < 5){
                         $notify[] = ['error', 'Minimum investment is 5 usdt']; 
                         return back()->withNotify($notify);
                    }

                    if($user->balance <  $request->amount){
                            $notify[] = ['error', 'Insufficient balance']; 
                            return back()->withNotify($notify);
                    }

                    $user->balance -= $request->amount;
                    $user->save();



                    $token = $request->amount / $general->precio_gdetoken;
                    $pat = new participate();
                    $pat->user_id      = $user->id;
                    $pat->inversion    = $request->amount;
                    $pat->precio_cripto = $general->precio_gdetoken;
                    $pat->gdetoken     =  $token;
                    $pat->save();
                    
                    $titulo = $token. 'gdetoken de participación precio '.$general->precio_gdetoken;
                    transaccion($user, ($request->amount * -1), $titulo, 'participa' );

                    $user->gdetoken += $token;
                    $user->save();
                  
                    $title = $user->username.' participation of '.$request->amount.' usd ';
               
                    pay_unilevel($user->ref_id,$request->amount, $title);

                    $notify[] = ['success', 'Participate complete']; 
                    return back()->withNotify($notify);
                    
      } 
}
