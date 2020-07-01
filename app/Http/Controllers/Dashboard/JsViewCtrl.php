<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request,
    App\Http\Controllers\Controller;

class JsViewCtrl extends Controller
{    
    public function __construct(){}

    public function _index(Request $request)
    {
        if(!config('app.debug')){
            if(!$request->session()->has('temp_session_js')) {
                return response('',200)->header('Content-Type','application/javascript');
            }
            $request->session()->forget('temp_session_js');
        }

        $data = '';
        foreach ($request->all() as $key => $value) {
            $data = $key;
            break;
        }

        if($data==''){
            return response('',200)->header('Content-Type','application/javascript');
        };

        $salt   = sha1($request->server('HTTP_USER_AGENT'));
        $data   = decodeStr($data,$salt);
        $data   = json_decode($data);
        if(!is_object($data)){
            return response('',200)->header('Content-Type','application/javascript');
        };
        $files  = $data->file;
        $param  = $data->param;
        $filejs = '';

        $fl = [];
        foreach ($files as $file) {
            $fk = app_path() .'\Http\JS\\' . $file;
            $filejs .= file_get_contents($fk);
        }

        $pr = [];
        foreach ($param as $key => $value) {
            $pr['{{$'.$key.'}}'] = $value;
            $pr['{{ $'.$key.' }}'] = $value;
        }
        $param = $pr;

        

        $filejs = strtr($filejs,$pr);
        
        $filejs = (config('app.env')=='local') ? $filejs : html_compress($filejs);

        return response($filejs,200)->header('Content-Type','application/javascript');
    }
   
}
