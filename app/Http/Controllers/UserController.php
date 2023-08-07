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
use App\Wallet_asigna;
use App\Trx;
use App\User;
use App\UserExtra;
use App\UserLogin;
use App\UserMatrix;
use App\Withdrawal;
use App\WithdrawMethod;
use App\Share;
use App\PoolInterest;
use App\Pointpaid;
use App\pop;
use App\kyc;
use Carbon\Carbon;
use App\GatewayCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use  Auth;
use  Session;
use Illuminate\Support\Str;
use App\Lib\CoinPaymentHosted;
use App\DB;


class UserController extends Controller
{
    public function home()
    {
        $user = Auth::user();
        $general = GeneralSetting::first();
        $data['precio']                  =    $general->precio_gdetoken;
        $data['total_usuario']           =    User::where('ref_id', $user->id)->count();     
        $data['total_left']              =    User::where('ref_id', $user->id)->where('position',1)->count();   
        $data['total_right']             =   User::where('ref_id', $user->id)->where('position',2)->count(); 
        $data['user']                    =    $user;
        $data['status_binario']          =   binary_activo($user->id);
        $data['UserExtra']               =    UserExtra::where('user_id', $user->id)->first();  
        $data['page_title']              =    "Dashboard";
        $data['total_deposit']           =    Deposit::whereUserId($user->id)->where('status', 1)->where('isact', 0)->sum('amount');
        $data['referidos']               =    User::where('ref_id', $user->id)->count();
        $data['direccion']               =    @$user->address->country;
        return view(activeTemplate() . 'user.dashboard', $data);
    }


    public function profile_edit(){
            $user = Auth::user();
            $page_title = 'Profile';
            $us = User::where("id",$user->ref_id)->first();
            $sponsor =@$us->username;
            $status_tron = 1;

            $set = WithdrawMethod::Where('id',1)->first();
            $minimo_retiro = $set->min_limit;
            $set = WithdrawMethod::Where('id',2)->first();
            $minimo_retiro_trx = $set->min_limit;
            return view(activeTemplate() . 'user.editprofile', $data);
    }



    public function profile_pos($valor){
            if($valor == 1 || $valor == 2)
            {
                $user = auth()->user();
                $user->config_posi = $valor;
                $user->save();
                $notify[] = ['success', 'Your side binary update']; 
                return back()->withNotify($notify);
            }
            $notify[] = ['error', 'Your side binary no update']; 
            return back()->withNotify($notify);
    }



    public function myTree()
    {
        $data['user'] = auth()->user();
        $data['tree'] = showTreePage(Auth::id());
        if(auth()->user()->ref_id>0)
              $ref = User::where('id', auth()->user()->ref_id)->first('username');
        else
              $ref = User::where('id', auth()->user()->id)->first('username'); 

        $user   = $data['user'];
        $extrs  = UserExtra::where('user_id',$user->id )->first();

        $data['extra']      = $extrs;
        $data['ref_by']     = ucfirst(@$ref->username);
        $data['page_title'] = "My Tree";
        return view(activeTemplate() . 'user.my_tree', $data);

    }



    public function otherTree(Request $request, $username = null)
    {
        $data['user']  =  auth()->user();
        if ($request->username) {
            $user = User::where('username', $request->username)->first();
        } else {
            $user = User::where('username', $username)->first();
        }

        if($user == null){
            $notify[] = ['error', 'User Invalid!!'];
            return redirect()->route('user.my.tree')->withNotify($notify);
        }   

        $extrs = UserExtra::where('user_id',$user->id )->first();
        $data['extra']      = $extrs;

        if(treeAuth($user->id, auth()->id())){
            $data['tree'] = showTreePage($user->id);
            $data['page_title'] = "Tree of " . $user->fullname;
            $ref = User::where('id', $user->ref_id)->first('username');
            $data['ref_by'] = ucfirst($ref->username);
            return view(activeTemplate() . 'user.my_tree', $data);
        }

        $notify[] = ['error', 'Tree Not Found or You do not have Permission to view that!!'];
        return redirect()->route('user.my.tree')->withNotify($notify);

    }

    function profile(){
        $user = Auth::user();
        $general = GeneralSetting::first();
        $data['page_title'] = 'Perfil de usuario';
        $data['user'] = $user;
        return view(activeTemplate() . 'user.profile', $data);
    }


    function guardar_perfil(Request $request){
            
        $this->validate($request, [
            'documento' => 'required',
            'telefono'  => 'required',
            'nombre'    => 'required',
            'apellido'  => 'required'
         ]); 


         $user = Auth()->user();
         $user->identity   = $request->documento;
         $user->mobile      = $request->telefono;
         $user->firstname   = $request->nombre;
         $user->lastname    = $request->apellido;
         $user->save();

         $notify[] = ['success', 'Datos actualizados']; 
         return back()->withNotify($notify);
    }

    function passwordUpdate(Request $request){

        $this->validate($request, [
            'current' => 'required',
            'new'  => 'required',
            'repite'    => 'required'
         ]); 
             
         $user = Auth()->user();
         $valor = Hash::check($request->current, $user->password);
         
         if(!$valor){
            $notify[] = ['error', 'Contraseñas ctual incorrecta']; 
            return back()->withNotify($notify);
         }   
         if($request->new != $request->repite)
         {
            $notify[] = ['error', 'Contraseñas no coinciden']; 
            return back()->withNotify($notify);
         }
           

         $user->password = Hash::make($request->new);
         $user->save();

         $notify[] = ['success', 'Password a sido cambiado']; 
         return back()->withNotify($notify);
         
    }

    function wallet_change(Request $request){
        $this->validate($request, [
            'wallet' => 'required'
         ]); 

         $user = Auth()->user();

         $user->wallet_wave  = $request->wallet;
         $user->save();

         $notify[] = ['success', 'Tu wallet ha sido actualizada']; 
         return back()->withNotify($notify);
    }



    function arbol($id){

        $segundo = User::where('ref_id', $id)->get();
        unset($cua);
        if($segundo){
                        foreach($segundo as $data){
                                    $cua[] = $data;
                }
            }
        $resul = json_encode($cua);   
        return $resul;
    }



    function myRefLog(){
        $data['page_title']    = "Mis referidoa";
        $data['user']          = Auth()->user();
        $data['empty_message'] = 'No data found';
        $data['refers'] = User::where('ref_id', auth()->id())->latest()->paginate(15);
        return view(activeTemplate() . '.user.my_ref', $data);
    }






}



