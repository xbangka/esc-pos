<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
                            'id',
                            'uuid',
                            'barcode',
                            'full_name',
                            'short_name',
                            'description',
                            'category',
                            'created_at',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    public static $rules = array();

    public static $messages = array();

    public function categories()
    {
        return $this->hasOne('App\Http\Models\Categories', 'code','category');
    }

    public function product_prices()
    {
        return $this->hasMany('App\Http\Models\Product_Prices', 'id_product','id');
    }

}
