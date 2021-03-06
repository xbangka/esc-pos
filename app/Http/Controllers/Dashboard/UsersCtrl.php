<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller,
    App\Http\Models\Users,
    App\Http\Models\Stores,
    App\Http\Models\Statuses,
    App\Http\Models\Config_General,
    Illuminate\Http\Request,
    Yajra\Datatables\Datatables;

class UsersCtrl extends Controller
{
    private $my_module;
    private $log;
    private $js;
    
    public function __construct(){
        $this->my_module    = 'users';
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

            $stores = \Cache::remember('stores_users', 60, function(){
                $stores = Stores::select('uuid','name')->where('status','=',1)->get();
                $storesx = [];
                foreach ($stores as $row) {
                    $k = (object)[];
                    $k->uuid = $row->uuid;
                    $k->name = str_Decode($row->name);
                    array_push($storesx,$k);
                }
                return $storesx;
            });
            $statuses = \Cache::remember('statuses_users', 86400, function(){
                return Statuses::select('foreign_key','name','bgcolor')->where('module','=',$this->my_module)->get()->toArray();
            });
            $cg_email_allow = \Cache::remember('email_allow', 86400, function()
            {
                return Config_General::select('value')->where(['key' => 'email_allow', 'status' => 1])->first();
            });
            
            $email_allow = ($cg_email_allow) ? $cg_email_allow->value : '[]';

            $param['appid']         = $appid;
            $param['csrf_token']    = csrf_token();
            $param['insertupdatedelete'] = url('users/insert-update-delete');
            $param['getData']       = url('users/data');
            $param['key_salt']      = $key_cross;
            $param['stores']        = json_encode($stores);
            $param['statuses']      = json_encode($statuses);
            $param['email_allow']   = $email_allow;

            array_push($this->js,'users.js');

            $data['data']   = config('auth.udata');
            $data['menu']   = $this->my_module;
            $data['filejs'] = view_js($this->js, $param, $request);
            $data['app']    = $appid;
            
            return view('dashboard.users.v_index', $data);
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
                $q    = Users::query()
                        ->select(
                            'users.uuid       as uuid',
                            'users.title      as title',
                            'users.firstname  as fname',
                            'users.lastname   as lname',
                            'users.nameshow   as alias',
                            'users.username   as user',
                            'users.email      as mail',
                            'users.phone      as telp',
                            'users.image_path as path',
                            'users.image_file as file',
                            'users.id_store   as id_store',
                            'users.status     as status',
                            'users.updated_at as modify'
                        )
                        ->with('stores','statuses');
        
                return   $datatables->eloquent($q)
                                    ->editColumn('nama', function ($x) {
                                        $fname = str_Decode($x->fname);
                                        $lname = str_Decode($x->lname);
                                        $nama  = ($fname!=$lname) ? $fname .' '. $lname : $fname;
                                        return $nama;
                                    })
                                    ->editColumn('title', function ($x) {
                                        return $x->title.'';
                                    })
                                    ->editColumn('fname', function ($x) {
                                        return str_Decode($x->fname);
                                    })
                                    ->editColumn('lname', function ($x) {
                                        return str_Decode($x->lname);
                                    })
                                    ->editColumn('alias', function ($x) {
                                        return str_Decode($x->alias);
                                    })
                                    ->editColumn('user', function ($x) {
                                        return str_Decode($x->user);
                                    })
                                    ->editColumn('mail', function ($x) {
                                        return email_Decode($x->mail);
                                    })
                                    ->editColumn('telp', function ($x) {
                                        return str_Decode($x->telp);
                                    })
                                    ->editColumn('stores', function ($x) {
                                        if($x->stores==null) {
                                            $obj = [];
                                            $obj['uuid'] = '-';
                                            return $obj;
                                        };
                                        return json_decode($x->stores,true);
                                    })
                                    ->editColumn('stores.name', function ($x) {
                                        return str_Decode($x->stores->name);
                                    })
                                    ->editColumn('modify', function ($x) {
                                        if($x->modify=='') return '01.01.2020 00:00';
                                        $date = date_create($x->modify);
                                        return date_format($date,"d.m.Y H:i");
                                    })
                                    ->removeColumn('firstname')
                                    ->removeColumn('lastname')
                                    ->removeColumn('id_store')
                                    ->removeColumn('status')
                                    ->removeColumn('stores.id')
                                    ->removeColumn('stores.code')
                                    ->removeColumn('stores.phone')
                                    ->removeColumn('stores.address')
                                    ->removeColumn('stores.description')
                                    ->removeColumn('stores.status')
                                    ->removeColumn('stores.created_at')
                                    ->removeColumn('stores.updated_at')
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
                $user  = Users::where('uuid', $uuid)->first();
                $this->log->insert_costume_data('users', $row_id=$user->id, $old_value='Exist', $new_value='Delete', $column='DATA');
                Users::destroy($user->id);
                return '*OK*';
            }else{
                $title      = $_q->title;
                $fname      = str_Encode(strtoupper($_q->fname));
                $lname      = str_Encode(strtoupper($_q->lname));
                $nameshow   = str_Encode($_q->alias);
                $username   = str_Encode($_q->username);
                $email      = email_Encode($_q->email);
                $phone      = str_Encode($_q->phone);
                // $image_path = $_q->path;
                // $image_file = $_q->file;
                $status     = $_q->stts;
                $id_store   = $_q->store;

                $stores = Stores::select('id')->where('uuid','=',$id_store)->first();
                if(!$stores) return 'Maaf, id toko tidak ada';
                $id_store = $stores->id;

                if($uuid){
                    $ext = Users::where('uuid', '!=', $uuid)
                                ->where(function($q) use ($email,$phone,$username) {
                                    $q->where('email', '=', (($email!='') ? $email:'9999999') )
                                    ->orWhere('phone', '=', (($phone!='') ? $phone:'xxxxxx') )
                                    ->orWhere('username', '=', (($username!='') ? $username:'#!@!@%&') );
                                })->first();
                    if($ext && $ext->phone==$phone) return 'Telp sudah ada di sistem !';
                    if($ext && $ext->email==$email) return 'Email sudah ada di sistem !';
                    if($ext && $ext->username==$username) return 'Username sudah ada di sistem !';
                    
                    if($ext) return 'Maaf, tidak dapat menyimpan data.';

                    $old  = Users::where('uuid', $uuid)->first();
                    $user = Users::where('uuid', $uuid)->firstOrFail();
                }else{
                    $ext = Users::select('username', 'email', 'phone')
                                ->where(function($q) use ($email,$phone,$username) {
                                    $q->where('email', '=', (($email!='') ? $email:'9999999') )
                                    ->orWhere('phone', '=', (($phone!='') ? $phone:'xxxxxx') )
                                    ->orWhere('username', '=', (($username!='') ? $username:'#!@!@%&') );
                                })->first();
                    if($ext && $ext->phone==$phone) return 'Telp sudah ada di sistem !';
                    if($ext && $ext->email==$email) return 'Email sudah ada di sistem !';
                    if($ext && $ext->username==$username) return 'Username sudah ada di sistem !';
                    
                    if($ext) return 'Maaf, tidak dapat menyimpan data.';

                    $user           = new Users;
                    $user->uuid     = uuid4();
                    $user->password = \Hash::make(uniqid());
                }
                $user->title    = $title;
                $user->firstname= $fname;
                $user->lastname = $lname;
                $user->nameshow = $nameshow;
                $user->username = $username;
                $user->email    = $email;
                $user->phone    = $phone;
                $user->status   = $status;
                $user->id_store = $id_store;
                // $user->image_path = $image_path;
                // $user->image_file = $image_file;
                
                if(!$user->save()){
                    return 'Maaf, tidak dapat menyimpan data';
                }else{
                    if($uuid){
                        $statuses  = Statuses::select('foreign_key as id','name')->where('module','=','users')->get();
                        $optionAttributKey['status'] = $statuses;
                        $this->log->createLog($user->getChanges(), $old->getOriginal(), 'users', $old->id, $optionAttributKey);
                    }else{
                        $this->log->firstLog('users', $user->id);
                    }
                    return '*OK*';
                }
            }
        }catch(\Exception $e){
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
        }
    }
}