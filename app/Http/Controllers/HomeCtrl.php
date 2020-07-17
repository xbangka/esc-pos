<?php

namespace App\Http\Controllers;

use App\Http\Models\Users,
    App\Http\Models\Config_General,
    App\Http\Models\Products,
    Illuminate\Http\Request;

class HomeCtrl extends Controller
{
    private $js;

    public function __construct(){
        $version = config('app.env')=='local' ? '_dev' : '';

        $this->js = array(
                            'axios.min.js',
                            'vue'.$version.'.js',
                            'crypto-js.js',
                            'Encryption.js',
                            'sweetalert2.all.min.js',
                            'login.js'
                        );
    }

    public function _login(Request $request)
    {   //echo \DNS1D::getBarcodeSVG("11132000240", "EAN13", 2,40);exit;
        //echo \DNS1D::getBarcodeSVG("610064", "EAN8", 2,40);exit;

        try{
        
            if(!$request->session()->has('Login_Page')) {
                $request->session()->put('Login_Page',uniqid());
                $err['code'] = 403;
                $err['message'] = 'forbidden';
                return response($err,403)->header('Content-Type', 'application/json');
            }

            $request->session()->forget('Login_Page');

            $cg_email_allow = \Cache::remember('email_allow', 86400, function()
            {
                return Config_General::select('value')->where(['key' => 'email_allow', 'status' => 1])->first();
            });
            
            $email_allow = ($cg_email_allow) ? $cg_email_allow->value : '[]';

            $key_cross = '_'.uniqid();
            $request->session()->forget('key_cross');
            $request->session()->put('key_cross',$key_cross);

            $appid  = substr(date('D'),0,1).sha1( uniqid() );

            $param['appid']         = $appid;
            $param['csrf_token']    = csrf_token();
            $param['signin']        = url('signin');
            $param['dashboard']     = url('dashboard');
            $param['key_salt']      = $key_cross;
            $param['email_allow']   = $email_allow;

            $data['filejs'] = view_js($this->js, $param, $request);
            $data['app']    = $appid;

            return view('front.vlogin',$data);
        }catch(\Exception $e){
            if(config('app.debug')){
                return $e->getMessage();
            }else{
                $err['code'] = 500;
                $err['message'] = 'Error Exception in try action';
                return response($err,500)->header('Content-Type', 'application/json');
            }
        }
    }
    

    public function _signin(Request $request){
        if($request->session()->has('key_cross')) {
            $key_cross = $request->session()->get('key_cross');
        }else{
            return 'Halaman telah kadaluarsa, silakan muat ulang';
        }

        $_q = $request->input('_q');
        $_hash = $request->input('_hash');
        
        $validator = app()->make('validator');

        $validate = $validator->make($request->all(), [
                '_q' => 'required'
            ]
        );
        if ($validate->fails()) return 'Perhatikan, Inputan perlu di isi dengan benar !';

        // decript
        $Encryption = new \Encryption();
        $_q = $Encryption->decrypt($_q, $key_cross);
        $_q = json_decode($_q,false);
        $userInput = $_q->email;
        $passInput = $_q->password;
        $pass_hash = hash('sha256',$passInput);
        if($pass_hash!==$_hash) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
        // return \Hash::make($passInput);

        // check email domain is allow
        if(!filter_var($userInput, FILTER_VALIDATE_EMAIL)) return 'Mohon format email di isi dengan benar !';

        $cg_email_allow = \Cache::remember('email_allow', 86400, function()
        {
            return Config_General::select('value')->where(['key' => 'email_allow', 'status' => 1])->first();
        });

        $email_allow = ($cg_email_allow) ? $cg_email_allow->value : '[]';

        $email_allow = json_decode($email_allow,false);

        $dx = explode("@",$userInput);

        $domainemail = end($dx);

        if(!in_array($domainemail, $email_allow)) return 'Email tidak di rekomendasikan';

        $user = Users::where(['email' => email_Encode($userInput),'status' => 1])->first();
        
        if($user){
            
            $data               = (object)[];
            $data->id           = $user->id;
            $data->firstname    = str_Decode($user->firstname);
            $data->lastname     = str_Decode($user->lastname);
            $data->nameshow     = str_Decode($user->nameshow);
            $data->username     = str_Decode($user->username);
            $data->email        = email_Decode($user->email);
            $data->phone        = str_Decode($user->phone);
            $data->image_path   = $user->image_path;
            $data->image_file   = $user->image_file;
            $data->status       = $user->status;
            $data->statuses     = isset($user->statuses) ? $user->statuses : false;
            $data->store_id     = isset($user->stores) ? $user->stores->id : '';
            $data->store_code   = isset($user->stores) ? $user->stores->code : '';
            $data->store_name   = isset($user->stores) ? str_Decode($user->stores->name) : 'none';
            $data->store_phone  = isset($user->stores) ? str_Decode($user->stores->phone) : '+62';
            $data->store_address= isset($user->stores) ? str_Decode($user->stores->address) : '-';
            
            $hashChecked = \Hash::check($passInput, $user->password);
            
            if($hashChecked){
                
                $request->session()->forget('key_cross');
                $request->session()->forget('xtoken');

                $xtoken = $request->server('HTTP_USER_AGENT');
                $xtoken = \Hash::make($xtoken);
                $request->session()->put('xtoken',$xtoken);

                $request->session()->forget('udata');
                $request->session()->put('udata',$data);

                return '*OK*';
            }
            return 'Kombinasi email dan password tidak sesuai,';
        }
        return 'Kombinasi email dan password tidak sesuai.';
    }

    public function get_barcode_image(Request $request){
        $requests = $request->all();
        $type = $request->input('type',false);
        $code = '';
        foreach ($requests as $key => $row) {
            if( is_numeric($key) ){
                $code = $key;
                break;
            }else{
                return ;
            }
        }

        $existpng = \Storage::disk('public')->exists("barcodepng/$code.png");
        if(!$existpng){
            try{
                $png = \DNS1D::getBarcodePNG($code, "EAN13", 2.7, 80);
                \Storage::disk('public')->put("barcodepng/$code.png", base64_decode($png));
            }catch(\Exception $e){
                //
            }
        }

        if($type){
            if($type=='png' && $existpng){
                return \Storage::disk('public')->download("barcodepng/$code.png");
            }
        }

        $exist = \Storage::disk('local')->exists("barcodes/$code.svg");
        if($exist){
            return \Storage::disk('local')->get("barcodes/$code.svg");
        }else{
            try{
                $svg = \DNS1D::getBarcodeSVG($code, "EAN13", 2, 40);
                $svg = removebetween($svg,'<?xml','.dtd">');
                $svg = html_compress($svg);
                \Storage::disk('local')->put("barcodes/$code.svg",$svg);
                return $svg;
            }catch(\Exception $e){
                return '';
            }
        }
    }

    public function printBarcode(Request $request)
    {
        try{
            $code = $request->input('code',false);

            if(!$code) return;

            $token = $this->_get_token_barcode_generator();

            if(!$token) return;

            $product = Products::where('barcode', '=', $code)->first();
            if(!$product) return;

            if( $request->session()->has('udata') ) {
                $udata = $request->session()->get('udata');
                if(!isset($udata->store_code)) return;
                $store_code = $udata->store_code;
            }else{
                return;
            }

            $responses = [];

            $obj0 = (object)[];
            $obj1 = (object)[];
            $obj2 = (object)[];
            $obj3 = (object)[];

            $obj0->type 	= 0;
            $obj0->content 	= $product->full_name;
            $obj0->bold 	= 0;
            $obj0->align 	= 0;
            $obj0->format 	= 0;
            array_push($responses,$obj0);

            $obj1->type 	= 1;
            $obj1->path 	= 'https://www.terryburton.co.uk/barcodewriter/generator/imagegen?action=png&encoder=ean13&data='.$code.'&options=includetext%20guardwhitespace&scale_x=3.2&scale_y=2.4&rotate=0&csrf_token='.$token;
            $obj1->align 	= 0;
            array_push($responses,$obj1);

            if(count($product->product_prices)>=1){
                $obj2->type 	= 0;
                $obj2->content 	= '';
                $obj2->bold 	= 0;
                $obj2->align 	= 0;
                array_push($responses,$obj2);

                foreach ($product->product_prices as $row) {
                    $obj_ = (object)[];
                    $obj_->type 	= 0;
                    $obj_->content 	= 'Rp. '.format_number($row->price);
                    $obj_->bold 	= 0;
                    $obj_->align 	= 2;
                    $obj_->format 	= 3;
                    array_push($responses,$obj_);
                }
            }

            $obj3->type 	= 0;
            $obj3->content 	= ' <br /> ';
            $obj3->bold 	= 0;
            $obj3->align 	= 0;
            array_push($responses,$obj3);
            
            $json_obj = json_encode($responses,JSON_FORCE_OBJECT);
            $json_obj = str_replace('\/','/',$json_obj);
            
            $encryption = new \Encryption();
	
            $string = $encryption->encrypt($json_obj, 'esc-pos');

            return $string;

        }catch(\Exception $e){
            return (config('app.debug')) ? /*$e.''*/ $e->getMessage() : 'Error Exception in try action';
        }
    }

    public function get_price_references(Request $request){
        try{
            $requests = $request->all();
            $code = '';
            foreach ($requests as $key => $row) {
                if( is_numeric($key) ){
                    $code = $key;
                    break;
                }else{
                    $responses['code'] = 403;
                    $responses['message'] = 'Forbiden';
                    $responses['data'] = '';
                    return $responses;
                }
            }
            $price = [];
            $responses['code'] = 200;
            $responses['message'] = 'OK';
            
            /*
            ** Get Products Name
            */
            $product = \Cache::remember('name_products_'.$code, 86400, function() use($code){
                $product = Products::select('full_name')->where('barcode', '=', $code)->first();
                if(!$product) return false;
                return $product->full_name;
            });
            
            if(!$product){
                \Cache::forget('name_products_'.$code);
                $responses['code'] = 500;
                $responses['message'] = 'barcode not define';
                $responses['data'] = '';
                return $responses;
            }
            //======================================================================================================
            
            /*
            ** Get Price From https://waorder.suzuyagroup.com
            */
            $sc = \Cache::remember('get_suzuya_price_'.$code, 432000, function() use($code){
                $sc = $this->_get_suzuya($code);
                return $sc;
            });

            if(!$sc){
                \Cache::forget('get_suzuya_price_'.$code);
            }else{
                array_push($price,$sc);
            }
            //======================================================================================================

            /*
            ** Get Price From https://id.nikmart.online
            */
            $sc = \Cache::remember('get_nikmart_online_price_'.$code, 432000, function() use($code){
                $sc = $this->_get_nikmart_online($code);
                return $sc;
            });

            if(!$sc){
                \Cache::forget('get_nikmart_online_price_'.$code);
            }else{
                array_push($price,$sc);
            }
            //======================================================================================================
            
            /*
            ** Get Price From http://harga.kpri-handayani.com
            */
            $sc = \Cache::remember('get_kpri_handayani_price_'.$code, 432000, function() use($code){
                $sc = $this->_get_kpri_handayani($code);
                return $sc;
            });

            if(!$sc){
                \Cache::forget('get_kpri_handayani_price_'.$code);
            }else{
                array_push($price,$sc);
            }
            //======================================================================================================

            /*
            ** Get Price From http://www.kopkarsentra.com
            */
            $sc = \Cache::remember('get_kopkarsentra_price_'.$code, 432000, function() use($code){
                $sc = $this->_get_kopkarsentra($code);
                return $sc;
            });

            if(!$sc){
                \Cache::forget('get_kopkarsentra_price_'.$code);
            }else{
                array_push($price,$sc);
            }
            //======================================================================================================

            /*
            ** Get Price From http://www.mosritel.com
            */\Cache::forget('get_mosritel_price_'.$code);
            $sc = \Cache::remember('get_mosritel_price_'.$code, 432000, function() use($code){
                $sc = $this->_get_mosritel($code);
                return $sc;
            });

            if(!$sc){
                \Cache::forget('get_mosritel_price_'.$code);
            }else{
                array_push($price,$sc);
            }
            //======================================================================================================

            $data['code'] = $code;
            $data['name'] = $product;
            $data['prices'] = $price;
            
            $responses['data'] = $data;

            return response($responses)->header('Content-Type', 'application/json');
        }catch(\Exception $e){
            $message = (config('app.debug')) ? $e.'' /*$e->getMessage()*/ : 'Error Exception in try action';
            $responses['code'] = 500;
            $responses['message'] = $message;
            $responses['data'] = '';
            return $responses;
        }
    }

    private function _get_suzuya($code)
    {
        try{
            $xurl   = 'https://waorder.suzuyagroup.com/ajax/getproduct.php?searchproduct='.$code;
            $oapXML = '';
            $result = getcurl($xurl, $oapXML);

            if(!$result) return false;

            $left = "class='product-price'>";
            $right = '</div>';
            $price = between($result, $left, $right);
            if($price=='' || $price=='NULL') return false;
            $price = str_replace('Rp','',$price);
            $price = str_replace('.','',$price);
            $price = trim($price);
            $responsesproduct = (object)[];
            $responsesproduct->source = 'waorder.suzuyagroup.com';
            $responsesproduct->price = (float)$price;
            return $responsesproduct;
        }catch(\Exception $e){
            return false;
        }
        
    }

    private function _get_nikmart_online($code)
    {
        try{
            $xurl   = 'https://id.nikmart.online/search-services-reader/v1/suggest/federated';
            $oapXML = '{"query":"'.$code.'","limit":10,"language":"id"}';
            $header = [];
            array_push($header, 'Host: id.nikmart.online');
            array_push($header, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0');
            array_push($header, 'Accept: application/json, text/plain, */*');
            array_push($header, 'Accept-Language: en-US,id;q=0.7,en;q=0.3');
            array_push($header, 'Accept-Encoding: gzip, deflate, br');
            array_push($header, 'Content-Type: application/json;charset=utf-8');
            array_push($header, 'Authorization: cJpLeUZSo0hvb5-bZQzWeh0wK54xStEqTcQcZF-KlfI.eyJpbnN0YW5jZUlkIjoiMWYxZTY0ZGItOTUxZi00MjQyLWJhYjAtNjljNmYwNjJhYjM3IiwiYXBwRGVmSWQiOiIxNDg0Y2I0NC00OWNkLTViMzktOTY4MS03NTE4OGFiNDI5ZGUiLCJtZXRhU2l0ZUlkIjoiMWFhM2ZhYjItMDczOC00NjVjLWEzMTQtMGVkYTczM2M1ZGE0Iiwic2lnbkRhdGUiOiIyMDIwLTA2LTExVDA2OjUzOjM0LjE0MVoiLCJkZW1vTW9kZSI6ZmFsc2UsImFpZCI6IjQ4Y2IxMTNlLTdkZTQtNDU2MS1hOGFjLWU4YWYyNzQ3N2E5OCIsImJpVG9rZW4iOiIwNWJkOWU2OS05MjI3LTA0MWUtMTlhNC02NzFjODM1ZWY2OTMiLCJzaXRlT3duZXJJZCI6IjBmYTQ3ZTc5LTgyODktNGMwZS05YzU5LWYxZDdlYTUyYzViYyJ9');
            array_push($header, 'Content-Length: 52');
            array_push($header, 'Origin: https://id.nikmart.online');
            array_push($header, 'Connection: keep-alive');
            array_push($header, 'Referer: https://id.nikmart.online/_partials/wix-bolt/1.6050.0/node_modules/viewer-platform-worker/dist/wixcode-worker.js');
            array_push($header, 'Cookie: svSession=af884f4f3d641ab3b18f68a75443449e8defa569e0d6dd7916ac3546369b783ca1cdcd2a5de1ecb8d0b2ea6917eecb9d1e60994d53964e647acf431e4f798bcd8b7b3337969752515607fccc3ff76ca5024cb3a66b7192d271053082ed3c22ab; _ga=GA1.2.800074535.1591587463; hs=-2139845448; XSRF-TOKEN=1591848524|dK8MJRzraXtK; _gid=GA1.2.633538645.1591848529; _gat=1');
            array_push($header, 'TE: Trailers');
            $result = basiccurl($xurl, $oapXML, $header);

            if(!$result) return false;

            $result = json_decode($result);
            $price = $result->results[0]->documents[0]->discountedPrice;
            $price = str_replace('Rp','',$price);
            $price = str_replace('.00','',$price);
            $price = str_replace(',','',$price);
            $price = str_replace('.','',$price);
            $price = trim($price);
            $responsesproduct = (object)[];
            $responsesproduct->source = 'id.nikmart.online';
            $responsesproduct->price = (float)$price;
            return $responsesproduct;
        }catch(\Exception $e){
            return false;
        }
    }

    private function _get_kpri_handayani($code)
    {
        try{
            $xurl   = 'http://harga.kpri-handayani.com/pos_item_table.php?q='.$code;
            $header = [];
            array_push($header, 'Host: harga.kpri-handayani.com');
            array_push($header, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0');
            array_push($header, 'Origin: http://harga.kpri-handayani.com');
            array_push($header, 'Connection: keep-alive');
            array_push($header, 'Referer: http://harga.kpri-handayani.com/');
            array_push($header, 'Cookie: TOKO=9qbms6kfu2fjq07s2sja1vavc7');
            $result = basiccurl($xurl, '',$header);

            if(!$result) return false;

            $left = '<table border="0" cellpadding=1 cellspacing=1 class="table_tampil">';
            $right = '</table>';
            $tables = between($result, $left, $right);
            
            $rows = explode('</tr>',$tables);

            if(count($rows)>=3){
                $price = between($rows[1], 'align=right>', '</td>');
                if($price=='' || $price=='NULL') return false;
                $price = str_replace('.','',$price);
                $price = trim($price);
                $responsesproduct = (object)[];
                $responsesproduct->source = 'harga.kpri-handayani.com';
                $responsesproduct->price = (float)$price;
                return $responsesproduct;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }

    private function _get_kopkarsentra($code)
    {
        try{
            $xurl   = 'http://www.kopkarsentra.com/mefomart/belibarang.php?kodebrg='.$code;
            $oapXML = '';
            $result = getcurl($xurl, $oapXML);

            if(!$result) return false;

            $left = 'name="harga_beli"';
            $right = '>';
            $price = between($result, $left, $right);
            if($price=='' || $price=='NULL') return false;

            $price = between($price, 'value="', '"');
            if($price=='' || $price=='NULL') return false;

            $price = str_replace('.','',$price);
            $price = str_replace(',','',$price);
            $price = trim($price);
            if($price==0 || $price=='') return false;

            $responsesproduct = (object)[];
            $responsesproduct->source = 'kopkarsentra.com';
            $responsesproduct->price = (float)$price;
            return $responsesproduct;
        }catch(\Exception $e){
            return false;
        }
    }

    private function _get_mosritel($code)
    {
        try{
            $xurl   = 'http://www.mosritel.com/?s='.$code;
            $oapXML = '';
            $result = getcurl($xurl, $oapXML);

            if(!$result) return false;

            $vaidation = between($result, '<h2>', '</h2>');
            if($vaidation=='Not Found') return false;

            $left = 'class="smart_dtprice"';
            $right = '/p>';
            $div = between($result, $left, $right);
            if($div=='' || $div=='NULL') return false;

            $price = between($div, '>', '<');
            if($price=='' || $price=='NULL') return false;
            
            if($price=='') return false;
            $price = str_replace('Rp','',$price);
            $price = str_replace('.','',$price);
            $price = str_replace(',','',$price);
            $price = trim($price);

            $responsesproduct = (object)[];
            $responsesproduct->source = 'mosritel.com';
            $responsesproduct->price = (float)$price;
            return $responsesproduct;
        }catch(\Exception $e){
            return false;
        }
    }

    private function _get_token_barcode_generator()
    {
        try{
            $xurl   = 'https://www.terryburton.co.uk/barcodewriter/generator/';
            $oapXML = '';
            $result = getcurl($xurl, $oapXML);

            if(!$result) return false;

            $input = between($result, 'name="csrftoken"', '/>');
            if($input=='' || $input=='NULL') return false;

            $token = between($input, 'value="', '" ');
            if($token=='' || $token=='NULL') return false;

            $token = str_replace(' ','%20',$token);
            $token = trim($token);
            return $token;
        }catch(\Exception $e){
            return false;
        }
    }
}
