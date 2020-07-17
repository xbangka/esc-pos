<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Order_Detail extends Model
{
    use SoftDeletes;

    protected $table = 'order_detail_xxxxx';

    public function __construct()
    {
        parent::__construct();

        if( \Session::has('udata') ) {
            $code = \Session::get('udata')->store_code;
        }elseif( \Session::has('toko') ){
            $code = \Session::get('toko')->code;
        }

        $this->table = 'order_detail_' . $code;
    }
    
    protected $fillable = [
                            'id',
                            'id_order',
                            'id_product_prices',
                            'price',
                            'qty',
                            'subtotal',
                            'created_at',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    public static $rules = array();

    public static $messages = array();

    public function orders()
    {
        return $this->hasOne('App\Http\Models\Orders', 'id','id_order');
    }

    public function product_prices()
    {
        return $this->hasOne('App\Http\Models\Product_Prices', 'id','id_product_prices');
    }

}