<?php

namespace App\Http\Controllers\Admin;

use App\Deposit;
use App\GeneralSetting;
use App\Trx;
use App\User;
use App\nfts;
use App\usernfts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\participate;
use App\Http\Controllers\Controller;

class nftsController extends Controller
{
    public function index(){
        $admin              = Auth::guard('admin')->user(); 
        $data['page_title'] = '';
        $data['user']       = $admin;
        $data['nfts']       =   nfts::paginate('10');
        $data['empty_message'] = 'No hay data.';
        return view('admin.nfts.index', $data);
    }

    public function buy_pendientes(){
        $admin              =    Auth::guard('admin')->user(); 
     
        $page_title         =    'Nfts pendiendes por confirmar';
        $data['page_title'] =    $page_title;
        $data['user']       =    $admin;
        $data['parchuse']  =     usernfts::where('status', 0)->paginate('10');
        $data['empty_message'] = 'No hay data.';
        return view('admin.nfts.buypend', $data);
    }

    public function confirmar(Request $request){
        $admin              =    Auth::guard('admin')->user(); 
        $data['page_title'] =    'Nfts por usuarios';
        $data['user']       =    $admin;
        $data               =    usernfts::where('status', 0)->paginate('10');
        $data['empty_message'] = 'No hay datas.';
        return view('admin.nfts.detail', $data);

    }

    public function editar_buy(Request $request){
      
        $this->validate($request, [
            'id' => 'required|numeric'
         ]); 
         $nfts              =   usernfts::where('id',$request->id)->first();
         $user              = User::where('id', $nfts->user_id)->first();

        if(!$request->hash){
                                            $notify[] = ['error', 'Por favor ingrese el hash del nfts enviado']; 
                                            return back()->withNotify($notify);
        }

         $nfts->hash = $request->hash;
         $nfts->status = 1;
         $nfts->save();
         $link = 'https://polygonscan.com/tx/'.$request->hash;
         send_tele($user, 'Tu compra de nfts ha sido confirmada  '.$link);
         $notify[] = ['success', 'Nfts ha sido enviada']; 
         return redirect()->route('admin.nfts')->withNotify($notify);
    
    }

    public function details($i){
        $admin              =    Auth::guard('admin')->user(); 
        $data['nfts']       =    usernfts::where('id',$i)->first();
        $data['usuario']    =    User::where('id', $data['nfts']->user_id)->first();
        $data['page_title'] =    'Buy nfts '.$data['nfts']->nfts_id;
        $data['user']       =    $admin;
        return view('admin.nfts.detail', $data);
    }

    public function purchase(){
        $admin              =    Auth::guard('admin')->user(); 
        $data['page_title'] =    'Nfts por usuarios';
        $data['user']       =    $admin;
        $data['usernfts']               =   usernfts::where('status', 1)->paginate('10');
        $data['empty_message'] = 'No hay data.';
        return view('admin.nfts.purchase', $data);
    }

    public function edit($id){
        $admin              = Auth::guard('admin')->user(); 
        $data['nfts']       =   nfts::where('id', $id)->first();

        $data['page_title'] = 'Editar nfts '.$data['nfts']->id;
        $data['user']       = $admin;
        return view('admin.nfts.edit', $data);
    }

    public function save(Request $request){
           
    
        $this->validate($request, [
            'id'          => 'required|numeric',
            'nombre'      => 'required',
            'cantidad'    =>  'required',
            'descripcion' => 'required',
            'precio'      => 'required'
         ]); 

         $nfts = nfts::where('id', $request->id)->first();
         $nfts->Nombre         = $request->nombre;
         $nfts->Cantidad       = $request->cantidad;
         $nfts->Descripcion    = $request->descripcion;
         $nfts->Precio         = $request->precio;
         $nfts->save();


         $notify[] = ['success', 'Nfts ha sido actualizada!']; 
         return redirect()->route('admin.my.nfts')->withNotify($notify);

    }





}