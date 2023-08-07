<?php

namespace App\Http\Controllers\Auth;

use App\GeneralSetting;
use App\User;
use App\Http\Controllers\Controller;
use App\UserExtra;
use App\WithdrawMethod;
use Illuminate\Http\Request;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware(['guest']);
        $this->middleware('regStatus')->except('registrationNotAllowed');
    }

    public function showRegistrationForm(Request $request)
    {
        
        if (@$request->ref ){
           
            $por_id = (int)substr($request->ref,2);
            $ref_user = User::where('username', $request->ref)->first();
            if ($ref_user == null) {
                $notify[] = ['error', 'Invalid Referral link.'];
                return redirect()->route('home')->withNotify($notify);
            }
            $page_title = "Sign Up";
            return view(activeTemplate() . 'user.auth.register', compact('page_title', 'ref_user'));

        }

        $ref_user = null;
        $joining = null;
        $page_title = "Sign Up";
         $notify[] = ['error', 'Invalid Referral link.'];
         return redirect()->route('home')->withNotify($notify);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'referral' => 'required|string|max:60',
            'terms_and_conditions' => 'required|string|max:60',
            'email' => 'required|string|email|max:160|unique:users',
            'password' => 'required|string|min:6',
            'username' => 'required|string|unique:users|min:6',
        ]);

    }


    protected function create(array $data)
    {
      
        $gnl = GeneralSetting::first();
        $por_id = (int)substr($data['referral'],2);
        $userCheck = User::where('username', $data['referral'])->orWhere('id', $por_id)->first();

        if($data['password'] != $data['password_confirmation'])
        {
            $notify[] = ['error', 'Password not match.'];
            return back()->withNotify($notify);
        }

        if ($userCheck == NULL) {
            $notify[] = ['error', 'Referral not found.'];
            return back()->withNotify($notify);
        } else {
            $pos = getPosition($userCheck->id, $userCheck->config_posi);
            $ref_id = $userCheck->id;
            $pos_id = $pos['pos_id'];
            $position = $pos['position'];
        }

            
        return User::create([
            'ref_id' => $ref_id,
            'pos_id' => $pos_id,
            'position' => $position,
            'firstname' => '',
            'lastname' => '',
            'email' => str_replace(' ','',$data['email']),
            'password' => Hash::make($data['password']),
            'username' => str_replace(' ','',$data['username']),

            'address' => [
                'address' => '',
                'state' => '',
                'zip' => '',
                'country' => '',
                'city' => '',
            ],
            'active_status'=>1,
            'status' => 1,
            'ev' => 1,
            'sv' => 1,
            'ts' => 0,
            'tv' => 1,
        ]);
    }

    public function registered(Request $request, $user)
    {
        $user_extras = new UserExtra();
        $user_extras->user_id = $user->id;
        $user_extras->save();


        updateFreeCount($user->id);
        $this->guard()->login($user);
        
        return redirect()->route('user.login');

    }

    public function is_valid_email($str)
    {
          return (false !== strpos($str, "@") && false !== strpos($str, "."));
    }

    public function register(Request $request)
    {
       
        $valor = $this->is_valid_email($request->email);
         
        
        if(!$valor){
            $notify[] = ['error', 'Invalid email!'];
            return back()->withNotify($notify);
        }

        if(strlen($request->username) > 12)
        {
          $notify[] = ['error', 'User cannot exceed 12 characters!'];
          return back()->withNotify($notify);
        }
        
        if (!ctype_alnum($request->username)){
          $notify[] = ['error', 'The username can only be aplhanumeric!'];
          return back()->withNotify($notify);
        }

        $this->validator($request->all())->validate();

   

        event(new Registered($user = $this->create($request->all())));
       // $this->guard()->login($user);
        
         
        return $this->registered($request, $user);
    }

    function send_smart($url){
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,$url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $resul = curl_exec ($ch);
          curl_close ($ch);
          $resu = json_decode($resul);
          return $resu;

      }



}
