<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Deposit;
use App\GeneralSetting;
use App\MatrixPlan;
use App\Plan;
use App\PoolInterest;
use App\Share;
use App\Trx;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\pop;
use App\Ticket;
use App\UserLogin;
use App\Withdrawal;
use App\participate;
use App\patentes;

class AdminController extends Controller
{

    public function dashboard(Request $request)
    {
        
        $page_title = 'Dashboard';
        $admin      = Auth::guard('admin')->user(); 
        $fecha = \Carbon\Carbon::now()->subYear();
             
        $data['page_title'] = $page_title;
        $data['user']       = $admin;
        $day = date('Y-m-d');

        $semanas = \DB::select("SELECT WEEKOFYEAR('".$day."') as semana");
        $cantidad = $semanas[0]->semana;
        $dias= date('N')-1;
        $p = 0;
        unset($tabla);
        for($i= $cantidad; $i>0;$i--){
                
                if($p>0)           $fecha_ini = Carbon::parse($day)->subDay()->toDateString();
                  else              $fecha_ini = $day;

                    $fecha_fin =  Carbon::parse($fecha_ini)->subDays($dias)->toDateString();
                    $dias = 6;
                    $day = $fecha_fin;
                    $p++;
                    
                    $reinversion = participate::where('origen','1')->whereDate('created_at','>=',$fecha_fin)->whereDate('created_at','<=',$fecha_ini)->sum('inversion');
                    $depositos =  Deposit::whereDate('created_at','>=',$fecha_fin)->whereDate('created_at','<=',$fecha_ini)->where('status',1)->sum('amount');
                 
                    $comisiones =  TRX::whereRaw("(type='unilevel' or 
                                                             type = 'comi_membresia' or type = 'comi_binary
                                                             ') and substring(created_at,1,10) >= '".$fecha_fin."' and substring(created_at,1,10) <= '".$fecha_ini."'")->sum('amount');
                    $retiros   =   TRX::whereDate('created_at', '>=', $fecha_fin)->whereDate('created_at','<=',$fecha_ini)->where('type', 'retiro')->sum('amount');
                    $participacion = participate::where('origen','0')->whereDate('created_at','>=',$fecha_fin)->whereDate('created_at','<=',$fecha_ini)->sum('inversion');
                    $membresias    = patentes::whereDate('created_at','>=',$fecha_fin)->whereDate('created_at','<=',$fecha_ini)->where('origen',0)->sum('precio');

                  $fechas = $fecha_ini.' - '.$fecha_fin;
                  $tabla[$p] = json_decode(json_encode(array('fecha'=>$fechas,
                                     'depositos'=>$depositos,
                                     'comisiones'=>$comisiones,
                                     'retiros'  => $retiros,
                                     'reinversion'=>$reinversion,
                                     'participaciones'=>$participacion,
                                     'membresias'=>$membresias
                      )));
                      if($p>5) $i=0;
        }
        $data['tabla'] = $tabla;
        return view('admin.dashboard', $data);
    }

    public function pop_index(){
        $page_title = 'Popup';
        $admin      = Auth::guard('admin')->user();
        $pop   =  pop::first();
        return view('admin.pop', compact('page_title', 'admin', 'pop'));
    }

    public function pop_save(Request $request){
           $pop = pop::first();
           $pop->status = $request->status;
           $pop->titulo  = $request->titulo_noticia;
           $pop->mensaje = $request->notice;
           $pop->save();
           $notify[] = ['success', 'Pop has been update'];
           return back()->withNotify($notify);
    }

    public function profile()
    {
        $page_title = 'Profile';
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('page_title', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        $user = Auth::guard('admin')->user();
        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = upload_image($request->image, config('constants.admin.profile.path'), config('contants.admin.profile.size'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Your profile has been updated.'];
        return redirect()->route('admin.profile')->withNotify($notify);
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::guard('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password Do not match !!'];
            return back()->withErrors(['Invalid old password.']);
        }
        $user->update([
            'password' => bcrypt($request->password)
        ]);
        return redirect()->route('admin.profile')->withSuccess('Password Changed Successfully');
    }


 



}
