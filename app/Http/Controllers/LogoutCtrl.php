<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutCtrl extends Controller
{
    public function __construct(){ }

    public function _logout(Request $request)
    {
        $request->session()->forget('xtoken');
        $request->session()->forget('udata');
        $request->session()->forget('toko');
        $request->session()->forget('user');
        $request->session()->flush();
        return redirect('/');
    }

}
