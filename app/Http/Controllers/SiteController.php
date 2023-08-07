<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\Frontend;
use App\Gateway;
use App\GeneralSetting;
use App\Language;
use App\Share;
use App\Subscriber;
use App\User;
use App\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SiteController extends Controller
{


    public function CheckUsername(Request $request)
    {
        $id = User::where('username', $request->ref_id)->first();
        if ($id == '') {
            return response()->json(['success' => false, 'msg' => "<span class='help-block'><strong style='color: #f90808'>Referrer Name Not Found</strong></span>"]);
        } else {


            return response()->json(['success' => true, 'msg' => "<span class='help-block'><strong style='color: #1ed81e'>Referrer Name Matched</strong></span>
                     <input type='hidden' id='referrer_id' value='$id->id' name='referrer_id'>"]);

        }
    }

    public function userPosition(Request $request)
    {


        if ($request == '') {
            return response()->json(['success' => false, 'msg' => "<span class='help-block'><strong style='color: #f90808'>Inter Referral name first</strong></span>"]);
        } else {

            $user = User::find($request->referrer);

            $pos = getPosition($user->id, $request->position);

            $join_under = User::find($pos['pos_id']);


//                       return response()->json(['success'=>true, 'msg' => $pos['position']]);

            if ($pos['position'] == 1)
                $position = 'Left';

            else {
                $position = 'Right';
            }

            return response()->json(['success' => true, 'msg' => "<span class='help-block'><strong style='color: #1ed81e'>Your are joining under $join_under->username at $position  </strong></span>"]);

        }

    }


    public function home()
    {
        
           return redirect()->route('user.login');
           
         return;
         
        $data['page_title'] = "Home";
        $frontend = Frontend::where('key', 'blog.title')->OrWhere('key', 'testimonial.title')
            ->orWhere('key', 'service.title')->orWhere('key', 'howWork.title')
            ->orWhere('key', 'about.title')->orWhere('key', 'breadcrumb')
            ->orWhere('key', 'vid.post')->orWhere('key', 'howWork.item')->orWhere('key', 'call_to_action')->get();

        $data['banner'] = Frontend::where('key', 'banner')->first();

        $data['service_titles'] = $frontend->where('key', 'service.title')->first();
        $data['service'] = Frontend::where('key', 'service.item')->get();
        $data['about'] = $frontend->where('key', 'about.title')->first();

        $data['how_it_work_title'] = $frontend->where('key', 'howWork.title')->first();
        $data['how_it_work'] = Frontend::where('key', 'howWork.item')->get();


        $data['gates'] = Gateway::automatic()->orderBy('code')->where('status', 1)->get();

        $data['deposits'] = Deposit::where('status', 1)->latest()->take(10)->with(['user', 'gateway'])->get();
        $data['withdraws'] = Withdrawal::where('status', 1)->latest()->take(10)->with(['user', 'method'])->get();

        $data['count_bg'] = $frontend->where('key', 'call_to_action')->first();
        $data['counts'] = Frontend::where('key', 'counter.item')->get();

        $data['titles'] = Frontend::where('key', 'title_subtitle')->first();

        return view(activeTemplate() . 'home', $data);
    }


    public function about()
    {
        $data['page_title'] = "About Us";

        $data['team_title'] = Frontend::where('key', 'team.title')->first();

        $data['testimonial_title'] = Frontend::where('key', 'testimonial.title')->first();
        $data['about'] = Frontend::where('key', 'about.title')->first();

        $data['testimonial'] = Frontend::where('key', 'testimonial')->get();

        $data['teams'] = Frontend::where('key', 'team')->get();
        return view(activeTemplate() . 'about', $data);
    }

    public function terms()

    {   $data['page_title'] = "Terms And Conditions";
        $data['terms'] = Frontend::where('key', 'terms')->first();
        return view(activeTemplate() . 'terms', $data);
    }


    public function blog()
    {
        $data['page_title'] = 'Latest News';
        $data['blogs'] = Frontend::where('key', 'blog.post')->latest()->paginate(12);
        return view(activeTemplate() . 'blog', $data);
    }


    public function singleBlog($slug, $id)
    {


        $blog = Frontend::where('id', $id)->where('key', 'blog.post')->first();
        $latestBlogs = Frontend::where('id', '!=', $id)->where('key', 'blog.post')->take('5')->get();

        if ($blog != NULL) {

            $page_title = "Details";
            return view(activeTemplate() . 'singleBlog', compact('page_title', 'blog', 'latestBlogs'));


        }
        return redirect('404');

    }


    public function contact()
    {
        $data['page_title'] = "Contact us";
        $data['contact'] = Frontend::where('key', 'contact.post')->first();

        return view(activeTemplate() . 'contact', $data);

    }
    
    public function quienes()
    {
        $data['page_title'] = "Contact us";
        $data['contact'] = Frontend::where('key', 'contact.post')->first();
        return view(activeTemplate() . 'quienes', $data);

    }


    public function producto()
    {
        $data['page_title'] = "Contact us";
        $data['contact'] = Frontend::where('key', 'contact.post')->first();

        return view(activeTemplate() . 'produ_fina', $data);

    }

    function sendEmailContact(Request $request)
    {

        $this->validate($request, [
            'email' => 'required',
            'name' => 'required|',
            'message' => 'required|',
        ]);


        $from = $request->email;
        $name = $request->name;
        $message = $request->message;
        $subject = 'Contact mail from ' . $request->name;

        $general = GeneralSetting::first();
        $config = $general->mail_config;
        if ($config->name == 'php') {
            send_php_mail($general->contact_email, $from, $name, $subject, $message);
        } else if ($config->name == 'smtp') {
            send_smtp_mail($config, $general->contact_email, $general->sitetitle, $from, $name, $subject, $message);
        } else if ($config->name == 'sendgrid') {
            send_sendgrid_mail($config, $general->contact_email, $general->sitetitle, $from, $name, $subject, $message);
        } else if ($config->name == 'mailjet') {
            send_mailjet_mail($config, $general->contact_email, $general->sitetitle, $from, $name, $subject, $message);
        }


        $notify[] = ['success', 'Mail Send successfully'];
        return back()->withNotify($notify);

    }


    function subscriberStore(Request $request)

    {
        $this->validate($request, [
            'email' => 'required',
        ]);


        if (filter_var($request->email, FILTER_VALIDATE_EMAIL) == false) {
            $notify[] = ['error', 'Please insert valid email address'];
            return back()->withNotify($notify);
        }

        $subs = Subscriber::where('email', $request->email)->count();
        if ($subs == 0) {
            Subscriber::create([
                'email' => $request->email
            ]);


            $notify[] = ['success', 'Successfully Subscribed'];
            return back()->withNotify($notify);


        } else {
            $notify[] = ['error', 'Already Subscribed'];
            return back()->withNotify($notify);

        }
    }


    public function changeLang($lang)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return redirect()->back();
    }

    public function faq()
    {
        $data['page_title'] = "faq";
        $data['faqs'] = Frontend::where('key', 'faq.post')->get();
        return view(activeTemplate() . 'faq', $data);

    }

    public function marketing()

    {
        $page_title = "Marketing Tool";
        $data = Frontend::where('key', 'marketing')->get();
        return view(activeTemplate() . 'marketing', compact('data', 'page_title'));

    }


}
