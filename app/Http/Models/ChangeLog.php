<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{	
    protected $table = 'change_logs';
    protected $fillable = [ 
                            'user_id',
                            'old_value',
                            'new_value',
                            'module_name',
                            'column'
                        ];
    protected $hidden = ['created_at','updated_at'];

    public static $rules = array();

    public static $messages = array();

    public function users()
    {
        return $this->hasOne('App\Http\Models\Users', 'id','user_id');
    }
}