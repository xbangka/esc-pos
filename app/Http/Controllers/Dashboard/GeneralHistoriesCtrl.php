<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller,
    App\Http\Models\ChangeLog,
    Illuminate\Http\Request,
    Yajra\Datatables\Datatables;

class GeneralHistoriesCtrl extends Controller
{
    private $my_module;
    private $log;
    

    public function __construct(){
        $this->my_module    = 'general_histories';
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
            $param['getData']       = url('general-histories/data');
            $param['key_salt']      = $key_cross;

            array_push($this->js,'general_histories.js');

            $data['data']   = config('auth.udata');
            $data['menu']   = $this->my_module;
            $data['filejs'] = view_js($this->js, $param, $request);
            $data['app']    = $appid;
            
            return view('dashboard.general_histories.v_index', $data);
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
                $q    = ChangeLog::query()
                        ->select(
                            'row_id         as row_id',
                            'user_id        as user_id',
                            'module_name    as module',
                            'column         as column',
                            'old_value      as old',
                            'new_value      as new',
                            'created_at     as created'
                        )
                        ->with('users');
        
                return   $datatables->eloquent($q)
                                    ->editColumn('created', function ($x) {
                                        $date = date_create($x->modify);
                                        return date_format($date,"d F Y H:i");
                                    })
                                    ->addColumn('user', function ($x) {
                                        if($x->users==null){
                                            $user = '-';
                                        }else{
                                            if($x->users->title==1){
                                                $title = 'Mrs ';
                                            }elseif($x->users->title==2){
                                                $title = 'Ms ';
                                            }else{
                                                $title = 'Mr ';
                                            }
                                            $firstname = $x->users->firstname;
                                            $lastname = ($firstname==$x->users->lastname) ? '': $x->users->lastname;
                                            $user = $title . strtoupper(str_Decode($firstname) .' '. str_Decode($lastname));
                                        };
                                        return $user;
                                    })
                                    ->addColumn('username', function ($x) {
                                        if($x->users==null){
                                            $username = '-';
                                        }else{
                                            $username = $x->users->username;
                                        };
                                        return str_Decode($username);
                                    })
                                    ->removeColumn('user_id')
                                    ->removeColumn('users')
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

    

}