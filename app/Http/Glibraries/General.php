<?php

/*  
    Init for global function
    Note : Don't put function related to models in here
*/
use Illuminate\Support\Facades\Crypt,
    Illuminate\Support\Str,
    Illuminate\Http\Request,
    Ramsey\Uuid\Uuid;

function format_number($angka){ 
    $hasil =  number_format($angka,0, ',' , '.'); 
    return $hasil; 
}


function encode_for_slug($string)
    {
        $replace  = str_replace(' ', '-', $string);
        // $replace  = str_replace(".", "-",$replace);
        $replace  = str_replace("&", "-",$replace);
        $replace  = str_replace(" ", "-",$replace);
        $replace  = str_replace("  ", "-",$replace);
        $replace  = str_replace("   ", "-",$replace);
        $replace  = str_replace("$", "-",$replace);
        $replace  = str_replace("+", "-",$replace);
        $replace  = str_replace("! ", "-",$replace);
        $replace  = str_replace("@", "-",$replace);
        $replace  = str_replace("#", "-",$replace);
        $replace  = str_replace("$", "-",$replace);
        $replace  = str_replace("%", "-",$replace);
        $replace  = str_replace("^", "-",$replace);
        $replace  = str_replace("&", "-",$replace);
        $replace  = str_replace("*", "-",$replace);
        $replace  = str_replace("(", "-",$replace);
        $replace  = str_replace(")", "-",$replace);
        $replace  = str_replace("/", "-",$replace);
        $replace  = str_replace("+", "-",$replace);
        $replace  = preg_replace('/[^A-Za-z0-9\-]/', '', $replace);
        $replace  = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $replace);
        $replace = preg_replace('/-+/', '-', $replace);

        if(substr($replace, -1) == '-')
            {
                $replace = substr_replace($replace,'',-1);
            }

        return strtolower($replace);
    }



    function generateReservationCode($id){

        switch($id){
        case $id <= 999999999;
            $front = '1';
        break; 

        case $id>=1000000000 && $id<=1999999999 :
            $id = $id - 1000000000;
            $front = '2';
        break;

        case $id>=2000000000 && $id<=2999999999 :
            $id = $id - 2000000000;
            $front = '3';
        break;

        case $id>=3000000000 && $id<=3999999999 :
            $id = $id - 3000000000;
            $front = '4';
        break;

        case $id>=4000000000 && $id<=4999999999 :
            $id = $id - 4000000000;
            $front = '5';
        break;

        case $id>=5000000000 && $id<=5999999999 :
            $id = $id - 5000000000;
            $front = '6';
        break;

        case $id>=6000000000 && $id<=6999999999 :
            $id = $id - 6000000000;
            $front = '7';
        break;

        case $id>=7000000000 && $id<=7999999999 :
            $id = $id - 7000000000;
            $front = '8';
        break;

        case $id>=8000000000 && $id<=8999999999 :
            $id = $id - 8000000000;
            $front = '9';
        break;
        }

        return $front.sprintf("%'09d", $id);
    }


    function title($str){
        switch($str){
        case '0' | 0 :
            return 'Mr';
        break;
        case '1' | 1 :
            return 'Ms';
        break;
        case '2' | 2 :
            return 'Mrs';
        break; 
        }
    }




// Encript Idx
function encodeId($idx='0', $salt='=#*#*#='){

    if(config('app.env')){

        return $idx .'.'. substr(sha1($salt.$idx),5,18);

    }else{
        try {
            
            $Strrandom1 = Str::random(40); 
            $Strrandom2 = Str::random(40);  
            $Intrand = rand(1, 1000);
            $Intrand = $Intrand % 2;

            $string     = ($Intrand == 0 ) ? $idx     : $Strrandom1;
            $Strrandom1 = ($Intrand == 0 ) ? $Strrandom1 : $idx;
            
            return Crypt::encryptString($string.$salt.$Strrandom1.$salt.$Strrandom2.$salt.$Intrand);

        } catch (\exception $e) {
            return false;
        }
    }
}

function decodeId($idxstring='0000.0', $salt='=#*#*#='){

    if(config('app.env')){
        $k = explode('.',$idxstring);
        if(count($k)!=2) return false;

        $id  = $k[0];
        $md5 = $k[1];

        $mmx = substr(sha1($salt.$id),5,18);

        if($mmx!==$md5) return false;

        return $id;
    }else{
        try {
            $decode =  Crypt::decryptString($idxstring); 
            $decode = explode($salt,$decode); 
            $idx  = $decode[$decode[3]]; 
            return $idx;
        } catch (\Exception $e) {
            return false;
        }
    }
}

function encodeStr($str='xyz', $salt='*#*#*#*'){
    try {
        $Strrandom1 = Str::random(40); 
        $Strrandom2 = Str::random(40);  
        $Intrand    = rand(1, 1000);
        $Intrand    = $Intrand % 2;
        $string     = ($Intrand == 0 ) ? $str : $Strrandom1;
        $Strrandom1 = ($Intrand == 0 ) ? $Strrandom1 : $str;
        return Crypt::encryptString($string.$salt.$Strrandom1.$salt.$Strrandom2.$salt.$Intrand);
    } catch (\exception $e) {
        return false;
    }
}

function decodeStr($str='zyx', $salt='*#*#*#*'){
    try {
        $decode = Crypt::decryptString($str); 
        $decode = explode($salt,$decode);
        return $decode[$decode[3]];
    } catch (\Exception $e) {
        return false;
    }
}


function countMonth($date1='', $date2=''){
    $d1 = strtotime($date1);
    $d2 = strtotime($date2);
    $min_date = min($d1, $d2);
    $max_date = max($d1, $d2);
    $i = 1;
    while (($min_date = strtotime('+1 month', $min_date)) < $max_date) {
        $i++;
    }
    return $i;
}



function generate_code( $length=6, $sm_alpha = false, $lg_alpha = true, $number= false, $specialchar= false ){
    $characters  = " ";
    if($sm_alpha){
        $characters  .= "abcdefghijkmnopqrstuvwxyz";
    }
    if($lg_alpha){
        $characters  .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if($number){
        $characters  .= "0123456789";
    }
    if($specialchar){
        $characters  .= "!@#$%&*?";
    }
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(1, $charactersLength - 1)];
    }
    return $randomString;
}

function date2_range_format( $date1='2000-01-01' ,$date2='2000-12-31' ){
    $start_time = date_create($date1);
    $start_y    = date_format($start_time, "Y");
    $start_m    = date_format($start_time, "m");
    $start_d    = date_format($start_time, "d");
    $start_time = date_format($start_time, "d M Y");

    $end_time   = date_create($date2);
    $end_y      = date_format($end_time, "Y");
    $end_m      = date_format($end_time, "m");
    $end_time   = date_format($end_time, "d M Y");
    
    if($start_y===$end_y){
        $start_time = substr( $start_time, 0 , -5 );

        if($start_m===$end_m){
            $start_time = substr( $start_time, 0 , -4 );
        }
    }
    return $start_time . ' - ' . $end_time;
}

function tranform_phone( $number=false ){
    if($number){
        $len = strlen($number);
        if($len>=8 && $len <=13){
            $numstart = substr($number,0,1);
            if($numstart=='0'){
                $numstr = substr($number,1);
                return '+62'.$numstr;
            }else{
                return $number;
            }
        }else{
            return $number;
        }
    }
    return ;
}


function findthis($find, $arr_, $field1, $field2) {
    $result = $find;
    foreach($arr_ as $row) {
        if($row->$field1 == $find ) { return $row->$field2; exit; }
    }
    return $result;
}

function kiri($string, $count){ return substr($string, 0, $count); }

function kanan($value, $count){ return substr($value, ($count*-1)); }

function between($text, $left, $right) {
    $result = explode($left,$text);
    if(count($result)==1) {
        $result = 'NULL';
    } else  {
        $result = explode($right,$result[1]);
        $result = $result[0];
    }
    return $result;
}

function findtext($content, $find) {
    $hasil = -1;
    $content = strtoupper($content); $find = strtoupper($find);
    $hasil = strpos($content, $find );
    if($hasil == null ) { $hasil = -1; }
    return $hasil;
}

function replacebetween($text, $left, $right, $repl) {
    $result = explode($left,$text);
    if(count($result)==1) {
        $result = $text;
    } else  {
        $before = $result[0];
        $result = explode($right,$result[1]);
        $after = $result[1];
        $result = $before . $repl . $after;
    }
    return $result;
}    

function removebetween($text, $left, $right) {
    $result = explode($left,$text);
    if(count($result)==1) {
        $result = $text;
    } else  {
        $before = $result[0];
        $result = explode($right,$result[1]);
        $after = $result[1];
        $result = $before . $after;
    }
    return $result;
}    


function xRemoveBetween($Text_, $From_, $End_) {
    $result = $Text_;
    $still_run_ = 1;
    while($still_run_ > 0) {
        $a1 = strpos($result,$From_);
        if($a1>0) {
            $a2 =   strpos($result,$End_,$a1+1);
            $lash = substr($result,$a2-1,1);
            // echo "sebelum >$lash<<br>";
            if($lash== '/') {
                $removeit_ = substr($result,$a1-1,$a2-$a1);
            } else {
                $removeit_ = substr($result,$a1,$a2-$a1);
            }
            $result = str_replace($removeit_,'',$result);
        } else {
            $still_run_ = 0;
        }
    }
    return $result;
}

function cleanData($str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    return $str;
}

function xss($string) {
    return strip_tags(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));        
    // return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');        
    // return htmlspecialchars($string);        
}

function uuid4() {
    // remove tanda minus 
    $uuid = Uuid::uuid4()->toString();
    return str_replace('-','',$uuid);           
}

function basiccurl($xurl, $oapXML, $header=false) {
    $info = '';
    $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER,   0);
        curl_setopt($soap_do, CURLOPT_URL,              $xurl);
        curl_setopt($soap_do, CURLOPT_FOLLOWLOCATION,   1); 
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER,   true);
        curl_setopt($soap_do, CURLOPT_HTTPAUTH,         CURLAUTH_ANY);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,          10);
        curl_setopt($soap_do, CURLOPT_POST,             true);
        if($header) curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,       $oapXML); // the SOAP request
    $myinfo = curl_exec($soap_do);
    curl_close($soap_do);  
    $splitinfo = json_decode($myinfo,true);
    
    return $myinfo;
}

function getcurl($xurl, $oapXML) {
    $info = '';
    $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER,   0);
        curl_setopt($soap_do, CURLOPT_URL,              $xurl);
        curl_setopt($soap_do, CURLOPT_FOLLOWLOCATION,   1); 
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER,   true);
        curl_setopt($soap_do, CURLOPT_HTTPAUTH,         CURLAUTH_ANY);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,          10);
        curl_setopt($soap_do, CURLOPT_POST,             false);
    $myinfo = curl_exec($soap_do);
    curl_close($soap_do);  
    $splitinfo = json_decode($myinfo,true);
    
    return $myinfo;
}

function html_compress($kompres){
    $kompres = str_replace(array("\n","\r","\t"," ","  ","   ","    "),' ',$kompres);
    $kompres = str_replace(array("\n","\r","\t"," ","  ","   ","    "),' ',$kompres);
    $space = "";
    for ($i=0; $i < 30 ; $i++) { 
        $space .= " ";
        $kompres = str_replace($space,' ',$kompres);
        $cc = ">".$space."<" ;
        $kompres = str_replace($cc,'><',$kompres);
    }
    
    return $kompres;
}

function view_js($file=false, $param=false, $request=false){
    
    if(!$file || !is_array($file) || !$param || !$request) return false;

    if(!config('app.debug')){
        $request->session()->forget('temp_session_js');
        $request->session()->put('temp_session_js','1');
    }

    $salt           = $request->server('HTTP_USER_AGENT');
    $salt           = sha1($salt);
    $filejs['file'] = $file;
    $filejs['param']= $param;
    $formatjson     = json_encode($filejs);
    return encodeStr($formatjson,$salt);
}

function view_css($file=false, $request){
    
    if(!config('app.debug')){
        $request->session()->forget('temp_session_css');
        $request->session()->put('temp_session_css','1');
    }

    if(!$file || !is_array($file)) return false;
    $session_key    = sha1(json_encode($file));
    $session_value  = $file;

    $expiresAt      = now()->addMinutes(32160);
    if(!\Cache::has($session_key)) {
        \Cache::put($session_key, $session_value);
    }
    return $session_key;
}


function str_Encode($kata) {
    $kata = strrev($kata);
    $k = explode(' ',$kata);
    $l = [];
    for ($i = 0; $i < count($k); $i++) {
        $kata_y = $k[$i];
        $len_y = strlen($kata_y);
        if($len_y>=2){
            $len_x = ($len_y / 2);
            $len_x = (int)$len_x;
            $temp = '';
            $n = 0;
            for ($x = 0; $x < $len_x; $x++) {
                $a = substr($kata_y, $n, 1);
                $b = substr($kata_y, (1+$n), 1);
                $n = $n + 2;
                $temp = $temp . $b . $a;
            }
            if($len_y>=3) {
                $temp = $temp . substr($kata_y,$n);
            }
        }else{
            $temp = $kata_y;
        }
        array_push($l, $temp);
    }
    $kata = implode(' ',$l);
    return $kata ;
}

function str_Decode($kata) {
    $k = explode(' ',$kata);
    $l = [];
    for ($i = 0; $i < count($k); $i++) {
        $kata_y = $k[$i];
        $len_y = strlen($kata_y);
        if($len_y>=2){
            $len_x = ($len_y / 2);
            $len_x = (int)$len_x;
            $temp = '';
            $n = 0;
            for ($x = 0; $x < $len_x; $x++) {
                $a = substr($kata_y, (1+$n), 1);
                $b = substr($kata_y, $n, 1);
                $n = $n + 2;
                $temp = $temp . $a . $b;
            }
            if($len_y>=3) {
                $temp = $temp . substr($kata_y,$n);
            }
        }else{
            $temp = $kata_y;
        }
        array_push($l, $temp);
    }
    $kata = implode(' ',$l);
    $kata = strrev($kata);
    return $kata;
}

function email_Encode($email) {
    $k = explode('@',$email);
    $user = $k[0];
    $dns = $k[1];

    $user = str_Encode($user);
    $email= $user.'@'.$dns;
    return $email;
}

function email_Decode($email) {
    $k = explode('@',$email);
    $user = $k[0];
    $dns = $k[1];

    $user = str_Decode($user);
    $email= $user.'@'.$dns;
    return $email;
}
