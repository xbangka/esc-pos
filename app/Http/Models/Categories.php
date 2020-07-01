<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

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

    public function products()
    {
        return $this->hasMany('App\Http\Models\Products', 'category_id','code');
    }
}
