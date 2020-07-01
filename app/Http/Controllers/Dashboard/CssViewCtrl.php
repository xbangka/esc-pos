<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request,
    App\Http\Controllers\Controller;

class CssViewCtrl extends Controller
{    
    public function __construct(){}

    public function _index(Request $request)
    {
        if(!config('app.debug')){
            if(!$request->session()->has('temp_session_css')) {
                return response('',200)->header('Content-Type','text/css');
            }
            $request->session()->forget('temp_session_css');
        }

        $data_key = '';
        foreach ($request->all() as $key => $value) {
            $data_key = $key;
            break;
        }

        if($data_key==''){
            return response('',200)->header('Content-Type','text/css');
        };

        $session_key = $data_key;


        $data = [];
        if(\Cache::has($session_key)) {
            $data = \Cache::get($session_key);
        }
        
        $files = $data;

        $filecss = '';
        $fl = [];
        foreach ($files as $file) {
            $fk = app_path() .'\Http\CSS\\' . $file;
            $filecss .= file_get_contents($fk);
        }
        
        $filecss = (config('app.env')=='local') ? $filecss : html_compress($filecss);

        return response($filecss,200)->header('Content-Type','text/css');
    }
   
}
