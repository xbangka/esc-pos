<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller,
    App\Http\Models\Products,
    App\Http\Models\Categories,
    Illuminate\Http\Request,
    Yajra\Datatables\Datatables;

class ProductsCtrl extends Controller
{
    private $my_module;
    private $log;
    
    public function __construct(){
        $this->my_module    = 'products';
        $this->js           = array();
        $this->log          = new \ChangeLog;
    }

    public function index(Request $request)
    {//dd(Categories::select('code as id','name')->get());
        try{
            $key_cross = '_'.uniqid();
            $request->session()->forget('key_cross');
            $request->session()->put('key_cross',$key_cross);

            $appid  = substr(date('D'),0,1).sha1( uniqid() );

            $categories = \Cache::remember('categories_products', 60, function(){
                return Categories::select('code','name')->get()->toArray();
            });//print_r( json_encode($categories) );exit;
            $param['appid']         = $appid;
            $param['csrf_token']    = csrf_token();
            $param['insertupdatedelete'] = url('products/insert-update-delete');
            $param['getData']       = url('products/data');
            $param['key_salt']      = $key_cross;
            $param['categories']    = json_encode($categories);

            array_push($this->js,'products.js');

            $data['data']   = config('auth.udata');
            $data['menu']   = $this->my_module;
            $data['filejs'] = view_js($this->js, $param, $request);
            $data['app']    = $appid;
            
            return view('dashboard.products.v_index', $data);
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
                $q    = Products::query()
                        ->select(
                            'uuid       as uuid',
                            'barcode    as kode',
                            'full_name  as nama',
                            'short_name as nm',
                            'description as desc',
                            'category',
                            'updated_at as modify'
                        )
                        ->with('categories');
                return   $datatables->eloquent($q)
                                    ->editColumn('modify', function ($x) {
                                        if($x->modify=='') return '01.01.2020 00:00';
                                        $date = date_create($x->modify);
                                        return date_format($date,"d.m.Y H:i");
                                    })
                                    ->editColumn('categories', function ($x) {
                                        if($x->categories==null) {
                                            $obj = [];
                                            $obj['name'] = '-';
                                            return $obj;
                                        };
                                        return json_decode($x->categories,true);
                                    })
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

    public function insert_update_delete(Request $request)
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
            if($uuid_hash!=$_hash) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
            
            if($request->_delete){
                $product  = Products::where('uuid', $uuid)->first();
                $this->log->insert_costume_data('products', $row_id=$product->id, $old_value='Exist', $new_value='Delete', $column='DATA');
                Products::destroy($product->id);
                return '*OK*';
            }else{
                $code = $_q->code;
                $name = $_q->name;
                $snme = $_q->snme;
                $cate = $_q->cate;
                $desc = $_q->desc;
                if($uuid){
                    $old        = Products::where('uuid', $uuid)->first();
                    $product    = Products::where('uuid', $uuid)->firstOrFail();
                }else{
                    $product            = new Products;
                    $product->uuid      = uuid4();
                }
                $product->barcode       = $code;
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
                    return '*OK*';
                }
            }
        }catch(\Exception $e){
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
        }
    }

}