<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller,
    App\Http\Models\Product_Prices,
    App\Http\Models\Products,
    App\Http\Models\Users,
    App\Http\Models\Stores,
    App\Http\Models\Orders,
    App\Http\Models\Order_Detail,
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
        try{
            $code = $request->code;
            $stores = Stores::where(['code' => $code, 'status' => 1])->first();

            if(!$stores) {
                $response['code']   = 404;
                $response['message']= "Not Found";
                return $response;
            }

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
                
                if($request->input('m','false')=='true'){
                    return view('pos.v_m_index',$data);
                }else{
                    return view('pos.v_index',$data);
                }

            }else{

                array_push($this->js,'locked.js');

                $data['filecss']    = view_css($this->css, $request);
                $data['filejs']     = view_js($this->js, $param, $request);

                return view('pos.v_locked',$data);
            }
        }catch(\Exception $e){
            return (config('app.debug')) ? /*$e.''*/ $e->getMessage() : 'Error Exception in try action';
        }
    }




    public function _checking_keyas(Request $request)
    {
        try{

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

                    $user->nameshow = str_Decode($user->nameshow);

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
        }catch(\Exception $e){
            return (config('app.debug')) ? /*$e.''*/ $e->getMessage() : 'Error Exception in try action';
        }
    }



    public function _get_products(Request $request)
    {
        try{
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
        }catch(\Exception $e){
            return (config('app.debug')) ? /*$e.''*/ $e->getMessage() : 'Error Exception in try action';
        }
    }

    public function _update_data_local(Request $request)
    {
        try{

            $Encryption = new \Encryption();

            $data       = $request->input('_data');

            $code_toko  = $request->input('_toko');

            $hash       = $request->header('Hash');

            if($request->session()->has('key_cross') && $request->session()->has('toko')) {

                $key_cross  = $request->session()->get('key_cross');
                $toko       = $request->session()->get('toko');

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
            
            if($code_toko!=$toko->code){
                $response['code']   = 403;
                $response['message']= "Halaman telah kadaluarsa, silakan muat ulang";
                $response['data']   = [];
                return $response;
            }

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
                        $updated                = ($row->updated_at>$updated) ? $row->updated_at : $updated;
                        $detail->discount       = $row->discounts;
                        array_push($varians,$detail);
                    }
                }
                $product_output['x'.$barcode] = $varians;
            }
            
            $response['code']   = 200;
            $response['message']= 'OK';
            $response['data']   = $product_output;
            
            return $response;
        }catch(\Exception $e){
            return (config('app.debug')) ? /*$e.''*/ $e->getMessage() : 'Error Exception in try action';
        }
    }

    public function _send_transaction (Request $request)
    {
        \DB::beginTransaction();
        try{
            if($request->session()->has('key_cross')) {
            
                $Encryption = new \Encryption();
        
                $trx        = $request->input('_trx');

                $key_cross  = $request->session()->get('key_cross');

                $trx        = $Encryption->decrypt($trx, $key_cross);

                $tr         = json_decode($trx,false);

                $items      = $tr->items;
                $total      = $tr->total;
                $cash       = $tr->cash;
                $changedue  = $tr->changedue;
                $code_toko  = $tr->toko;
                $action     = $tr->action;
                $trxuniqid  = $tr->trxuniqid;

                if($request->session()->has('toko')) {
                    $toko = $request->session()->get('toko');
                    if($toko->code!=$code_toko){
                        $response['code']   = 403;
                        $response['message']= "Kode toko tidak teridentifikasi, silakan muat ulang";
                        $response['data']   = [];
                        return $response;
                    }
                }
                
                if(!$request->session()->has('user')) {
                    $response['code']   = 403;
                    $response['message']= "ID Kasir tidak teridentifikasi, silakan muat ulang";
                    $response['data']   = [];
                    return $response;
                }

                $user = $request->session()->get('user');

                if($request->session()->has('trxuniqid')) {
                    $xtemptrxuniqid = $request->session()->get('trxuniqid');
                    if($xtemptrxuniqid==$trxuniqid){
                        $response['code']   = 403;
                        $response['message']= "transaktion uniq id sudah tersimpan, silakan muat ulang";
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

            if($action==0 || $action==1){
                $invoice    = $code_toko . date('ymd');
                $last       = Orders::select(\DB::raw('MID(invoice, 12,4) as invoice_number'))
                                    ->where(\DB::raw('LEFT(invoice, 11)'),'=',$invoice)
                                    ->orderBy('invoice','desc')
                                    ->first();
                $thelast    = isset($last->invoice_number) ? ((int)$last->invoice_number + 1) : 1 ;

                $invoice    = $invoice . sprintf('%04d',$thelast);

                $odr             = new Orders;
                $odr->uuid       = uuid4();
                $odr->invoice    = $invoice;
                $odr->id_customer= null;
                $odr->id_user    = $user->id;
                $odr->total      = (float)$total;
                $odr->cash       = (float)$cash;
                $odr->changedue  = (float)$changedue;
                $odr->json_detail= $trx;
                $odr->status     = 1;

                if($odr->save()){
                    $odr_id = $odr->id;
                    foreach ($items as $row){

                        $discount = 0;
                        foreach($row->discount as $di){
                            if($row->qty>=$di->condition_qty_from && $row->qty<=$di->condition_qty_to){
                                if($di->value_type==1){
                                    $discount = $di->value;
                                }else{
                                    $pricex     = $row->price * $row->qty;
                                    $discount   = ($pricex/100) * $di->value;
                                }
                            }
                        }
                        $det                = new Order_Detail;
                        $det->id_order      = $odr_id;
                        $det->product_name  = $row->name;
                        $det->category      = $row->category;
                        $det->unit          = $row->unit_name;
                        $det->price         = $row->price;
                        $det->qty           = $row->qty;
                        $det->discount      = $discount;
                        $det->subtotal      = (float)(($row->price * $row->qty)-$discount);
                        $det->save();
                    }
                }
                
                \DB::commit();
            }
            if($action==0 || $action==2){
                
                $json_obj = $this->_output_print($trx);

                $encryption = new \Encryption();
	
                $json_obj = $encryption->encrypt($json_obj, 'esc-pos');

                return $json_obj;
            }
            return ;
        }catch(\Exception $e){
            \DB::rollBack();
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
        }
    }

    private function _output_print($trx)
    {
        try{
            $tr         = json_decode($trx,false);
            $items      = $tr->items;
            $total      = $tr->total;
            $cash       = $tr->cash;
            $changedue  = $tr->changedue;

            /// Cari Nama Toko
            if( \Session::has('toko') ){
                $nama_toko = \Session::get('toko')->name;
            }else{
                $nama_toko = config('app.name');
            }

            /// Cari Nama Pengguna atau nama kasir
            if( \Session::has('user') ){
                $nameshow = \Session::get('user')->nameshow;
            }else{
                $nameshow = '-';
            }

            /// define Tanggal struk
            $tanggal = date('d.m.y.H:i');

            /// header identity
            $len = strlen($nameshow);
            $sisa = 28 - $len;
            $spasi = '';
            for ($i=0; $i < $sisa; $i++) $spasi .= ' ';
            $header_id = $tanggal.$spasi.$nameshow;
            

            $responses = [];

            $obj0 = (object)[];
            $obj1 = (object)[];
            $obj2 = (object)[];
            $obj3 = (object)[];
            $obj4 = (object)[];
            $obj5 = (object)[];
            $obj6 = (object)[];

            $obj0->type 	= 0;
            $obj0->content 	= strtoupper($nama_toko);
            $obj0->bold 	= 1;
            $obj0->align 	= 1;
            $obj0->format 	= 0;
            array_push($responses,$obj0);

            $obj1->type 	= 0;
            $obj1->content 	= '------------------------------------------';
            $obj1->bold 	= 0;
            $obj1->align 	= 2;
            $obj1->format 	= 4;
            array_push($responses,$obj1);

            $obj2->type 	= 0;
            $obj2->content 	= $header_id;
            $obj2->bold 	= 0;
            $obj2->align 	= 2;
            $obj2->format 	= 4;
            array_push($responses,$obj2);

            $obj3->type 	= 0;
            $obj3->content 	= '------------------------------------------';
            $obj3->bold 	= 0;
            $obj3->align 	= 2;
            $obj3->format 	= 4;
            array_push($responses,$obj3);

            $subTotal = 0;
            $discountTotal = 0;
            $itemprice  = [];

            foreach ($items as $row) {
                $discount = 0;
                foreach($row->discount as $di){
                    if($row->qty>=$di->condition_qty_from && $row->qty<=$di->condition_qty_to){
                        if($di->value_type==1){
                            $discount = $di->value;
                        }else{
                            $pricex     = $row->price * $row->qty;
                            $discount   = ($pricex/100) * $di->value;
                        }
                    }
                }
                $discountTotal += $discount;
                
                // Baris nama produk =================
                $len = strlen($row->name);
                $sisa = 42 - $len;
                $spasi = '';
                for ($i=0; $i < $sisa; $i++) $spasi .= ' ';
                $product = $row->name.$spasi.'<br />';

                // Baris Harga  =================
                $quantity   = '    '.$row->qty . ' ' . $row->unit_name . ' x '. format_number($row->price);
                $price      = $row->qty*$row->price;
                $priceFormat= format_number($price);
                $subTotal  += $price;

                $len1 = strlen($quantity);
                $len2 = strlen($priceFormat);
                $sisa = 42 - ($len1 + $len2);
                $spasi = '';
                for ($i=0; $i < $sisa; $i++) $spasi .= ' ';

                $produkPrice = $quantity.$spasi.$priceFormat;

                array_push($itemprice,($product.$produkPrice));
            }

            if( count($itemprice)>0 ){
                $produk         = implode('<br />',$itemprice);
                $obj4->type 	= 0;
                $obj4->content 	= $produk;
                $obj4->bold 	= 0;
                $obj4->align 	= 2;
                $obj4->format 	= 4;
                array_push($responses,$obj4);
            }

            // define Total, discount, dan kembalian
            $bottomPrice = '                    ----------------------<br />';
            if($discountTotal!=0){

                $subTotal   = format_number($subTotal);
                $len        = strlen($subTotal);
                $sisa       = 11 - $len;
                $spasi      = '';
                for ($i=0; $i < $sisa; $i++) $spasi .= ' ';
                $subTotal = $spasi.$subTotal.'<br />';
                $bottomPrice .= '                    SUB TOTAL :'.$subTotal;

                $discountTotal= format_number($discountTotal);
                $len        = strlen($discountTotal);
                $sisa       = 11 - $len;
                $spasi      = '';
                for ($i=0; $i < $sisa; $i++) $spasi .= ' ';
                $discountTotal = $spasi.$discountTotal.'<br />';
                $bottomPrice .= '                    DISCOUNT  :'.$discountTotal;

                $bottomPrice .= '                    ----------------------<br />';
            }

                $total  = format_number($total);
                $len    = strlen($total);
                $sisa   = 11 - $len;
                $spasi  = '';
                for ($i=0; $i < $sisa; $i++) $spasi .= ' ';
                $total  = $spasi.$total.'<br />';
                $bottomPrice .= '                    TOTAL     :'.$total;

                $cash   = format_number($cash);
                $len    = strlen($cash);
                $sisa   = 11 - $len;
                $spasi  = '';
                for ($i=0; $i < $sisa; $i++) $spasi .= ' ';
                $cash   = $spasi.$cash;
                $bottomPrice .= '                    TUNAI     :'.$cash;

                if($changedue!=0){
                    $changedue  = format_number($changedue);
                    $len        = strlen($changedue);
                    $sisa       = 11 - $len;
                    $spasi      = '';
                    for ($i=0; $i < $sisa; $i++) $spasi .= ' ';
                    $changedue   = $spasi.$changedue;
                    $bottomPrice .= '<br />                    KEMBALI   :'.$changedue;
                }

            $obj5->type 	= 0;
            $obj5->content 	= $bottomPrice;
            $obj5->bold 	= 0;
            $obj5->align 	= 2;
            $obj5->format 	= 4;
            array_push($responses,$obj5);

            $obj6->type 	= 0;
            $obj6->content 	= ' <br /> ';
            $obj6->bold 	= 0;
            $obj6->align 	= 0;
            array_push($responses,$obj6);
            
            $json_obj = json_encode($responses,JSON_FORCE_OBJECT);
            $json_obj = str_replace('\/','/',$json_obj);

            return $json_obj;

        }catch(\Exception $e){
            return (config('app.debug')) ? $e.'' /*$e->getMessage()*/ : 'Error Exception in try action';
        }
        
    }
}
