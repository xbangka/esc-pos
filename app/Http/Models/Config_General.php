<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Config_General extends Model
{
    use SoftDeletes;

    protected $table = 'config_general';

    protected $fillable = [
                            'id',
                            'key',
                            'value',
                            'status',
                            'created_at',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    public static $rules = array();

    public static $messages = array();

}
