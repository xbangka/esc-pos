<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller,
    App\Http\Models\Stores,
    App\Http\Models\Statuses,
    Illuminate\Http\Request,
    Yajra\Datatables\Datatables;

class StoresCtrl extends Controller
{
    private $my_module;
    private $log;
    

    public function __construct(){
        $this->my_module    = 'stores';
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

            $statuses = \Cache::remember('statuses_stores', 86400, function(){
                return Statuses::select('foreign_key','name','bgcolor')->where('module','=',$this->my_module)->get()->toArray();
            });//print_r( json_encode($statuses) );exit;
            $param['appid']         = $appid;
            $param['csrf_token']    = csrf_token();
            $param['insertupdatedelete'] = url('stores/insert-update-delete');
            $param['getData']       = url('stores/data');
            $param['getUsers']      = url('stores/get-users');
            $param['key_salt']      = $key_cross;
            $param['statuses']      = json_encode($statuses);

            array_push($this->js,'stores.js');

            $data['data']   = config('auth.udata');
            $data['menu']   = $this->my_module;
            $data['filejs'] = view_js($this->js, $param, $request);
            $data['app']    = $appid;
            
            return view('dashboard.stores.v_index', $data);
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
            $key = $request->header('Accept-Dinamic-Key').'pp';

            if($request->session()->has('key_cross')) {
                $key_cross = $request->session()->get('key_cross');
            }else{
                return 'Halaman telah kadaluarsa, silakan muat ulang';
            }

            if(config('app.debug')) { $key=true;$key_cross=true; }

            if($key && $key==$key_cross){
                $q    = Stores::query()
                        ->select(
                            'stores.uuid       as uuid',
                            'stores.name       as nama',
                            'stores.code       as kode',
                            'stores.phone      as telp',
                            'stores.address    as almt',
                            'stores.description as desc',
                            'stores.status     as status',
                            'stores.updated_at as modify'
                        )
                        ->with('statuses');
                return   $datatables->eloquent($q)
                                    ->editColumn('nama', function ($x) {
                                        return str_Decode($x->nama);
                                    })
                                    ->editColumn('telp', function ($x) {
                                        return str_Decode($x->telp);
                                    })
                                    ->editColumn('almt', function ($x) {
                                        return str_Decode($x->almt);
                                    })
                                    ->editColumn('desc', function ($x) {
                                        return str_Decode($x->desc);
                                    })
                                    ->editColumn('modify', function ($x) {
                                        if($x->modify=='') return '01.01.2020 00:00';
                                        $date = date_create($x->modify);
                                        return date_format($date,"d.m.Y H:i");
                                    })
                                    ->removeColumn('status')
                                    ->removeColumn('statuses.id')
                                    ->removeColumn('statuses.foreign_key')
                                    ->removeColumn('statuses.module')
                                    ->removeColumn('statuses.created_at')
                                    ->removeColumn('statuses.updated_at')
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
                $store  = Stores::where('uuid', $uuid)->first();
                $this->log->insert_costume_data('stores', $row_id=$store->id, $old_value='Exist', $new_value='Delete', $column='DATA');
                Stores::destroy($store->id);
                return '*OK*';
            }else{
                $nama = $_q->nama;
                $telp = $_q->telp;
                $almt = $_q->almt;
                $desc = $_q->desc;
                $stts = $_q->stts;
                if($uuid){
                    $old    = Stores::where('uuid', $uuid)->first();
                    $store  = Stores::where('uuid', $uuid)->firstOrFail();
                }else{
                    $store          = new Stores;
                    $store->uuid    = uuid4();
                    $store->code    = $this->getCodeStore();
                }
                $store->name        = str_Encode($nama);
                $store->phone       = str_Encode($telp);
                $store->address     = str_Encode($almt);
                $store->description = str_Encode($desc);
                $store->status      = $stts;
                
                if(!$store->save()){
                    return 'Maaf, tidak dapat menyimpan data';
                }else{
                    if($uuid){
                        $statuses  = Statuses::select('foreign_key as id','name')->where('module','=','stores')->get();
                        $optionAttributKey['status'] = $statuses;
                        $this->log->createLog($store->getChanges(), $old->getOriginal(), 'stores', $old->id, $optionAttributKey);
                    }else{
                        $this->log->firstLog('stores', $store->id);
                    }
                    return '*OK* '.$stts;
                }
            }
        }catch(\Exception $e){
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
        }
    }

    
    public function getUsers(Request $request)
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
            
            $store = Stores::select('id')->with('users')->where('uuid', $uuid)->first();
            
            if($store){
                $users = $store->users;
                $usrs  = [];
                foreach ($users as $row) {
                    $u = (object)[];
                    $u->uuid = $row->uuid;
                    $u->nama = (trim($row->firstname)==trim($row->lastname)) ? str_Decode($row->firstname) : str_Decode($row->firstname).' '.str_Decode($row->lastname);
                    $u->mail = email_Decode($row->email);
                    $u->telp = str_Decode($row->phone);
                    $u->stts = $row->statuses->name;
                    $u->sttsbgcolor = $row->statuses->bgcolor;
                    $u->sttsfontcolor = $row->statuses->fontcolor;
                    array_push($usrs,$u);
                }
                return $usrs;
            }else{
                $err = [];
                return response($err)->header('Content-Type', 'application/json');
            }
        }catch(\Exception $e){
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
        }
    }
    
    private function getCodeStore()
    {
        $stop = false;

        while(!$stop) {
            $arr = [];
            for ($i=0; $i < 20; $i++) { 
                $code = generate_code( $length=5, $sm_alpha = false, $lg_alpha = false, $number= true, $specialchar= false );
                if( (int)$code >= 10000 ) array_push($arr,trim($code));
            }
            $exists = Stores::select('code')->whereIn('code', $arr)->pluck('code')->toArray();

            $codeSelected = '';
            foreach($arr as $r) {
                if( !in_array($r, $exists) ){
                    $codeSelected = $r;
                    break;
                }
            }
            if($codeSelected!='') $stop = true;
        } 
        return $codeSelected;
    }
}