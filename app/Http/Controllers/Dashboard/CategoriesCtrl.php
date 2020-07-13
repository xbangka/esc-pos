<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller,
    App\Http\Models\Categories,
    Illuminate\Http\Request,
    Yajra\Datatables\Datatables;

class CategoriesCtrl extends Controller
{
    private $my_module;
    private $log;
    

    public function __construct(){
        $this->my_module    = 'categories';
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

            $param['appid']         = $appid;
            $param['csrf_token']    = csrf_token();
            $param['insertupdatedelete'] = url('categories/insert-update-delete');
            $param['getData']       = url('categories/data');
            $param['key_salt']      = $key_cross;

            array_push($this->js,'categories.js');

            $data['data']   = config('auth.udata');
            $data['menu']   = $this->my_module;
            $data['filejs'] = view_js($this->js, $param, $request);
            $data['app']    = $appid;
            
            return view('dashboard.categories.v_index', $data);
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
                $q    = Categories::query()
                        ->select(
                            'uuid       as uuid',
                            'name       as nama',
                            'code       as kode',
                            'updated_at as modify'
                        );
        
                return   $datatables->eloquent($q)
                                    ->editColumn('modify', function ($x) {
                                        if($x->modify=='') return '01.01.2020 00:01';
                                        $date = date_create($x->modify);
                                        return date_format($date,"d.m.Y H:i");
                                    })
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
            if(!config('app.debug') && $uuid_hash!=$_hash) return 'Perhatikan, Inputan perlu di isi dengan benar !.';
            
            if($request->_delete){
                $cate  = Categories::where('uuid', $uuid)->first();
                $this->log->insert_costume_data('categories', $row_id=$cate->id, $old_value='Exist', $new_value='Delete', $column='DATA');
                Categories::destroy($cate->id);
                return '*OK*';
            }else{
                $code = substr($_q->code,0,10);
                $name = $_q->name;
                if($uuid){
                    $ext = Categories::where([['code', '=', $code],['uuid', '!=', $uuid]])->first();
                    if($ext) return 'Kode unik sudah ada !';

                    $old  = Categories::where('uuid', $uuid)->first();
                    $cate = Categories::where('uuid', $uuid)->firstOrFail();
                }else{
                    $ext = Categories::where('code', $code)->first();
                    if($ext) return 'Kode unik sudah ada !';

                    $cate           = new Categories;
                    $cate->uuid     = uuid4();
                }
                $cate->code = $code;
                $cate->name = $name;
                
                if(!$cate->save()){
                    return 'Maaf, tidak dapat menyimpan data';
                }else{
                    if($uuid){
                        $this->log->createLog($cate->getChanges(), $old->getOriginal(), 'categories', $old->id);
                    }else{
                        $this->log->firstLog('categories', $cate->id);
                    }
                    return '*OK*';
                }
            }
        }catch(\Exception $e){
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
        }
    }
}