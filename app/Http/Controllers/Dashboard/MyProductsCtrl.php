<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller,
    App\Http\Models\Products,
    App\Http\Models\Product_Prices,
    App\Http\Models\Categories,
    App\Http\Models\Discounts,
    App\Http\Models\Retail_Units,
    App\Http\Models\Statuses,
    Illuminate\Http\Request,
    Yajra\Datatables\Datatables;

class MyProductsCtrl extends Controller
{
    private $my_module;
    private $log;
    

    public function __construct(){
        $this->my_module    = 'products';
        $this->js           = array();
        $this->log          = new \ChangeLog;
    }

    public function index(Request $request)
    {
        try{
            $key_cross = '_'.uniqid();
            $request->session()->forget('key_cross');
            $request->session()->put('key_cross',$key_cross);

            $appid  = substr(date('D'),0,1).sha1( uniqid() );

            $categories = \Cache::remember('categories_products', 60, function(){
                return Categories::select('code','name')->get()->toArray();
            });//print_r( json_encode($categories) );exit;

            $units = \Cache::remember('retail_units', 400, function(){
                return Retail_Units::select('id','code','name')->get()->toArray();
            });//print_r( json_encode($statuses) );exit;

            $param['appid']         = $appid;
            $param['csrf_token']    = csrf_token();
            $param['getBarcodeImage']= url('barcode-image');
            $param['printBarcode']  = url('print-barcode');
            $param['getPriceRef']   = url('get-price-references');
            $param['getPriceVariations']= url('my-products/get-price-variations');
            $param['newData']       = url('my-products/new-data');
            $param['getData']       = url('my-products/data');
            $param['getRetails']    = url('my-products/get-retails');
            $param['cekNewBarcode'] = url('my-products/cek-new-barcode');
            $param['changeStatusPrice'] = url('my-products/change-status-price');
            $param['changeStatusDiscount'] = url('my-products/change-status-discount');
            $param['saveDataPrice'] = url('my-products/save-data-price');
            $param['saveDataDiscount'] = url('my-products/save-data-discount');
            $param['key_salt']      = $key_cross;
            $param['categories']    = json_encode($categories);
            $param['units']         = json_encode($units);

            array_push($this->js,'my_products.js');

            $data['data']   = config('auth.udata');//dd(config('auth.udata'));
            $data['menu']   = 'myproducts';
            $data['filejs'] = view_js($this->js, $param, $request);
            $data['app']    = $appid;
            
            return view('dashboard.products.v_my_products', $data);
        }catch(\Exception $e){
            if(config('app.debug')){
                return $e->getMessage();
            }else{
                $err['code'] = 500;
                $err['message'] = 'Error Exception in try action';
                return response($err)->header('Content-Type', 'application/json');
            }
        }
    }

    public function getData(Datatables $datatables, Request $request)
    {
        try{
            $key = $request->header('Accept-Dinamic-Key');

            if($request->session()->has('key_cross')) {
                $key_cross = $request->session()->get('key_cross');
            }else{
                return 'Halaman telah kadaluarsa, silakan muat ulang';
            }

            if(config('app.debug')) { $key=true;$key_cross=true; }

            if($key && $key==$key_cross){
                $udata      = config('auth.udata');
                $store_code = $udata->store_code;
                $q    = Products::query()
                        ->select(
                            'products.id         as id',
                            'products.uuid       as uuid',
                            'products.barcode    as kode',
                            'products.full_name  as nama',
                            'products.short_name as nm',
                            'products.description as desc',
                            'products.category',
                            'products.updated_at as modify'
                        )
                        ->with('categories')
                        ->whereHas('product_prices');
                return   $datatables->eloquent($q)
                                    ->editColumn('modify', function ($x) {
                                        if($x->modify=='') return '01.01.2020 00:00';
                                        $date = date_create($x->modify);
                                        return date_format($date,"d.m.Y H:i:s");
                                    })
                                    ->editColumn('categories', function ($x) {
                                        if($x->categories==null) {
                                            $obj = [];
                                            $obj['name'] = '-';
                                            return $obj;
                                        };
                                        return json_decode($x->categories,true);
                                    })
                                    ->removeColumn('id')
                                    ->removeColumn('category')
                                    ->removeColumn('categories.id')
                                    ->removeColumn('categories.uuid')
                                    ->removeColumn('categories.code')
                                    ->removeColumn('categories.created_at')
                                    ->removeColumn('categories.updated_at')
                                    ->make();
            }
        }catch(\Exception $e){
            if(config('app.debug')){
                return $e->getMessage();
            }else{
                $err['code'] = 500;
                $err['message'] = 'Error Exception in try action';
                return response($err)->header('Content-Type', 'application/json');
            }
        }
    }

    public function newData(Request $request)
    {
        \DB::beginTransaction();
        try{
            if($request->session()->has('key_cross')) {
                $key_cross = $request->session()->get('key_cross');
            }else{
                return 'Halaman telah kadaluarsa, silakan muat ulang';
            }

            $_q = $request->input('_q');
            $_hash = $request->header('Host');
            
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
            $uuid = $_q->uuid;
            $uuid = ($uuid=='') ? false:$uuid;
            $uuid_hash = ($uuid) ? hash('sha256',$uuid):'';
            if(!config('app.debug') && $uuid_hash!=$_hash) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
            
            $code = $_q->code;
            $name = $_q->name;
            $snme = substr($_q->snme,0,20);
            $cate = $_q->cate;
            $desc = $_q->desc;
            if($uuid){
                $old        = Products::where([['uuid', '=', $uuid],['barcode', '=', $code]])->first();
                if(!isset($old->id)) return 'Barcode tidak diketahui !.';
                $product    = Products::where('uuid', $uuid)->firstOrFail();

            }else{
                $old        = Products::where('barcode', '=', $code)->first();
                if(isset($old->id)) return 'Barcode sudah ada, silakan barcode anda di cek terlebih dahulu!';

                $product            = new Products;
                $product->uuid      = uuid4();
                $product->barcode   = $code;
            }
            $product->full_name     = $name;
            $product->short_name    = $snme;
            $product->category      = $cate;
            $product->description   = $desc;
            
            if(!$product->save()){
                return 'Maaf, tidak dapat menyimpan data';
            }else{
                if($uuid){
                    $categories  = Categories::select('name','code')->get();
                    $arr = [];
                    foreach ($categories as $row) {
                        $obj = (object)[];
                        $obj->id = $row->code;
                        $obj->name = $row->name;
                        array_push($arr,$obj);
                    }
                    $optionAttributKey['category'] = $arr;
                    $this->log->createLog($product->getChanges(), $old->getOriginal(), 'products', $old->id, $optionAttributKey);
                }else{
                    $this->log->firstLog('products', $product->id);
                }

                $price_check = Product_Prices::where('id_product', '=', $product->id)->first();
                if(!isset($price_check->id)){
                    $pr                 = new Product_Prices;
                    $pr->uuid           = uuid4();
                    $pr->id_product     = $product->id;
                    $pr->id_retail_unit = 1;
                    $pr->price          = 0.00;
                    $pr->status         = 1;
                    if($pr->save()) $this->log->firstLog('product_prices_'.(config('auth.udata')->store_code), $pr->id);
                }

                \DB::commit();
                return '*OK*'.$product->uuid;
            }
        }catch(\Exception $e){
            \DB::rollBack();
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
        }
    }
    
    public function getPriceVariations(Request $request)
    {
        try{
            if($request->session()->has('key_cross')) {
                $key_cross = $request->session()->get('key_cross');
            }else{
                return 'Halaman telah kadaluarsa, silakan muat ulang';
            }

            $_q = $request->input('_q');
            $_hash = $request->header('_hash');
            
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
            $uuid = $_q->uuid;
            $uuid = ($uuid=='') ? false:$uuid;
            $uuid_hash = ($uuid) ? hash('sha256',$uuid):'';
            if(!config('app.debug') && $uuid_hash!=$_hash) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
            
            $varians = $this->variations('uuid', $uuid); 
            return ($varians) ? $varians : response([])->header('Content-Type', 'application/json');

        }catch(\Exception $e){
            return (config('app.debug')) ? /*$e.''*/ $e->getMessage() : 'Error Exception in try action';
        }
    }

    public function changeStatusPrice(Request $request)
    {
        try{
            if($request->session()->has('key_cross')) {
                $key_cross = $request->session()->get('key_cross');
            }else{
                return 'Halaman telah kadaluarsa, silakan muat ulang';
            }

            $_q = $request->input('_q');
            $_hash = $request->header('_hash');
            
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
            $uuid = isset($_q->uuid) ? $_q->uuid : false;
            $stts = isset($_q->stt) ? $_q->stt : false;
            if(!$uuid || !$stts) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
            $uuid_hash = hash('sha256',$uuid);
            if(!config('app.debug') && $uuid_hash!=$_hash) return 'sha256 invalid';

            $old    = Product_Prices::where('uuid', '=', $uuid)->first();
            if(!isset($old->id)) return 'Tidak diketahui !.';
            $prices = Product_Prices::where('uuid', $uuid)->firstOrFail();
            $prices->status = $stts;
            
            if(!$prices->save()){
                return 'Maaf, tidak dapat menyimpan data';
            }else{
                $statuses  = Statuses::select('foreign_key as id','name')->where('module','=','product_prices')->get();
                $optionAttributKey['status'] = $statuses;
                $this->log->createLog($prices->getChanges(), $old->getOriginal(), 'product_prices_'.(config('auth.udata')->store_code), $old->id, $optionAttributKey);
            }
            
            $varians = $this->variations('id', $old->id_product); 
            return ($varians) ? $varians : response([])->header('Content-Type', 'application/json');

        }catch(\Exception $e){
            return (config('app.debug')) ? /*$e.''*/ $e->getMessage() : 'Error Exception in try action';
        }
    }

    public function changeStatusDiscount(Request $request)
    {
        try{
            if($request->session()->has('key_cross')) {
                $key_cross = $request->session()->get('key_cross');
            }else{
                return 'Halaman telah kadaluarsa, silakan muat ulang';
            }

            $_q = $request->input('_q');
            $_hash = $request->header('_hash');
            
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
            $uuid = isset($_q->uuid) ? $_q->uuid : false;
            $stts = isset($_q->stt) ? $_q->stt : false;
            if(!$uuid || !$stts) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
            $uuid_hash = hash('sha256',$uuid);
            if(!config('app.debug') && $uuid_hash!=$_hash) return 'sha256 invalid';
            $old    = Discounts::where('uuid', '=', $uuid)->first();
            if(!isset($old->id)) return 'Tidak diketahui !.';
            $disc = Discounts::where('uuid', $uuid)->firstOrFail();
            $disc->status = $stts;
            
            if(!$disc->save()){
                return 'Maaf, tidak dapat menyimpan data';
            }else{
                $statuses  = Statuses::select('foreign_key as id','name')->where('module','=','discounts')->get();
                $optionAttributKey['status'] = $statuses;
                $this->log->createLog($disc->getChanges(), $old->getOriginal(), 'discounts_'.(config('auth.udata')->store_code), $old->id, $optionAttributKey);
            }
            
            $varians = $this->variations('id', $old->product_prices->id_product); 
            return ($varians) ? $varians : response([])->header('Content-Type', 'application/json');

        }catch(\Exception $e){
            return (config('app.debug')) ? $e.'' /*$e->getMessage()*/ : 'Error Exception in try action';
        }
    }

    public function cekNewBarcode(Request $request)
    {
        try{
            if($request->session()->has('key_cross')) {
                $key_cross = $request->session()->get('key_cross');
            }else{
                return 'Halaman telah kadaluarsa, silakan muat ulang';
            }

            $_q = $request->input('_q');
            $_hash = $request->header('_hash');
            
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
            $code = $_q->code;
            $code = ($code=='') ? false:$code;
            $code_hash = ($code) ? hash('sha256',$code):'';
            if(!config('app.debug') && $code_hash!=$_hash) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
            


            // $curl_1 = $this->_get_kpri_handayani($code); return json_encode($curl_1);



            $product = Products::where('barcode', $code)->first();

            if($product){
                $responsesproduct = (object)[];
                $responsesproduct->uuid         = $product->uuid;
                $responsesproduct->fname        = $product->full_name;
                $responsesproduct->sname        = $product->short_name;
                $responsesproduct->cate         = $product->category;
                $responsesproduct->source       = 'db';
                if($product->description!=''){
                    $responsesproduct->desc         = $product->description;
                }
            }else{
                $curl_1 = $this->_get_kpri_handayani($code);
                if($curl_1){
                    $responsesproduct = $curl_1;
                }else{
                    $curl_2 = $this->_get_nikmart_online($code);
                    if($curl_2){
                        $responsesproduct = $curl_2;
                    }else{
                        $curl_2 = $this->_get_kopkarsentra($code);
                        if($curl_2){
                            $responsesproduct = $curl_2;
                        }else{
                            $curl_2 = $this->_get_jembatanbaru($code);
                            if($curl_2){
                                $responsesproduct = $curl_2;
                            }else{
                                $curl_2 = $this->_get_mosritel($code);
                                if($curl_2){
                                    $responsesproduct = $curl_2;
                                }else{
                                    $curl_2 = $this->_get_suzuya($code);
                                    if($curl_2){
                                        $responsesproduct = $curl_2;
                                    }else{
                                        $responsesproduct = (object)[];
                                        $responsesproduct->source = 'null';
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            $responses = json_encode($responsesproduct);
            return response($responses)->header('Content-Type', 'application/json');
        }catch(\Exception $e){
            return (config('app.debug')) ? $e.'' /*$e->getMessage()*/ : 'Error Exception in try action';
        }
    }

    public function saveDataPrice(Request $request)
    {
        try{
            if($request->session()->has('key_cross')) {
                $key_cross = $request->session()->get('key_cross');
            }else{
                return 'Halaman telah kadaluarsa, silakan muat ulang';
            }

            $_q = $request->input('_q');
            $_hash = $request->header('_hash');
            
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
            $uuid = $_q->uuid;
            $uuid = ($uuid=='') ? false:$uuid;
            $uuid_hash = ($uuid) ? hash('sha256',$uuid):'';// dd($uuid_hash,$_hash,$_q);
            if(!config('app.debug') && $uuid_hash!=$_hash) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
            
            $unit = $_q->unit;
            $price = $_q->price;
            $product = $_q->product;
            $id_product = Products::select('id')->where('uuid', $product)->first();
            if(!$id_product) return 'Maaf, Produk tidak terdaftar.';
            $id_product = $id_product->id;

            if($uuid){
                $old    = Product_Prices::where('uuid', $uuid)->first();
                $prices = Product_Prices::where('uuid', $uuid)->firstOrFail();
            }else{
                $prices             = new Product_Prices;
                $prices->uuid       = uuid4();
                $prices->id_product = $id_product;
                $prices->status     = 1;
            }
            $prices->id_retail_unit = $unit;
            $prices->price          = (float)$price;
            
            if(!$prices->save()){
                return 'Maaf, tidak dapat menyimpan data';
            }else{
                if($uuid){
                    $r_unit  = Retail_Units::select('id','name','code')->get();
                    $arr = [];
                    foreach ($r_unit as $row) {
                        $obj = (object)[];
                        $obj->id = $row->id;
                        $obj->name = $row->name.' ('.$row->name.')';
                        array_push($arr,$obj);
                    }
                    $optionAttributKey['id_retail_unit'] = $arr;
                    $this->log->createLog($prices->getChanges(), $old->getOriginal(), 'product_prices_'.(config('auth.udata')->store_code), $old->id, $optionAttributKey);
                }else{
                    $this->log->firstLog('product_prices_'.(config('auth.udata')->store_code), $prices->id);
                }
                $varians = $this->variations('id', $id_product); 
                return ($varians) ? $varians : response([])->header('Content-Type', 'application/json');
            }
        }catch(\Exception $e){
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
        }
    }

    public function saveDataDiscount(Request $request)
    {
        try{
            if($request->session()->has('key_cross')) {
                $key_cross = $request->session()->get('key_cross');
            }else{
                return 'Halaman telah kadaluarsa, silakan muat ulang';
            }

            $_q = $request->input('_q');
            $_hash = $request->header('_hash');
            
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
            $uuid = $_q->uuid;
            $uuid = ($uuid=='') ? false:$uuid;
            $uuid_hash = ($uuid) ? hash('sha256',$uuid):'';
            if(!config('app.debug') && $uuid_hash!=$_hash) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
            
            $name   = $_q->name;
            $value  = $_q->value;
            $tipe   = $_q->type;
            $from   = $_q->from;
            $conto  = $_q->conto;
            $start  = $_q->start;
            $end    = $_q->end;

            $price_uuid = Product_Prices::select('id')->where('uuid',$_q->uuid_price)->first();
            if(!$price_uuid) return 'Maaf, id tidak terdaftar.';
            $price_uuid = $price_uuid->id;

            if($uuid){
                $old    = Discounts::where('uuid', $uuid)->first();
                $disx   = Discounts::where('uuid', $uuid)->firstOrFail();
            }else{
                $disx                   = new Discounts;
                $disx->uuid             = uuid4();
                $disx->product_price_id = $price_uuid;
                $disx->discount_type    = 1;
                $disx->status           = 1;
            }
            $disx->event_name           = $name;
            $disx->value                = (float)$value;
            $disx->value_type           = $tipe;
            $disx->condition_qty_from   = $from;
            $disx->condition_qty_to     = $conto;
            $disx->start_date           = $start;
            $disx->end_date             = $end;
            
            if(!$disx->save()){
                return 'Maaf, tidak dapat menyimpan data';
            }else{
                if($uuid){
                    $statuses  = Statuses::select('foreign_key as id','name')->where('module','=','discounts')->get();
                    $optionAttributKey['status'] = $statuses;
                    $this->log->createLog($disx->getChanges(), $old->getOriginal(), 'discounts_'.(config('auth.udata')->store_code), $old->id, $optionAttributKey);
                }else{
                    $this->log->firstLog('discounts_'.(config('auth.udata')->store_code), $disx->id);
                }
            }
            
            $varians = $this->variations('id', $disx->product_prices->id_product); 
            return ($varians) ? $varians : response([])->header('Content-Type', 'application/json');
        }catch(\Exception $e){
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
        }
    }

    private function variations($key = 'id', $x)
    {
        try{$product = Products::where($key, $x)->first();
            if($product){
                $product_prices = isset($product->product_prices) ? $product->product_prices : array();
                $varians = [];
                foreach ($product_prices as $row) {
                    $detail = (object)[];
                    $detail->uuid           = $row->uuid;
                    $detail->unit           = isset($row->retail_units) ? $row->retail_units->code : '';
                    $detail->unit_name      = isset($row->retail_units) ? $row->retail_units->name : '';
                    $detail->price          = (float)$row->price;
                    $detail->status         = $row->status;
                    $detail->discounts      = $row->discounts_with_status;
                    array_push($varians,$detail);
                }
                return $varians;
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
    }

    private function _get_suzuya($code)
    {
        try{
            $xurl   = 'https://waorder.suzuyagroup.com/ajax/getproduct.php?searchproduct='.$code;
            $oapXML = '';
            $result = getcurl($xurl, $oapXML);

            if(!$result) return false;

            $left = '<strong>';
            $right = '</strong>';
            $name = between($result, $left, $right);
            if($name=='' || $name=='NULL') return false;
            $name = strtoupper($name);
            $name = str_replace('  ',' ',$name);
            $name = str_replace('  ',' ',$name);
            $responsesproduct = (object)[];
            $responsesproduct->fname = $name;
            $responsesproduct->sname = substr($name,0,20);
            $responsesproduct->source = 'waorder.suzuyagroup.com';
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
            $name = $result->results[0]->documents[0]->description;
            $name = strtoupper($name);
            $name = str_replace('  ',' ',$name);
            $name = str_replace('  ',' ',$name);
            $responsesproduct = (object)[];
            $responsesproduct->fname = $name;
            $responsesproduct->sname = substr($name,0,20);
            $responsesproduct->source = 'id.nikmart.online';
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
                $data = explode('<td>',$rows[1]);
                $nama = $data[1];
                $nama = trim( str_replace('</td>','',$nama) );
                $nama = strtoupper($nama);

                $responsesproduct = (object)[];
                $responsesproduct->fname = $nama;
                $responsesproduct->sname = substr($nama,0,20);
                $responsesproduct->source = 'harga.kpri-handayani.com';
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

            $left = '<table ';
            $right = '</table>';
            $tables = between($result, $left, $right);

            $left = '<tr>';
            $right = '<input ';
            $tables = between($tables, $left, $right);
            
            $rows = explode('</td>',$tables);

            if(count($rows)>=3){
                $data = explode('<td>',$rows[2]);
                $nama = $data[1];
                $nama = trim( str_replace('</td>','',$nama) );
                if($nama=='') return false;
                $nama = strtoupper($nama);

                $responsesproduct = (object)[];
                $responsesproduct->fname = $nama;
                $responsesproduct->sname = substr($nama,0,20);
                $responsesproduct->source = 'kopkarsentra.com';
                return $responsesproduct;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }

    private function _get_jembatanbaru($code)
    {
        try{
            $xurl   = 'http://jembatanbaru.com/store/product.php?id='.$code;
            $oapXML = '';
            $result = getcurl($xurl, $oapXML);

            if(!$result) return false;

            $left = '<div class="col-md-8 agileinfo_single_right">';
            $right = '</h2>';
            $div = between($result, $left, $right);
            
            $rows = explode('<h2>',$div);

            if(count($rows)==2){
                $nama = $rows[1];
                if($nama=='') return false;
                $nama = strtoupper($nama);

                $responsesproduct = (object)[];
                $responsesproduct->fname = $nama;
                $responsesproduct->sname = substr($nama,0,20);
                $responsesproduct->source = 'jembatanbaru.com';
                return $responsesproduct;
            }
            return false;
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

            $left = '<div class="smart_pdtitle">';
            $right = '<hr class="line">';
            $div = between($result, $left, $right);

            $left = '>';
            $right = '</a';
            $nama = between($div, $left, $right);
            
            if($nama=='') return false;
            $nama = strtoupper($nama);

            $responsesproduct = (object)[];
            $responsesproduct->fname = $nama;
            $responsesproduct->sname = substr($nama,0,20);
            $responsesproduct->source = 'mosritel.com';
            return $responsesproduct;
        }catch(\Exception $e){
            return false;
        }
    }
}