<?php

namespace App\Http\Middleware;

use Closure, Config;

class CheckAPIToken
{
    public function handle($request, Closure $next)
    {
        $token = $request->input('token');
        $request->token_invalid = false;
        if($request->session()->has('token')){
            $sessions   = $request->session()->get('token');
            if($sessions!==$token) $request->token_invalid = "Token Invalid 1";

            $self   = $request->session()->get('self');
            $request->dataVoter = $self;

        }else{
            $voter = \App\Http\Models\Voter::select('id','jk','nama')->where('token','=',$token)->first();
            if(!isset($voter->id)) $request->token_invalid = "Token Invalid 2";
            
            $request->session()->forget('token');
            $request->session()->put('token',$token);

            $self       = (object)[];
            $self->id   = $voter->id;
            $self->jk   = $voter->jk;
            $self->nama = $voter->nama;
            $request->session()->forget('self');
            $request->session()->put('self',$self);
            $request->dataVoter = $self;
        }
       
        return $next($request);
    }
}
