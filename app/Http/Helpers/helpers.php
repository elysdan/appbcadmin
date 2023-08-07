<?php
use App\GeneralSetting;
use App\User;
use App\Trx;
use App\UserExtra;
use App\MatrixPlan;
use App\MatrixSubscriber;
use App\UserMatrix;
use Carbon\Carbon;
use App\Share;
use App\rankpaid;
use App\Lib\Binance;
use App\WithdrawMethod;
use App\Pointpaid;
use App\Lib\CoinPaymentHosted;
use App\Cursos;
use App\patentes;
use App\Membresias;
use App\nfts;
use App\usernfts;

use Intervention\Image\Facades\Image;

function lin_refer($id){
    $total = strlen($id); 
    switch($total){
        case 1: $res = '000'.$id; break;
        case 2: $res = '00'.$id; break;
        case 3: $res = "0".$id; break;
        default: $res = $id;
    }
    return "18".$res;
}


function cantidad_vendida($id){
   $vendidas =  usernfts::where('nfts_id', $id)->count();
   return $vendidas;
}

function disponible($id){
    $nfts =  nfts::where('id', $id)->first();
    $cantidad = $nfts->cantidad;
    $dispo = $cantidad - cantidad_vendida($id);
    return $dispo;
}




function name_nfts($id){
       $nf = nfts::where('id', $id)->first();
       return $nf;
}


function smart_get($session)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:3006/'.$session);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    //close connection
    curl_close($ch);
    return $result;
}


function send_smart($sesion, $data){
    $arra = "";
    foreach ($data as $key => $value) {
        if($arra != "") $arra .= "&";
        $arra .= $key."=".$value;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"http://localhost:3006/".$sesion);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$arra);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resul = curl_exec ($ch);
    curl_close ($ch);
    $resu = json_decode($resul);
    return $resu;
}


function dolar_matic(){
    $api = json_decode(curlContent('https://api.coingecko.com/api/v3/simple/price?ids=matic-network&vs_currencies=usd'), true);
    $api = (object)$api['matic-network'];
    $usd = $api->usd;
    return $usd;
}

function el_ref($id){
     $ref = User::where('id', $id)->first();
     return $ref;
}

function membre_activa($user, $memb){
    $fecha =  $date = Carbon::now()->subYear()->toDateString();
    $cant_membresia = patentes::where('user_id',$user)->where('member_id', $memb)->where('created_at','>',$fecha )->count('id');
    return $cant_membresia;
}

function socio_activo($id){
    $fecha =  $date = Carbon::now()->subYear()->toDateString();
      $cant_membresia = patentes::where('user_id',$id)->where('created_at','>',$fecha )->count('id');
      return $cant_membresia;
}

function paq_activo($id){
    $fecha =  $date = Carbon::now()->subYear()->toDateString();
      $respuesta = patentes::where('user_id',$id)->where('created_at','>',$fecha )->orderBy('id', 'desc')->first();
      return $respuesta;
}



function new_por_level($i){
       
    switch($i){
       case 1: $por = 3; break;
       case 2: $por = 2; break;
       case 3: $por = 1; break;
    }
    return $pot;
}



function ceros_($i){
     $i = $i -1;
    $can = strlen($i);
    switch($can){
        case 1: $res = "000".$i;  break;
        case 2: $res = "00".$i;   break;
        case 3: $res = "0".$i;    break;
        case 4: $res = $i;        break;
        default : $res = $i;
    }
    return $res;
}


function ver_wallet($id){
          $arra = "id=".$id;
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,"agresr_url");
          curl_setopt($ch, CURLOPT_POST, TRUE);
          curl_setopt($ch, CURLOPT_POSTFIELDS,$arra);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $resul = curl_exec ($ch);
          curl_close ($ch);
          $resu = json_decode($resul);
          $wallet_smart = @$resu->result;
          if($wallet_smart == "" or $wallet_smart == '0x0000000000000000000000000000000000000000'){
            $wallet_smart = "";
           }
          return $wallet_smart;
      }





// aqui inicie



// aqui te rmine


function get_image($image, $clean = '')
{
    return file_exists($image) && is_file($image) ? asset($image) . $clean : asset(config('constants.image.default'));
}


function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}


function description_shortener($string, $length = null)
{
    if (empty($length)) $length = config('constants.stringLimit.default');
    return Illuminate\Support\Str::limit($string, $length);
}


function sidenav_active($routename, $class = 'active open')
{
    if (is_array($routename)) {
        foreach ($routename as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routename)) {
        return $class;
    }
}


function show_datetime($date, $format = 'd M, Y h:ia')
{
    return \Carbon\Carbon::parse($date)->format($format);
}


function shortcode_replacer($shortcode, $replace_with, $template_string)
{

    return str_replace($shortcode, $replace_with, $template_string);
}


function verification_code($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = 0;
    while ($length > 0 && $length--) {
        $max = ($max * 10) + 9;
    }
    return random_int($min, $max);
}


function site_precision()
{
    return config('constants.currency.precision.' . strtolower(config('constants.currency.base')));
}

function formatter_money($money, $currency = null)
{
   
    $money = sprintf('%.8f', $money);
    return $money;
}


function upload_image($file, $location, $size = null, $old = null, $thumb = null)
{
    $path = make_directory($location);
    if (!$path) throw new Exception('File could not been created.');

    if (!empty($old)) {
        remove_file($location . '/' . $old);
        remove_file($location . '/thumb_' . $old);
    }

    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();

    $image = Image::make($file);
    if (!empty($size)) {
        $size = explode('x', $size);
        $image->fit($size[0], $size[1]);
    }
    $image->save($location . '/' . $filename);

    if (!empty($thumb)) {

        $thumb = explode('x', $thumb);
        Image::make($file)->resize($thumb[0], $thumb[1])->save($location . '/thumb_' . $filename);
    }

    return $filename;
}

function make_directory($path)
{
    if (file_exists($path)) return true;
    return mkdir($path, 0755, true);
}

function remove_file($path)
{
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}

function valida_wall($wallet){
              $data['wallet'] = $wallet;
              $url = 'agregar url';
              $res = curlpost($url, $data);
             if(!$res->result)
               return 'error';
               else
               return 'ok';
}





function curlpost($url, $data){
       $arra = "";
    foreach ($data as $key => $value) {
        if($arra != "") $arra .= "&";
        $arra .= $key."=".$value;
    }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$arra);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resul = curl_exec ($ch);
        curl_close ($ch);
        $resu = json_decode($resul);
        return $resu;
}


function send_general_email($email, $subject, $message, $receiver_name = '')
{
    
    $general = GeneralSetting::first();

    if ($general->en != 1 || !$general->efrom) {
        return;
    }

    $message = shortcode_replacer("{{message}}", $message, $general->etemp);
    $message = shortcode_replacer("{{name}}", $receiver_name, $message);
    $config = $general->mail_config;

    if ($config->name == 'php') {
        send_php_mail($email, $receiver_name, $general->efrom, $subject, $message);
    } else if ($config->name == 'smtp') {
        send_smtp_mail($config, $email, $receiver_name, $general->efrom, $general->sitetitle, $subject, $message);
    } else if ($config->name == 'sendgrid') {
        send_sendgrid_mail($config, $email, $receiver_name, $general->efrom, $general->sitetitle, $subject, $message);
    } else if ($config->name == 'mailjet') {
        send_mailjet_mail($config, $email, $receiver_name, $general->efrom, $general->sitetitle, $subject, $message);
    }
}

function send_email($user, $type, $shortcodes = [])
{
    
    $general = GeneralSetting::first();
    $email_template = \App\EmailTemplate::where('act', $type)->where('email_status', 1)->first();
    if ($general->en != 1 || !$email_template) {
        return;
    }
    $message = shortcode_replacer("{{name}}", $user->username, $general->etemp);
    $message = shortcode_replacer("{{message}}", $email_template->email_body, $message);

    if (empty($message)) {
        $message = $email_template->email_body;
    }

    foreach ($shortcodes as $code => $value) {
        $message = shortcode_replacer('{{' . $code . '}}', $value, $message);
    }
    $config = $general->mail_config;

    if ($config->name == 'php') {
        send_php_mail($user->email, $user->username, $general->efrom, $email_template->subj, $message);
    } else if ($config->name == 'smtp') {
        send_smtp_mail($config, $user->email, $user->username, $general->efrom, $general->sitetitle, $email_template->subj, $message);
    } else if ($config->name == 'sendgrid') {
        send_sendgrid_mail($config, $user->email, $user->username, $general->efrom, $general->sitetitle, $email_template->subj, $message);
    } else if ($config->name == 'mailjet') {
        send_mailjet_mail($config, $user->email, $user->username, $general->efrom, $general->sitetitle, $email_template->subj, $message);
    }
}

function send_php_mail($receiver_email, $receiver_name, $sender_email, $subject, $message)
{
  
    $general = GeneralSetting::first();
    $headers = "From: $general->sitename <$sender_email> \r\n";
    $headers .= "Reply-To: $receiver_name <$receiver_email> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    @mail($receiver_email, $subject, $message, $headers);
}

function send_smtp_mail($config, $receiver_email, $receiver_name, $sender_email, $sender_name, $subject, $message)
{
       
    $f = fsockopen($config->host, $config->port);
    if ($f !== false) {
        $res = fread($f, 1024);
        if (strlen($res) > 0 && strpos($res, '220') === 0) {
            $mail_val = [
                'send_to_name' => $receiver_name,
                'send_to' => $receiver_email,
                'email_from' => $sender_email,
                'email_from_name' => $sender_name,
                'subject' => $subject,
            ];
            Config::set('mail.driver', $config->driver);
            Config::set('mail.from', $config->username);
            Config::set('mail.name', $sender_name);
            Config::set('mail.host', $config->host);
            Config::set('mail.port', $config->port);
            Config::set('mail.username', $config->username);
            Config::set('mail.password', $config->password);
            Config::set('mail.encryption', $config->enc);
            $xx = Mail::send('partials.email', ['body' => $message], function ($send) use ($mail_val) {
                $send->from($mail_val['email_from'], $mail_val['email_from_name']);
                $send->replyto($mail_val['email_from'], $mail_val['email_from_name']);
                $send->to($mail_val['send_to'], $mail_val['send_to_name'])->subject($mail_val['subject']);
            });
        }
    }
    
}

function send_sendgrid_mail($config, $receiver_email, $receiver_name, $sender_email, $sender_name, $subject, $message)
{
       
    
    require 'core/app/Http/Helpers/Lib/Sendgrid/vendor/autoload.php';


    $sendgridMail = new \SendGrid\Mail\Mail();
    $sendgridMail->setFrom($sender_email, $sender_name);
    $sendgridMail->setSubject($subject);
    $sendgridMail->addTo($receiver_email, $receiver_name);
    $sendgridMail->addContent("text/html", $message);
    $sendgrid = new \SendGrid($config->appkey);
    try {
        $response = $sendgrid->send($sendgridMail);
    } catch (Exception $e) {
        // echo 'Caught exception: '. $e->getMessage() ."\n";
    }
}

function send_mailjet_mail($config, $receiver_email, $receiver_name, $sender_email, $sender_name, $subject, $message)
{
   
    require 'core/app/Http/Helpers/Lib/Mailjet/vendor/autoload.php';
    $mj = new \Mailjet\Client($config->public_key, $config->secret_key, true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $sender_email,
                    'Name' => $sender_name,
                ],
                'To' => [
                    [
                        'Email' => $receiver_email,
                        'Name' => $receiver_name,
                    ]
                ],
                'Subject' => $subject,
                'TextPart' => "",
                'HTMLPart' => $message,
            ]
        ]
    ];
    $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
}


function send_sms($user, $type, $shortcodes = [])
{
    
    $general = GeneralSetting::first(['sn', 'smsapi']);
    $sms_template = \App\SmsTemplate::where('act', $type)->where('sms_status', 1)->first();
    if ($general->sn == 1 && $sms_template) {
        $template = $sms_template->sms_body;
        foreach ($shortcodes as $code => $value) {
            $template = shortcode_replacer('{{' . $code . '}}', $value, $template);
        }
        $template = urlencode($template);
        $message = shortcode_replacer("{{number}}", $user->mobile, $general->smsapi);
        $message = shortcode_replacer("{{message}}", $template, $message);
        $result = @file_get_contents($message);
    }
}

function activeTemplate($asset = false)
{
    $template = '';
    if (session()->has('active_template')) {
        $template = session('active_template');
    } else {
        $gs = GeneralSetting::first(['active_template']);
        $template = $gs->active_template;
        session()->put(['active_template' => $template]);
    }
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function recaptcha()
{
    $recaptcha = \App\Plugin::where('act', 'google-recaptcha3')->where('status', 1)->first();
    return $recaptcha ? $recaptcha->generateScript() : '';
}


function googleAnalysis()
{
    $analytics = \App\Plugin::where('act', 'google-analytics')->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function twakchat()
{
    $tawkchat = \App\Plugin::where('act', 'tawk-chat')->where('status', 1)->first();
    return $tawkchat ? $tawkchat->generateScript() : '';
}
function moneda($valor){
    switch($valor)
    {
        case 1: $res = "ETH"; break;
        case 2: $res = "TRX"; break;
        default: $res= "ETH";
    }

    return $res;
}
function recaptcha_validate($response)
{
    $recaptcha = \App\Plugin::where('act', 'google-recaptcha3')->where('status', 1)->first();
    if (!$recaptcha) return true;
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $secret_key = $recaptcha->shortcode->secretkey->value;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $verify_url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "secret=$secret_key&response=$response");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $capthca_response = curl_exec($curl);
    curl_close($curl);
    return json_decode($capthca_response);
}


function membresia_activa($id){
    $fecha =  $date = Carbon::now()->subYear()->toDateString();
    $paq = patentes::where('user_id',$id)->where('created_at','>=',$fecha )->orderBy('precio', 'Desc')->first();
    return $paq;
}

function member($id){
        
    $membresia ='';

}

function getTrx()
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 12; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function remove_element($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet, $amount, $crypto = null)
{
    $varb = $wallet . "?amount=" . $amount;
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$varb&choe=UTF-8";
}

function curlContent($url)
{
    //open connection
    $ch = curl_init();
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //execute post
    $result = curl_exec($ch);
    //close connection
    curl_close($ch);
    return $result;
}


function printEmail($email)
{
    $beforeAt = strstr($email, '@', true);
    $withStar = substr($beforeAt, 0, 2) . str_repeat("**", 5) . substr($beforeAt, -2) . strstr($email, '@');
    return $withStar;
}


function getUserById($id)
{
    return User::find($id);
}


///////////////////////////////////////// MLM FUNCTION //


function createBVLog($user_id, $lr, $amount, $details)
{
    $bvlog = new App\BvLog();
    $bvlog->user_id = $user_id;
    $bvlog->position = $lr;
    $bvlog->amount = $amount;
    $bvlog->details = $details;
    $bvlog->save();
}


function mlmWidth()
{
    return config('constants.mlm.width');
}

function khaliPosition($user_id)
{
    for ($i = 1; $i <= mlmWidth(); $i++) {
        $check = User::where('pos_id', $user_id)->where('position', $i)->count();
        if ($check == 0) {
            return $i;
        }
    }
}

function khaliAcheNaki($user_id)
{
    $count = User::where('pos_id', $user_id)->count();
    if ($count < mlmWidth()) {
        return khaliPosition($user_id);
    } else {
        return 0;
    }
}

function showPositionBelow($id)
{
    $arr = array();
    $under_ref = User::where('pos_id', $id)->get();
    foreach ($under_ref as $u) {
        array_push($arr, $u->id);
    }
    return $arr;
}


function getPosition($parentid, $position)
{
    $childid = getTreeChildId($parentid, $position);
    if ($childid != "-1") {
        $id = $childid;
    } else {
        $id = $parentid;
    }
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $nextchildid = getTreeChildId($id, $position);
            if ($nextchildid == "-1") {
                break;
            } else {
                $id = $nextchildid;
            }
        } else break;
    }
    $res['pos_id'] = $id;
    $res['position'] = $position;
    return $res;
}

function getTreeChildId($parentid, $position)
{
    $cou = User::where('pos_id', $parentid)->where('position', $position)->count();
    $cid = User::where('pos_id', $parentid)->where('position', $position)->first();
    if ($cou == 1) {
        return $cid->id;
    } else {
        return -1;
    }
}


////-------------->>>>>>>> UPTO ROOT

function isUserExists($id)
{
    $user = User::find($id);
    if ($user) {
        return true;
    } else {
        return false;
    }
}

function getPositionId($id)
{
    $user = User::find($id);
    if ($user) {
        return $user->pos_id;
    } else {
        return 0;
    }
}




function getPositionLocation($id)
{
    $user = User::find($id);
    if ($user) {
        return $user->position;
    } else {
        return 0;
    }
}


function updateFreeCount($id)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
           
            if ($posid == "0") {
                break;
            }
           
            $position = getPositionLocation($id);
            $extra = UserExtra::where('user_id', $posid)->first();
            if($extra){
                if ($position == 1) {
                    $extra->free_left += 1;
                } else {
                    $extra->free_right += 1;
                }
                $extra->save();
                $id = $posid;}
            else{
                break;
            }
        } else {
            break;
        }
    }//while
}


function updatePaidCount($id)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();

            if ($position == 1) {
                $extra->free_left -= 1;
                $extra->paid_left += 1;
            } else {
                $extra->free_right -= 1;
                $extra->paid_right += 1;
            }
            $extra->save();


            $id = $posid;
        } else {
            break;
        }
    }//while
}

function send_tele($user, $mensaje){

    if($user->chat_id != 0){
            $data['chat'] = $user->chat_id;
            $data['msg']  = $mensaje;
            $url = 'http://localhost:3000/send_sms';
            $res = curlpost($url, $data);
    }
}

function pay_unilevel($id, $monto, $by){
     
    $ref = $id;
    for($i=0; $i<3; $i++){
       if($ref < 1) break;
        $user =  User::where('id', $ref)->first();
        $socio_activo = socio_activo($user->id); 

        if($socio_activo > 0){
                $por = porce_level($i+1);
                $gana = ($monto * $por) / 100;
                $user->usdt += $gana;
                $user->save(); 
                $ref = $user->ref_id;
                $titulo = 'Comisión del '.$por.'% por valor parcitipativo de '.$by.' '.round($monto,4).' usdt';
                earning($user,$gana,$titulo, 'unilevel');
                send_tele($user,'Ha recibido '.round($gana,4).' USD por concepto de bono participativo  del socio: '.$by.', nivel: '.trim($i+1)).' ';
        }
    }
}

function porce_level($i){
    switch($i){
            case 1: $res = 3; break;
            case 2: $res = 2; break;
            case 3: $res = 1; break;
            default: $res = 0;
    }
    return $res;
}


function directo_activos($user_id, $position){
    $user =  User::where('ref_id', $user_id)->where('position',$position)->get();
    $total = 0;
    $i=0;
    foreach($user as $data){
         $total += socio_activo($data->id);
         $i++;
    }
    return $total;
}

function binary_refer($id, $user_id, $bv, $details, $inversion = 0, $puede = 0){
               
                 $posid = $user_id;
                 $pago = $bv;
                 $diferencia = $pago; 
                 $porc = 8;
    
            $posUser = User::find($posid);
            // total contractos 
            $total_co = Share::where('user_id',$posid)->where('status','<>','2')->count();

            $total_left  =  directo_activos($posid,1);
            $total_right =  directo_activos($posid,2);
            $position = getPositionLocation($id);

            $paso = 0;
            if($position == 1)
            {
                if( $total_left > 0)
                     $paso = 1;
                 else
                    $paso = 0;
            }else{
                if( $total_right > 0)
                    $paso = 1;
                else
                   $paso = 0;
            }
         
                    if($total_co > 0 && $paso > 0 && $posUser->gen_binary == 1)
                    {
                                if ($posUser->account_type == 1) {
                                    $extra = UserExtra::where('user_id', $posid)->first();
                                    $bvlog = new \App\BvLog();
                                    $bvlog->user_id = $posid;
                    
                                    if ($position == 1) {
                                        $extra->bv_left += $pago;
                                        $extra->bv_history_left += $pago;
                                        $extra->left_retenido += $diferencia;
                                        $bvlog->position = '1';
                                    } else {
                                        $extra->bv_right += $pago;
                                        $extra->bv_history_right += $pago;
                                        $extra->right_retenido += $diferencia;
                                        $bvlog->position = '2';
                                    }
                                    $extra->save();
                                    $bvlog->amount = $pago;
                                     $bvlog->porcentaje = $porc;
                                    $bvlog->retenido = $diferencia;
                                    $bvlog->details = $details;
                                    $bvlog->save();
                                }
                    }
}



function updateBV($id, $bv, $details)
{
    $i = 0;
    $bv = $bv;
    $ref =  User::where('id', $id)->first();
    
    while ($id != "" || $id != "0") {
         
        if (isUserExists($id)) {
           $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
             $pago =  $bv;
    
            $posUser = User::find($posid);
        
            $total_co = socio_activo($posid);
            $position = getPositionLocation($id);
        
                    if($total_co > 0 && $posUser)
                    {
                               
                                    $extra = UserExtra::where('user_id', $posid)->first();
                                    $bvlog = new \App\BvLog();
                                    $bvlog->user_id = $posid;
                    
                                    if ($position == 1) {
                                        $extra->bv_left += $pago;
                                        $extra->bv_history_left += $pago;
                                        $bvlog->position = '1';
                                    } else {
                                        $extra->bv_right += $pago;
                                        $extra->bv_history_right += $pago;
                                        $bvlog->position = '2';
                                    }
                                    $extra->save();
                                    $bvlog->amount = $pago;                           
                                    $bvlog->details = $details;
                                    $bvlog->save();
                            
                    }
            
           
            $id = $posid;
        } else {
            break;
        }
    }//while

}




function str_slug($title = null)
{
    return \Illuminate\Support\Str::slug($title);
}

function str_limit($title = null, $length = 10)
{
    return \Illuminate\Support\Str::limit($title, $length);
}


function monto_binary($puntos){
   
    return number_format($puntos,4,'.','');
}





//// compensation referral Commission


function trx_tran_binay($lmt,  $refer, $details){
       
                   $res = getTrx();
                    $refer->transactions()->create([
                        'trx' => $res,
                        'user_id' => $refer->id,
                        'amount' => $lmt,
                        'main_amo' => $lmt,
                        'balance' => $refer->interest_wallet,
                        'title' => $details,
                        'charge' => 0,
                        'moneda' => $moneda,
                        'type' => 'binary_comission',
                    ]);
    
        return $res;
}



function trx_tran($lmt,  $refer, $details){
     
                   $res = getTrx();
                    $refer->transactions()->create([
                        'trx' => $res,
                        'user_id' => $refer->id,
                        'amount' => $lmt,
                        'main_amo' => $lmt,
                        'balance' => $refer->interest_wallet,
                        'title' => $details,
                        'charge' => 0,
                        'moneda' => $moneda,
                        'type' => 'referral_commision',
                    ]);

        return $res;
}


function transaccion($user, $monto, $title, $type){
     $res             = getTrx();
     $trans           =  new trx();
     $trans->trx      = $res;
     $trans->user_id  = $user->id;
     $trans->amount   = $monto;
     $trans->main_amo = $monto;
     $trans->balance  = $user->balance;
     $trans->title    = $title;
     $trans->type     = $type;
     $trans->moneda   = 1;
     $trans->save();
}

function earning($user, $monto, $title, $type){

    $res             = getTrx();
    $trans           =  new trx();
    $trans->trx      = $res;
    $trans->user_id  = $user->id;
    $trans->amount   = $monto;
    $trans->main_amo = $monto;
    $trans->balance  = $user->usdt;
    $trans->title    = $title;
    $trans->type     = $type;
    $trans->moneda   = 1;
    $trans->save();

}


function getPositionUser($id, $position)
{
    return User::where('pos_id', $id)->where('position', $position)->first();
}

function showTreePage($id)
{
    $res = array_fill_keys(array('b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o'), null);
    $res['a'] = User::find($id);

    $res['b'] = getPositionUser($id, 1);
    if ($res['b']) {
        $res['d'] = getPositionUser($res['b']->id, 1);
        $res['e'] = getPositionUser($res['b']->id, 2);
    }
    if ($res['d']) {
        $res['h'] = getPositionUser($res['d']->id, 1);
        $res['i'] = getPositionUser($res['d']->id, 2);
    }
    if ($res['e']) {
        $res['j'] = getPositionUser($res['e']->id, 1);
        $res['k'] = getPositionUser($res['e']->id, 2);
    }
    $res['c'] = getPositionUser($id, 2);
    if ($res['c']) {
        $res['f'] = getPositionUser($res['c']->id, 1);
        $res['g'] = getPositionUser($res['c']->id, 2);
    }
    if ($res['f']) {
        $res['l'] = getPositionUser($res['f']->id, 1);
        $res['m'] = getPositionUser($res['f']->id, 2);
    }
    if ($res['g']) {
        $res['n'] = getPositionUser($res['g']->id, 1);
        $res['o'] = getPositionUser($res['g']->id, 2);
    }
    return $res;
}


function new_renglon($i, $total, $user, $monto){
    unset($data);
    if($i == 20){if($total > 25){$data['user'] = $user;$data['monto'] = $monto; $data = json_encode($data);                          
    }else{$data = "";}}else{$data = "";} return $data;
}









function imagen_m($user){

    $data = User::where("id",$user)->first();
    $imagen = $data->image;
    $default = "assets/images/default.png";
    $path    = "assets/images/user/profile/".$imagen;
    if($imagen == "") 
           $imagen =   $default;
    else {
        
      if(file_exists(asset($path))){
           $imagen = $path; 
      }else{
          $imagen =   $default;
      }
    }
    return $imagen;
}


function img_paq($i){
     
     switch($i){
                case 1 : $res = '30_a.png'; break;
                case 2 : $res = '100_a.png'; break;
                case 3 : $res = '500_a.png'; break;
     }
     return $res;
}

function showSingleUserinTree($user)
{
    $res = '';
    if ($user) {
        if ($user->plan_id == 0) {
            $userType = "free-user";
            $stShow = "Free";
            $planName = '';
        } else {
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = $user->plan->name;
        }

        //valida el paquete
        $activo = socio_activo($user->id);

        if($activo > 0){
               $paq = paq_activo($user->id);
               $img_p = img_paq($paq->member_id);
               $img = get_image(config('constants.image.member') . '/' . $img_p);
        }else{
               $img = get_image(config('constants.user.profile.path') . '/' . $user->image);
        }
        

        $resp = user::where('id',$user->ref_id)->first('username');
        $refby = $resp->username;

        if (auth()->guard('admin')->user()) {
            $hisTree = route('admin.users.other.tree', $user->username);

        } else {
            $hisTree = route('user.other.tree', $user->username);
        }

        $total_paid = Pointpaid::where('user_id', $user->id)->sum('point');
          
          $his_left = $total_paid + @$user->user_extra->bv_left;
          $his_right = $total_paid + @$user->user_extra->bv_right;
            
            

        $extraData = " data-name=\"$user->username\"";
        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-rankname=\"" . @$user->rank->name . "\"";
        $extraData .= " data-lpaid=\"" . @$user->user_extra->paid_left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->user_extra->paid_right . "\"";
        $extraData .= " data-lfree=\"" . @$user->user_extra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->user_extra->free_right . "\"";
        $extraData .= " data-lbv=\"" . formatter_money(@$user->user_extra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . formatter_money(@$user->user_extra->bv_right) . "\"";
         $extraData .= " data-lbvh=\"" . formatter_money(@$total_paid + @$user->user_extra->bv_left ) . "\"";
        $extraData .= " data-rbvh=\"" . formatter_money(@$total_paid+  @$user->user_extra->bv_right) . "\"";
        $res .= "<div class=\"user2 showDetails\" type=\"button\" $extraData>";
        $res .= "<span>";


        if ( haveBothSide($user->id, 1))
        {
            $estado = 'style="border:2px solid #0f0"';
        }else{
            $estado = 'style="border:2px solid #f00"';
        }
      
        $res .= "<img ".$estado." src=\"$img\" alt=\"*\"  class=\"$userType\">";
        $res .= "</span>";
        $res .= "<p class=\"user-name\">$user->username</p>";

    } else {
        $img = get_image('assets/images/nouser.png');

        $res .= "<div class=\"user2\" type=\"button\">";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
      //  $res .= "<p class=\"user-name\">No User</p>";
    }
    $res .= " </div>";
    $res .= " <span class=\"line\"></span>";
    return $res;
}



function showSingleUserinTree_ini($user)
{
    $res = '';
    if ($user) {
        if ($user->plan_id == 0) {
            $userType = "free-user";
            $stShow = "Free";
            $planName = '';
        } else {
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = $user->plan->name;
        }

        $activo = socio_activo($user->id);

        if($activo > 0){
               $paq = paq_activo($user->id);
               $img_p = img_paq($paq->member_id);

               $img = get_image(config('constants.image.member') . '/' . $img_p);
        }else{
               $img = get_image(config('constants.user.profile.path') . '/' . $user->image);
        }




        $resp = user::where('id',$user->ref_id)->first('username');
        $refby = @$resp->username;
        
   

        if (auth()->guard('admin')->user()) {
            $hisTree = route('admin.users.other.tree', $user->username);

        } else {
            $hisTree = route('user.other.tree', $user->username);
        }

         $total_paid = Pointpaid::where('user_id', $user->id)->sum('point');
          
          $his_left = $total_paid + @$user->user_extra->bv_left;
          $his_right = $total_paid + @$user->user_extra->bv_right;
            
            

        $extraData = " data-name=\"$user->username\"";
        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-rankname=\"" . @$user->rank->name . "\"";
        $extraData .= " data-lpaid=\"" . @$user->user_extra->paid_left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->user_extra->paid_right . "\"";
        $extraData .= " data-lfree=\"" . @$user->user_extra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->user_extra->free_right . "\"";
        $extraData .= " data-lbv=\"" . formatter_money(@$user->user_extra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . formatter_money(@$user->user_extra->bv_right) . "\"";
         $extraData .= " data-lbvh=\"" . formatter_money(@$user->user_extra->bv_history_left ) . "\"";
        $extraData .= " data-rbvh=\"" . formatter_money(@$user->user_extra->bv_history_right) . "\"";
        
        $res .= "<div class=\"user2 showDetails2\"  type=\"button\" $extraData>";
        $res .= "<span >";
        if ( haveBothSide($user->id, 1))
        {
            $estado = 'style="border:2px solid #0f0"';
        }else{
            $estado = 'style="border:2px solid #f00"';
        }

        $res .= "<img  ".$estado." src=\"$img\" alt=\"*\"  class=\"$userType\">";
        $res .= "</span>";
        $res .= "<p class=\"user-name\">$user->username</p>";

    } else {
        $img = get_image('assets/images/nouser.png');

        $res .= "<div class=\"user2\" type=\"button\">";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
       // $res .= "<p class=\"user-name\">No User s</p>";
    }
    $res .= " </div>";
    $res .= " <span class=\"line\"></span>";
    return $res;

}

//////////////////////// TREE AUTH
function treeAuth($whichID, $whoID)
{

    if ($whichID == $whoID) {
        return true;
    }

    $formid = $whichID;

    while ($whichID != "" || $whichID != "0") {
        if (isUserExists($whichID)) {
            $posid = getPositionId($whichID);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($whichID);
            if ($posid == $whoID) {
                return true;
            }
            $whichID = $posid;
        } else {
            break;
        }
    }//while
    return 0;
}






function get_appear_label($id)
{
    $rank = \App\Rank::where('id', '<', $id)->orderBy('id', 'desc')->first();

    if ($rank) {
        return '2 DIRECT ' . $rank->name;
    } else {
        return 'none';
    }


}

//ref




function updateTopRank($id, $rank_id)
{

    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);

            if ($posid == "0") {
                break;
            }

            $position = getPositionLocation($id);
            $posUser = User::find($posid);
            if (!$posUser) {
                break;
            }

            if ($position == 1) {
                //left
                if ($posUser->top_left < $rank_id) {
                    $posUser->top_left = $rank_id;
                    $posUser->save();
                }
            } else {
                //right
                if ($posUser->top_right < $rank_id) {
                    $posUser->top_right = $rank_id;
                    $posUser->save();
                }
            }
            $id = $posid;
        } else {
            break;
        }
    }//while
    return 0;

}





function ceros($valor){
    $t = strlen($valor);
    switch($t){
        case 1: $r= '00000'.$valor; break; 
        case 2: $r= '0000'.$valor; break;
        case 3: $r= '000'.$valor; break; 
        case 4: $r= '00'.$valor; break; 
        case 5: $r= '0'.$valor; break; 
        default: $r = $valor;
    }
      return $r;
}



function transaction_log($data,$user){
    $res = getTrx();
    $tr =  json_decode(json_encode($data));
    
    $user->transactions()->create([
        'trx'      => $res,
        'user_id'  => $user->id,
        'amount'   => round(@$tr->amount,8),
        'main_amo' => round(@$tr->main,8),
        'charge'   => round(@$tr->charge,8),
        'balance'  => @$tr->balance,
        'title'    => @$tr->details,
        'amount_con'   => round(@$tr->amount_con,8),
        'main_amo_con' => round(@$tr->main_amo_con,8),
        'charge_con'   => round(@$tr->charge_con,8),
        'hash'     => @$tr->hash,
        'moneda'   => @$tr->moneda,
        'type'     => @$tr->type,
    ]);
}

function tipo_bono($string){
    
    switch($string){
         case 'referral_commision': $res = "Bono referido"; break;
         case 'binary_comission': $res = "Bono binario"; break;
         default: $res = $string;
    }

    return $res;

}

function pago_rank($user_id, $rank){
     $resu = rankpaid::where('user_id', $user_id)->where('rand_id', $rank)->count();
     if($resu == null)
     $retorna = 0;
     else
     $retorna = 1;
     return $retorna;
}

function trx_compra($user, $amount, $title, $moneda){

    $trxnum = getTrx();
    $trx = new Trx();
    $trx->user_id = $user->id;
    $trx->amount = $amount;
    $trx->charge = 0;
    $trx->main_amo = $amount;
    $trx->balance = $balance;
    $trx->type = $type;
    $trx->trx = $trxnum;
    $trx->moneda = $moneda;
    $trx->title = $title;
    $trx->origen = 1;
    $trx->save();
}


function puntos_pagados($userid, $puntos){

     $paid = new Pointpaid();
     $paid->user_id       = $userid;
     $paid->point         = $puntos;
     $paid->fecha_pagado  = date('Y');
     $paid->save();
}

function haveBothSide($userid, $active = null)
{
    if ($active) {
        $left = directo_activos($userid, 1);
        $right = directo_activos($userid, 2);
        if ($left > 0 && $right > 0) {
            return true;
        }
        return false;
    }

    $left = \App\User::where('ref_id', $userid)->where('position', 1)->count();
    $right = \App\User::where('ref_id', $userid)->where('position', 2)->count();
    if ($left > 0 && $right > 0) {
        return true;
    }
    return false;

}

function binary_activo($id){
    
       $izq = directo_activos($id, 1);
       $der = directo_activos($id, 2);
       if($izq > 0 && $der > 0){
           return 1;
       }else{
        return 0;
       }

}

function mesDia($fecha){
      
    $cor = explode('-', $fecha);
    
    $mes_n = array('01'=>'Ene', 
                   '02'=>'Feb', 
                   '03'=>'Mar', 
                   '04'=>'Abr' , 
                   '05'=>'May', 
                   '06' => 'Jun', 
                   '07'=>'Jul', 
                   '08'=>'Ago',
                   '09'=>'Sep',
                   '10' => 'Oct',
                   '11' => 'Nov',
                   '12' => 'Dic'
);

   return $mes_n[$cor[1]].' - '.$cor[2];
}





