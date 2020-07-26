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

        $udata = config('auth.udata');
        $store_code = $udata->store_code;
        
        $stores = \Cache::remember('total_store', /*76800*/1, function(){
            return $this->_get_total_store();
        });
        
        $trxs = \Cache::remember('total_trx'.$store_code, /*3600*/1, function() use($store_code,$stores) {
            return $this->_get_total_transaction($store_code,$stores);
        });

        $new_product = \Cache::remember('new_product', /*300*/1, function() {
            return $this->_get_new_product_this_month();
        });
        
        $weather = \Cache::remember('weather', 3600, function() { return $this->_get_weather(); });
        
        $data['data']       = $udata;
        $data['n_store']    = count($stores);
        $data['trxs']       = ($trxs) ? format_number($trxs->count_item) : 0;
        $data['reve']       = ($trxs) ? format_number($trxs->sum_total) : 0;
        $data['weather']    = $weather;
        $data['new_products']= ($new_product) ? $new_product : [];
        $data['menu']       = $this->my_module;
        return view('dashboard.v_index', $data);
    }

    private function _get_weather()
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
    }


    private function _get_total_transaction($store_code='',$stores=false)
    {
        if(!$stores) return false;
        try{
            $result = (object)[];
            if($store_code!=''){
                $total = \App\Http\Models\Orders::select('total')->where('status','=',1)->get();
                $result->count_item = count($total);
                $result->sum_total = $total->sum('total');
            }else{
                $udata = config('auth.udata');
                $count_item = 0;
                $sum_total = 0;
                foreach ($stores as $row) {
                    $code = $row->code;
                    $udata->store_code = $code;
                    \Session::set('udata',$udata);
                    $order = \App\Http\Models\Orders::select('total')->where('status','=',1)->get();
                    $count_item = $count_item + count($order);
                    $sum_total = $sum_total + $order->sum('total');
                }
                $result->count_item = $count_item;
                $result->sum_total = $sum_total;
                $udata->store_code = $store_code;
                \Session::set('udata',$udata);
            }
            return $result;
        }catch(\Exception $e){
            return false;
        }
    }

    private function _get_total_store()
    {
        try{
            return \App\Http\Models\Stores::where('status','=',1)->get();
        }catch(\Exception $e){
            return false;
        }
    }

    private function _get_new_product_this_month()
    {
        try{
            $ddate = \Carbon\Carbon::now()->addMonths(-1)->format('Y-m-d') . ' 00:00:00';
            $products = \App\Http\Models\Products::where('created_at','>',$ddate)->orderBy('created_at','desc')->get();
            if(count($products)==0){
                $products = \App\Http\Models\Products::skip(0)->take(5)->orderBy('created_at','desc')->get();
            }
            return $products;
        }catch(\Exception $e){
            return false;
        }
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
