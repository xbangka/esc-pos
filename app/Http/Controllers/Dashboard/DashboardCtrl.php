<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller,
    Illuminate\Http\Request;

class DashboardCtrl extends Controller
{
    private $my_module;

    public function __construct(){
        $this->my_module    = 'dashboard';
    }

    public function _index(Request $request)
    {
        // dd(config('auth.udata'));
        // $key  = 'Rahasia Dedi Rudiyanto';
        // $key  = 'Keterangan';
        // $key  = 'toaniyudR diDe iaasahR';
        
        // dd(str_Encode($key));
        
        $weather = \Cache::remember('weather', 3600, function()
        {
            try{
                $xurl   = 'https://api.weather.com/v3/location/point?apiKey=d522aa97197fd864d36b418f39ebb323&format=json&geocode=-6.80%2C108.63&language=id-ID';
                $oapXML = '';
                $result1 = getcurl($xurl, $oapXML);
                $result1 = json_decode($result1,false);
                $xurl   = 'https://api.weather.com/v2/turbo/vt1observation?apiKey=d522aa97197fd864d36b418f39ebb323&format=json&geocode=-6.79%2C108.63&language=id-ID&units=m';
                $oapXML = '';
                $result2 = getcurl($xurl, $oapXML);
                $result2 = json_decode($result2,false);

                $response = (object)[];
                $response->location     = $result1->location->neighborhood;
                $response->phrase       = $result2->vt1observation->phrase;
                $response->temperature  = $result2->vt1observation->temperature;
                return $response;
            }catch(\Exception $e){
                $response = (object)[];
                $response->location     = 'Not Location';
                $response->phrase       = '-';
                $response->temperature  = '-';
                return $response;
            }
        });
        
        $data['data']       = config('auth.udata');
        $data['weather']    = $weather;
        $data['menu']       = $this->my_module;
        return view('dashboard.v_index', $data);
    }

    public function _logout(Request $request)
    {
        $request->session()->forget('xtoken');
        $request->session()->forget('udata');
        $request->session()->forget('toko');
        $request->session()->forget('user');
        $request->session()->flush();
        return redirect('admin');
    }
}
