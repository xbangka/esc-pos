<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Retail_Units extends Model
{
    use SoftDeletes;

    protected $table = 'retail_units';

    protected $fillable = [
                            'id',
                            'uuid',
                            'code',
                            'name',
                            'created_at',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    public static $rules = array();

    public static $messages = array();

}