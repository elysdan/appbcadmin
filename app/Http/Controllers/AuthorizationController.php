<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use Auth;
use app\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthorizationController extends Controller
{
    public function checkValidCode($user, $code, $add_min = 10000)
    {
        if (!$code) return false;
        if (!$user->ver_code_send_at) return false;
        if ($user->ver_code_send_at->addMinutes($add_min) < Carbon::now()) return false;
        if ($user->ver_code !== $code) return false;
        return true;
    }

    public function change_email()
    {
        $view = activeTemplate() . 'user.auth.change';
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->ev) {
                $page_title = 'Correct email form';
                return view($view, compact('user', 'page_title'));
            }
        }
        return redirect()->route('user.login');
    }





    public function change_form(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'repetir' => 'required'
        ]);

        $email = $request->email;
        $repetir = $request->repetir;

        if($email != $repetir)
        {
            $notify[] = ['error', 'Email no match!'];
            return back()->withNotify($notify);
        } 
        $email = str_replace(' ','',$email);
         
        $valido =  User::where('email', $email)->first();
        if($valido == null){
            $user = auth()->user();
            $user->email = $email;
            $user->save();
            $notify[] = ['success', 'Your email has been changed'];
            return redirect()->route('user.login')->withNotify($notify);

        }else{
            $notify[] = ['error', 'Email lready Exists !'];
            return back()->withNotify($notify);
        }  
    }

    public function recuperar2fa(){
        $user = auth()->user();
        $view = activeTemplate() . 'user.auth.recovery2fa';

        if(!$user->tv) {
            $page_title = 'Retrieve the google authenticator';
            return view($view, compact('user', 'page_title'));
        }

     return redirect()->route('user.home');
    }




    public function authorizeForm()
    {
        $view = activeTemplate() . 'user.auth.authorize';
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->status) {
                $page_title = 'Your Account has been blocked';
                return view($view, compact('user', 'page_title'));
            } elseif (!$user->ev) {
                if (!$this->checkValidCode($user, $user->ver_code)) {
                    $user->ver_code = verification_code(6);
                    $user->ver_code_send_at = Carbon::now();
                    $user->save();
                    send_email($user, 'EVER_CODE', [
                        'code' => $user->ver_code
                    ]);
                }
                $page_title = 'Email verification form';
                return view($view, compact('user', 'page_title'));
            } elseif (!$user->sv) {
                if (!$this->checkValidCode($user, $user->ver_code)) {
                    $user->ver_code = verification_code(6);
                    $user->ver_code_send_at = Carbon::now();
                    $user->save();
                    send_sms($user, 'SVER_CODE', [
                        'code' => $user->ver_code
                    ]);
                }
                $page_title = 'SMS verification form';
                return view($view, compact('user', 'page_title'));
            } elseif (!$user->tv) {
                $page_title = 'Google Authenticator';
              /*  $user->tv =1;
                $user->save();
                return redirect()->route('user.home'); */
               return view($view, compact('user', 'page_title'));
            }
        }
        return redirect()->route('user.login');
    }

    public function sendVerifyCode(Request $request)
    {
        $user = Auth::user();
        if ($this->checkValidCode($user, $user->ver_code, 2)) {
            $target_time = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay = $target_time - time();
            throw ValidationException::withMessages(['resend' => 'Please Try after ' . $delay . ' Seconds']);
        }
        if (!$this->checkValidCode($user, $user->ver_code)) {
            $user->ver_code = verification_code(6);
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        } else {
            $user->ver_code = $user->ver_code;
            $user->ver_code_send_at = Carbon::now();
            $user->save();
        }

        if ($request->type === 'email') {
            send_email($user, 'EVER_CODE', [
                'code' => $user->ver_code
            ]);
            $notify[] = ['success', 'Email verification code sent successfully'];
            return back()->withNotify($notify);
        } elseif ($request->type === 'phone') {
            send_sms($user, 'SVER_CODE', [
                'code' => $user->ver_code
            ]);
            return back()->with('success', 'SMS verification code sent successfully');
        } else {
            throw ValidationException::withMessages(['resend' => 'Sending Failed']);
        }
    }
    public function emailVerification(Request $request)
    {

        $request->validate([
            'email_verified_code' => 'required',
        ], [
            'email_verified_code.required' => 'Email verification code is required',
        ]);

        $user = Auth::user();
        if ($this->checkValidCode($user, $request->email_verified_code)) {
            $user->ev = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            return redirect()->intended(route('user.home'));
        }
        throw ValidationException::withMessages(['email_verified_code' => 'Verification code didn\'t match!']);
    }

    public function smsVerification(Request $request)
    {
        $request->validate([
            'sms_verified_code' => 'required',
        ], [
            'sms_verified_code.required' => 'SMS verification code is required',
        ]);
        $user = Auth::user();
        if ($this->checkValidCode($user, $request->sms_verified_code)) {
            $user->sv = 1;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            return redirect()->intended(route('user.home'));
        }
        throw ValidationException::withMessages(['sms_verified_code' => 'Verification code didn\'t match!']);
    }

    public function g2faVerification(Request $request)
    {
        $user = auth()->user();

        $this->validate(
            $request,
            [
                'code' => 'required',
            ]
        );
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;
        if ($oneCode == $userCode) {
            $user->tv = 1;
            $user->save();
            return redirect()->route('user.home');
        } else {
            return back()->with('danger', 'Wrong Verification Code');
        }
    }
}
