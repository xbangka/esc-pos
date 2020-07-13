<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller,
    App\Http\Models\Product_Prices,
    App\Http\Models\Products,
    App\Http\Models\Users,
    App\Http\Models\Stores,
    Illuminate\Http\Request;

class AppposCtrl extends Controller
{
    private $js;
    private $css;

    public function __construct(){

        $version = config('app.env')=='local' ? '_dev' : '';

        $this->js = array(
                            'axios.min.js',
                            'vue'.$version.'.js',
                            'crypto-js.js',
                            'Encryption.js',
                            'sweetalert2.all.min.js'
                        );
        $this->css = array(
                            'bootstrap.min.css',
                            'all.css');
    }



    public function _mystore(Request $request)
    {
        $code = $request->code;
        $stores = Stores::where(['code' => $code, 'status' => 1])->first();

        if(!$stores) return abort(404);

        // dd($stores);
        $stores->name           = str_Decode($stores->name);
        $stores->phone          = str_Decode($stores->phone);
        $stores->address        = str_Decode($stores->address);
        $stores->description    = str_Decode($stores->description);

        $key_cross = '_'.uniqid();
        $request->session()->forget('key_cross');
        $request->session()->put('key_cross',$key_cross);

        $appid  = substr(date('D'),0,1).sha1( uniqid() );

        $param['appid']         = $appid;
        $param['csrf_token']    = csrf_token();
        $param['loadingthree']  = asset("images/loadingthree.gif");
        $param['checking_keyas']= url('checking-keyas');
        $param['key_salt']      = $key_cross;
        $param['code']          = $code;

        $data['app']        = $appid;
        $data['mystore']    = $stores;

        if($request->session()->has('toko')) {
            $toko = $request->session()->get('toko');
            if($toko->code!=$code) {
                $request->session()->forget('toko');
                return redirect($code);
            }

            array_push($this->css,'custom.min.css');
            
            array_push($this->js,'jquery.min.js');
            array_push($this->js,'bootstrap.min.js');
            array_push($this->js,'app.js');

            $param['get_products']= url('get-products');
            $param['send_transaction']= url('send-transaction');
            $param['update_data_local']= url('update-data-local');
            $data['user']       = $request->session()->get('user');

            $data['filecss']    = view_css($this->css, $request);
            $data['filejs']     = view_js($this->js, $param, $request);
            
            return view('pos.v_index',$data);

        }else{

            array_push($this->js,'locked.js');

            $data['filecss']    = view_css($this->css, $request);
            $data['filejs']     = view_js($this->js, $param, $request);

            return view('pos.v_locked',$data);
        }
    }




    public function _checking_keyas(Request $request)
    {
        $Encryption = new \Encryption();

        $key = $request->input('keyas');

        $code= $request->input('toko');

        if($request->session()->has('key_cross')) {

            $key_cross = $request->session()->get('key_cross');

        }else{

            return 'Halaman telah kadaluarsa, silakan muat ulang';
        }

        $key    = $Encryption->decrypt($key, $key_cross);

        $code   = $Encryption->decrypt($code, $key_cross);

        $store  = Stores::where(['code' => $code, 'status' => 1])->first();

        if(!$store) return 'Maaf Toko Tidak Aktif';

        $store->name           = str_Decode($store->name);
        $store->phone          = str_Decode($store->phone);
        $store->address        = str_Decode($store->address);
        $store->description    = str_Decode($store->description);

        $users= (isset($store->users)) ? $store->users : array();

        if(count($users)==0) return 'PIN toko tidak ada';

        $key_pin_cocok = false;

        foreach($users as $user) {

            if($user->pin_store === str_Encode($key) ){

                $key_pin_cocok = true;

                $userx = $user;

                break;
            }
        }

        if($key_pin_cocok){

            $request->session()->forget('jumlah_percobaan');

            $request->session()->forget('toko');

            $request->session()->put('toko', $store);

            $request->session()->forget('user');

            $request->session()->put('user', $userx);

            return '*reload*';

        }else{

            if($request->session()->has('jumlah_percobaan')) {

                $jumlah_percobaan = $request->session()->get('jumlah_percobaan');
    
            }else{
    
                $jumlah_percobaan = 0;
            }

            $jumlah_percobaan = $jumlah_percobaan + 1;

            if($jumlah_percobaan>=3){

                $toko = Stores::where('id', $store->id)->firstOrFail();

                $toko->status   = 3;

                $toko->save();

                $request->session()->forget('jumlah_percobaan');

                return 'Salah tiga kali, status toko menjadi tidak aktif';
            }

            $request->session()->put('jumlah_percobaan', $jumlah_percobaan);

            return 'PIN salah';
        }
    }



    public function _get_products(Request $request)
    {
        $Encryption = new \Encryption();

        $barcode    = $request->input('_UPC');

        $code_toko  = $request->input('_toko');

        $hash       = $request->header('Hash');

        if($request->session()->has('toko')) {
            $toko_sess = $request->session()->get('toko');
        }else{
            $response['code']   = 403;
            $response['message']= "Halaman telah kadaluarsa, silakan muat ulang.";
            $response['data']   = [];
            return $response;
        }

        if($request->session()->has('key_cross')) {

            $key_cross = $request->session()->get('key_cross');

            $key_hash = hash('sha256',$key_cross);
            if(!config('app.debug') && $key_hash!=$hash){
                $response['code']   = 403;
                $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
                $response['data']   = [];
                return $response;
            }

        }else{
            $response['code']   = 403;
            $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
            $response['data']   = [];
            return $response;
        }

        $barcode    = $Encryption->decrypt($barcode, $key_cross);

        $code_toko  = $Encryption->decrypt($code_toko, $key_cross);

        if($toko_sess->code!=$code_toko){
            $response['code']   = 403;
            $response['message']= $toko_sess->code.' '.$code_toko."Halaman telah kadaluarsa, silakan muat ulang!";
            $response['data']   = [];
            return $response;
        }

        $product    = Products::where('barcode','=',$barcode)->first();
        
        if(!$product){
            $response['code']   = 404;
            $response['message']= "$barcode tidak ada dalam database";
            $response['data']   = [];
            return $response;
        }

        $name   = $product->full_name;
        $alias  = $product->short_name;
        $category   = isset($product->categories) ? $product->categories->name : $product->category_id;
        $product_prices = isset($product->product_prices) ? $product->product_prices : array();
        // dd($product_prices);

        $varians = [];
        foreach ($product_prices as $row) {
            if($row->status==1){
                $detail = (object)[];
                $detail->code           = $barcode;
                $detail->uuid           = $row->uuid;
                $detail->name           = $name;
                $detail->alias          = $alias;
                $detail->category       = $category;
                $detail->unit           = isset($row->retail_units) ? $row->retail_units->code : '';
                $detail->unit_name      = isset($row->retail_units) ? $row->retail_units->name : '';
                $detail->price          = (float)$row->price;
                $detail->discount       = $row->discounts;
                array_push($varians,$detail);
            }
        }

        $response['code']   = 200;
        $response['message']= 'OK';
        $response['data']   = $varians;
        
        return $response;
    }

    public function _update_data_local(Request $request)
    {
        $Encryption = new \Encryption();

        $data       = $request->input('_data');

        $code_toko  = $request->input('_toko');

        $hash       = $request->header('Hash');

        if($request->session()->has('key_cross')) {

            $key_cross = $request->session()->get('key_cross');

            $key_hash = hash('sha256',$key_cross);
            if(!config('app.debug') && $key_hash!=$hash){
                $response['code']   = 403;
                $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
                $response['data']   = [];
                return $response;
            }

        }else{
            $response['code']   = 403;
            $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
            $response['data']   = [];
            return $response;
        }

        $code_toko  = $Encryption->decrypt($code_toko, $key_cross);

        $datax = [];
        $arrcode = [];
        foreach ($data as $row) {
            $x = explode(',',$row);
            $code = trim($x[0]);
            $datax[$code] = $x[1] . ':00';
            array_push($arrcode,$code);
        }
        
        $products    = Products::whereIn('barcode',$arrcode)->get();

        if(!$products){
            $response['code']   = 401;
            $response['message']= "tidak dapat update data";
            $response['data']   = [];
            return $response;
        }

        $product_output = [];
        foreach ($products as $product) {
            $name   = $product->full_name;
            $alias  = $product->short_name;
            $barcode= $product->barcode;
            $updated= $product->updated_at;
            $category   = isset($product->categories) ? $product->categories->name : $product->category_id;
            $product_prices = isset($product->product_prices) ? $product->product_prices : array();
            
            $varians = [];
            foreach ($product_prices as $row) {
                if($row->status==1 && isset($row->stores) && $row->stores->code==$code_toko){
                    $detail = (object)[];
                    $detail->code           = $barcode;
                    $detail->uuid           = $row->uuid;
                    $detail->name           = $name;
                    $detail->alias          = $alias;
                    $detail->category       = $category;
                    $detail->unit           = isset($row->retail_units) ? $row->retail_units->code : '';
                    $detail->unit_name      = isset($row->retail_units) ? $row->retail_units->name : '';
                    $detail->price          = (float)$row->price;
                    $updated                = ($row->updated_at>$updated) ? $row->updated_at : $updated;
                    $detail->discount       = $row->discounts;
                    array_push($varians,$detail);
                }
            }
            if(count($varians)>=1 && $datax[$barcode]<$updated) $product_output['x'.$barcode] = $varians;
        }
        
        $response['code']   = 200;
        $response['message']= 'OK';
        $response['data']   = $product_output;
        
        return $response;
    }

    public function _send_transaction (Request $request)
    {
        $Encryption = new \Encryption();

        $trx        = $request->input('_trx');

        $cash       = $request->input('_cash');

        $trxuniqid  = $request->input('_trxuniqid');

        $code_toko  = $request->input('_toko');

        $hash       = $request->header('Hash');

        $hashCash   = $request->header('Cash');

        $hashTrxUniqid = $request->header('Trxuniqid');

        if($request->session()->has('key_cross')) {

            $key_cross = $request->session()->get('key_cross');

            $key_hash = hash('sha256',$key_cross);
            if(!config('app.debug') && $key_hash!=$hash){
                $response['code']   = 403;
                $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
                $response['data']   = [];
                return $response;
            }

            $cash_hash = hash('sha256',$cash);
            if(!config('app.debug') && $cash_hash!=$hashCash){
                $response['code']   = 403;
                $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
                $response['data']   = [];
                return $response;
            }

            $trxuniqid_hash = hash('sha256',$trxuniqid);
            if(!config('app.debug') && $trxuniqid_hash!=$hashTrxUniqid){
                $response['code']   = 403;
                $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
                $response['data']   = [];
                return $response;
            }

            if($request->session()->has('trxuniqid')) {
                $xtemptrxuniqid = $request->session()->get('trxuniqid');
                if($xtemptrxuniqid==$trxuniqid){
                    $response['code']   = 403;
                    $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
                    $response['data']   = [];
                    return $response;
                }
            }

            $request->session()->forget('trxuniqid');
            $request->session()->put('trxuniqid',$trxuniqid);

        }else{
            $response['code']   = 403;
            $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
            $response['data']   = [];
            return $response;
        }

        $code_toko  = $Encryption->decrypt($code_toko, $key_cross);

        $datax = [];
        $arrcode = [];
        foreach ($data as $row) {
            $x = explode(',',$row);
            $code = trim($x[0]);
            $datax[$code] = $x[1] . ':00';
            array_push($arrcode,$code);
        }
        
        $products    = Products::whereIn('barcode',$arrcode)->get();

        if(!$products){
            $response['code']   = 401;
            $response['message']= "tidak dapat update data";
            $response['data']   = [];
            return $response;
        }

        $product_output = [];
        foreach ($products as $product) {
            $name   = $product->full_name;
            $alias  = $product->short_name;
            $barcode= $product->barcode;
            $updated= $product->updated_at;
            $category   = isset($product->categories) ? $product->categories->name : $product->category_id;
            $product_prices = isset($product->product_prices) ? $product->product_prices : array();
            
            $varians = [];
            foreach ($product_prices as $row) {
                if($row->status==1 && isset($row->stores) && $row->stores->code==$code_toko){
                    $detail = (object)[];
                    $detail->code           = $barcode;
                    $detail->uuid           = $row->uuid;
                    $detail->name           = $name;
                    $detail->alias          = $alias;
                    $detail->category       = $category;
                    $detail->unit           = isset($row->retail_units) ? $row->retail_units->code : '';
                    $detail->unit_name      = isset($row->retail_units) ? $row->retail_units->name : '';
                    $detail->price          = (float)$row->price;
                    $updated                = ($row->updated_at>$updated) ? $row->updated_at : $updated;
                    $detail->discount       = $row->discounts;
                    array_push($varians,$detail);
                }
            }
            if(count($varians)>=1 && $datax[$barcode]<$updated) $product_output['x'.$barcode] = $varians;
        }
        
        $response['code']   = 200;
        $response['message']= 'OK';
        $response['data']   = $product_output;
        
        return $response;
    }
}
