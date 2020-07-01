<?php
namespace App\Http\Glibraries;
use App\Http\Models\ChangeLog as Log;

class ChangeLog {

    public function __construct(){}

    static public function firstLog($module=null, $row_id=null) {
        $id  = @config('auth.udata')->id;

        if($id && $module && $row_id){
            $log = new Log;
            $log->user_id       = $id;
            $log->old_value     = 'NULL';
            $log->new_value     = 'Create';
            $log->module_name   = $module;
            $log->row_id        = $row_id;
            $log->column        = 'DATA';
            $log->save();
        }
    }

    static public function createLog($row_new=null, $row_old=null, $module=null, $row_id=null,$optionAttributKey=null) {
        $id = @config('auth.udata')->id;

        if($id && $row_new && $row_old && $module && $row_id){
            foreach ($row_new as $key => $new_value) {
                if( ($row_old[$key] != $new_value) && ($key != 'created_at') && ($key != 'updated_at') && ($key != 'deleted_at') ){
                    if($optionAttributKey){
                        if( isset($optionAttributKey[$key]) ){
                            $string_val_new = 'NULL';
                            $string_val_old = 'NULL';
                            foreach ($optionAttributKey[$key] as $r) {
                                if($new_value==$r->id)
                                    $string_val_new = $r->name;
                                if($row_old[$key]==$r->id)
                                    $string_val_old = $r->name;
                            }
                        }else{
                            $string_val_new = $new_value;
                            $string_val_old = $row_old[$key];
                        }
                    }else{
                        $string_val_new = $new_value;
                        $string_val_old = $row_old[$key];
                    }
                    $log = new Log;
                    $log->user_id       = $id;
                    $log->old_value     = ($string_val_old!=null) ? $string_val_old : '-';
                    $log->new_value     = ($string_val_new!=null) ? $string_val_new : '-';;
                    $log->module_name   = $module;
                    $log->row_id        = $row_id;
                    $log->column        = $key;
                    $log->save();
                }
            }
        }
    }

    static public function getLog($module=null,$row_id=null) {
        if($module && $row_id){ 
            $result = Log::where([
                ['module_name'  ,$module],
                ['row_id'       ,$row_id]
            ])
            ->orderBy('created_at', 'desc')
            ->get();
            return $result;
        }
        return;
    }


    ///////////////////   Insert data costume String   ////////////////////

    static public function insert_costume_data($module=null, $row_id=null, $old_value=null, $new_value=null, $column=null) {
        $id  = @config('auth.udata')->id;

        if($id && $module && $row_id && $old_value && $new_value && $column){
            $log = new Log;
            $log->user_id       = $id;
            $log->old_value     = $old_value;
            $log->new_value     = $new_value;
            $log->module_name   = $module;
            $log->row_id        = $row_id;
            $log->column        = $column;
            $log->save();
        }
    }

}