<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller,
    Illuminate\Http\Request,
    App\Http\Models\Products,
    App\Http\Models\Product_Prices;

class TestCtrl extends Controller
{
    private $js;
    private $css;

    public function __construct(){

        $version = config('app.env')=='local' ? '_dev' : '';

        $this->js = array(
                            'axios.min.js',
                            'vue'.$version.'.js',
                            'repobarcode.js');
        $this->css = array(
                            'bootstrap.min.css',
                            'all.css');
    }

    public function _index(Request $request)
    {
        $key_cross = '_'.uniqid();
        $request->session()->forget('key_cross');
        $request->session()->put('key_cross',$key_cross);

        $appid  = substr(date('D'),0,1).sha1( uniqid() );

        $param['appid']     = $appid;
        $param['getval']    = url('get-val');
        $param['key_salt']  = $key_cross;

        $data['filecss']    = view_css($this->css, $request);
        $data['filejs']     = view_js($this->js, $param, $request);
        $data['app']        = $appid;

        return view('pos.v_repo_barcode',$data);
    }


    public function _get_val(Request $request)
    {
        $start = $request->input('start');
        $xurl = 'http://127.0.0.1/Ritel.html?start='.$start;
        // $xurl = 'http://data.izulthea.com/barcode/index.html?start='.$start;
        $oapXML = '';
        $result = basiccurl($xurl, $oapXML);

        $left = '<table class="table table-bordered" style="margin-bottom: 10px">';
        $right = '</table>';
        $tables = between($result, $left, $right);

        $rows = explode('<tr>',$tables);
        

        $list_barang = [];

        $arrbarcode = [];

        for ($i=2; $i < count($rows) ; $i++) { 
            $values = explode('<td>',$rows[$i]);
            
            $barcode = $values[5];
            $namabarang = $values[2];
            $kategori = $values[3];

            $barcode = removebetween($barcode,'</td>','</tr>');
            $barcode = trim( str_replace('</td>','',$barcode) );
            $namabarang = trim( str_replace('</td>','',$namabarang) );
            $kategori = trim( str_replace('</td>','',$kategori) );

            if($barcode!='' && $namabarang!=''){
                $barang = (object)[];
                $barang->barcode = $barcode;
                $barang->namabarang = $namabarang;
                $barang->kategori = $kategori;
                array_push($arrbarcode,$barcode);
                array_push($list_barang,$barang);
            }
        }

        $exists = Products::select('barcode')
                            ->whereIn('barcode',$arrbarcode)
                            ->pluck('barcode')
                            ->toArray();
        
        $tgl  = date('Y-m-d H:i:s');
        $data = [];
        foreach ($list_barang as $v) {
            if(!in_array($v->barcode, $exists)){
                $ins = array(
                    'barcode'       => $v->barcode,
                    'full_name'     => $v->namabarang,
                    'short_name'    => substr($v->namabarang,0,20),
                    'description'   => '',
                    'category_id'   => $v->kategori,
                    'created_at'    => $tgl,
                );
                $data[] = $ins;
            }
        }

        
        if( count($data)>=1 ){
            Products::insert($data);
        }


        $pk = Products::select('id')->get();

        $result = [];
        $result['data_rows'] = $pk->count();
        $result['data_repo'] = $data;

        return $result;
    }



    
    public function _prices(Request $request)
    {
        $key_cross = '_'.uniqid();
        $request->session()->forget('key_cross');
        $request->session()->put('key_cross',$key_cross);

        $appid  = substr(date('D'),0,1).sha1( uniqid() );

        $param['appid']     = $appid;
        $param['get_upc']   = url('get-upc');
        $param['get_prices']= url('get-prices');
        $param['key_salt']  = $key_cross;

        $version = config('app.env')=='local' ? '_dev' : '';
        $js = array(
                    'axios.min.js',
                    'vue'.$version.'.js',
                    'getprices.js');
        $css = array('bootstrap.min.css');
                    
        $data['filecss']    = view_css($css, $request);
        $data['filejs']     = view_js($js, $param, $request);
        $data['app']        = $appid;

        return view('pos.v_repo_prices',$data);
    }

    
    public function _get_upc()
    {
        $produk = Products::select('barcode','full_name')
                            ->where('id','>',50160)
                            ->whereRaw('LENGTH(barcode) >=13')
                            ->doesnthave('product_prices')
                            ->skip(0)
                            ->take(100)
                            ->get();
        if(count($produk)>=1){
            $result = [];
            foreach ($produk as $row) {
                if(strlen($row->barcode) >= 13){
                    $k = (object)[];
                    $k->barcode = $row->barcode;
                    $k->name = $row->full_name;
                    $k->price = '-';
                    array_push($result,$k);
                }
            } 
            return $result;
        }else{
            return '';
        }
    }

    public function _get_prices(Request $request)
    {
        $ean13    = $request->input('EAN13');
        // $xurl = 'http://127.0.0.1/laravel6/pos_item_table.php.html';
        $xurl   = 'http://harga.kpri-handayani.com/pos_item_table.php?q='.$ean13;
        $oapXML = '';
        $result = basiccurl($xurl, $oapXML);

        $response = [];
        $response['barcode']  = $ean13;

        if(!$result){
            $response['price']    = 'error';
            return $response;
        }

        $left = '<table border="0" cellpadding=1 cellspacing=1 class="table_tampil">';
        $right = '</table>';
        $tables = between($result, $left, $right);
        
        $rows = explode('</tr>',$tables);

        $price = 0;

        if(count($rows)>=3){

            $values = explode('<td>',$rows[1]);
            
            $values = $values[3];
    
            $values = explode('<td align=right>',$values);
    
            $price = $values[1];
    
            $price = trim( str_replace('</td>','',$price) );
    
            $price = str_replace('.','',$price);
    
            $price = number_format($price,2,".","");
        }
        

        
        
        $exists = Products::select('uuid')
                            ->where('barcode',$ean13)
                            ->first();
        $tgl  = date('Y-m-d H:i:s');

        $product_prices = new Product_Prices; 
        $product_prices->uuid           = uuid4();
        $product_prices->id_store       = 'd525d6f979e04f66a4a16cd2dd2ca6fd';
        $product_prices->id_product     = $exists->uuid;
        $product_prices->id_retail_unit = 'b087f978fd5041648527071819ffdbd0';
        $product_prices->price          = $price;
        $product_prices->status         = 1;
        $product_prices->created_at     = $tgl;
        $product_prices->updated_at     = $tgl;

        if($product_prices->save()){
            $response['price']    = (int)$price;
            return $response;
        }else{
            $response['price']    = 0;
            return $response;
        }

        
    }




    public function _gen_uuid_view(Request $request)
    {
        $key_cross = '_'.uniqid();
        $request->session()->forget('key_cross');
        $request->session()->put('key_cross',$key_cross);

        $appid  = substr(date('D'),0,1).sha1( uniqid() );

        $param['appid']     = $appid;
        $param['getuuid']   = url('gen-uuid');
        $param['key_salt']  = $key_cross;

        $version = config('app.env')=='local' ? '_dev' : '';
        $js = array(
                    'axios.min.js',
                    'vue'.$version.'.js',
                    'genuuid.js');
        $css = array('bootstrap.min.css');
                    
        $data['filecss']    = view_css($css, $request);
        $data['filejs']     = view_js($js, $param, $request);
        $data['app']        = $appid;

        return view('pos.v_generate_uuid',$data);
    }

    public function _gen_uuid(Request $request)
    {
        try{
            $produk_empty_uuid = Products::select('id')
                                ->where('uuid','=','')
                                ->skip(0)
                                ->take(200)
                                ->get();
            // dd($produk_empty_uuid);
            if( count($produk_empty_uuid)==0 ){
                return 'finish';
            }
            foreach ($produk_empty_uuid as $row) {
                Products::where('id','=',$row->id)
                        ->update(['uuid' => uuid4()]);  
            }
    
            return 'OK';
        }catch(\Exception $e){
            return $e;
        }
    }


    public function _barcode_view(Request $request)
    {
        $key_cross = '_'.uniqid();
        $request->session()->forget('key_cross');
        $request->session()->put('key_cross',$key_cross);

        $appid  = substr(date('D'),0,1).sha1( uniqid() );

        $param['appid']     = $appid;
        $param['getsvg']    = url('u-product-id');
        $param['key_salt']  = $key_cross;

        $version = config('app.env')=='local' ? '_dev' : '';
        $js = array(
                    'axios.min.js',
                    'vue'.$version.'.js',
                    'u_product_id.js');
        $css = array('bootstrap.min.css');
                    
        $data['filecss']    = view_css($css, $request);
        $data['filejs']     = view_js($js, $param, $request);
        $data['app']        = $appid;

        return view('pos.v_product_id',$data);
    }

    public function _update_product_id(Request $request)
    {
        $start = $request->input('start',0);
        
        $xx = new Product_Prices;
        $xx = $xx->setTable('product_prices_18307');
        $xx = $xx->select('id','id_product')
            ->whereRaw('LENGTH(id_product) >=28')
            ->orderBy('id','asc')
            ->with('products')
            ->skip($start)
            ->take(10)
            ->get();

            // dd($xx[0]->toArray());


        if(count($xx)==0){
            return 'finish';
        }

        foreach ($xx as $row) {
            if(isset($row->products)){
                $yy = new Product_Prices;
                $yy = $yy->setTable('product_prices_18307');
                $yy = $yy->where('id', $row->id);
                $yy = $yy->update(array('id_product' => $row->products->id));
            }
        }

        return ($start+200);
    }


    public function _get_similiar(Request $request)
    {
        $key_cross = '_'.uniqid();
        $request->session()->forget('key_cross');
        $request->session()->put('key_cross',$key_cross);

        $appid  = substr(date('D'),0,1).sha1( uniqid() );

        $param['appid']         = $appid;
        $param['get_handayani'] = url('get-handayani');
        $param['save_product']  = url('save-new-pro');
        $param['csrf_token']    = csrf_token();
        $param['key_salt']      = $key_cross;

        $version = config('app.env')=='local' ? '_dev' : '';
        $js = array(
                    'axios.min.js',
                    'vue'.$version.'.js',
                    'getsimiliar.js');
        $css = array('bootstrap.min.css');
                    
        $data['filecss']    = view_css($css, $request);
        $data['filejs']     = view_js($js, $param, $request);
        $data['app']        = $appid;

        return view('pos.v_similiar',$data);
    }
    
    public function _get_handayani(Request $request)
    {
        try{
            $word   = $request->input('q','');
            $xurl   = 'http://harga.kpri-handayani.com/pos_item_table.php?q='.$word;
            // $xurl = 'http://127.0.0.1/laravel7/esc_pos/public/r.html';
            $header = [];
            array_push($header, 'Host: harga.kpri-handayani.com');
            array_push($header, 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:74.0) Gecko/20100101 Firefox/74.0');
            array_push($header, 'Origin: http://harga.kpri-handayani.com');
            array_push($header, 'Connection: keep-alive');
            array_push($header, 'Referer: http://harga.kpri-handayani.com/');
            array_push($header, 'Cookie: TOKO=9qbms6kfu2fjq07s2sja1vavc7');
            $result = basiccurl($xurl, '',$header);

            if(!$result) return false;

            $left = '<table border="0" cellpadding=1 cellspacing=1 class="table_tampil">';
            $right = '</table>';
            $tables = between($result, $left, $right);
            
            $rows = explode('</tr>',$tables);//dd($rows);

            $responses = [];
            $arrcode = [];

            foreach ($rows as $row) {
                $data = explode('<td>',$row);
                if(count($data)>=6){
                    $code = $data[2];
                    $code = trim( str_replace('</td>','',$code) );
                    $code = $code."";

                    if(is_numeric($code)){
                        $nama = $data[1];
                        $nama = trim( str_replace('</td>','',$nama) );
                        $nama = strtoupper($nama);
                        
                        $x = (object)[];
                        $x->code = $code;
                        $x->name = strtoupper($nama);
                        $x->exist = false;
                        $x->loading = false;
                        array_push($responses,$x);
                        array_push($arrcode,$code);
                    }
                }
            }

            if(count($arrcode)>=1){
                $products = Products::select('barcode')->whereIn('barcode',$arrcode)->pluck('barcode')->toArray();
                if(count($products)>=1){
                    $n = count($responses);
                    for ($i=0; $i < $n; $i++) { 
                        if(in_array($responses[$i]->code,$products)){
                            $responses[$i]->exist = true;
                        }
                    }
                }
            }
            
            return $responses;
        }catch(\Exception $e){
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
            // echo '<pre>';
            // return $e.'';
        }
    }

    public function _save_new_handayani(Request $request)
    {   
        try{
            $code           = $request->input('_code',false);
            $name           = $request->input('_name',false);
            $p              = new Products;
            $p->uuid        = uuid4();
            $p->barcode     = $code;
            $p->full_name   = substr($name,0,42);
            $p->short_name  = substr($name,0,20);
            if($p->save()){
                return 'OK';
            }
        }catch(\Exception $e){
            return (config('app.debug')) ? $e->getMessage() : 'Error Exception in try action';
            // echo '<pre>';
            // return $e.'';
        }
    }
    
}
