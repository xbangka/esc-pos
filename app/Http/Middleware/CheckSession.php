<?php

namespace App\Http\Middleware;

use Closure, Config;

class CheckSession
{
    public function handle($request, Closure $next)
    {
        $agent      = $request->server('HTTP_USER_AGENT');

        if(!$request->session()->has('xtoken')){
            $request->session()->forget('xtoken');
            $request->session()->forget('udata');
            return redirect('/admin');
        }else{
            $sessions       = $request->session()->get('xtoken');
            $hashChecked    = \Hash::check($agent, $sessions);
            if(!$hashChecked) {
                $request->session()->forget('xtoken');
                $request->session()->forget('udata');
                return redirect('/admin');
            }
        }

        if(!$request->session()->has('udata'))
            return redirect('/admin');

        $udata = $request->session()->get('udata');
        $token = $request->session()->get('xtoken');

        $request->session()->forget('xtoken');
        $request->session()->forget('udata');

        $request->session()->put('udata',$udata);
        $request->session()->put('xtoken',$token);
        
        Config::set('auth.udata', $udata);

        $request->udata = $udata;

        return $next($request);
    }
}
