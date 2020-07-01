<?php

namespace App\Http\Middleware;

use Closure;

class AutoSignin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $agent  = $request->server('HTTP_USER_AGENT');
        
        if($request->session()->has('xtoken')){
            $sessions       = $request->session()->get('xtoken');
            $hashChecked    = \Hash::check($agent, $sessions);
            if($hashChecked) return redirect('dashboard');
        }

        $request->session()->forget('xtoken');
        $request->session()->forget('udata');

        return $next($request);
    }
}
