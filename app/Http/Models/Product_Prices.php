<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Product_Prices extends Model
{
    use SoftDeletes;

    protected $table = 'product_prices_xxxxx';

    public function __construct()
    {
        parent::__construct();

        if( \Session::has('udata') ) {
            $code = \Session::get('udata')->store_code;
        }elseif( \Session::has('toko') ){
            $code = \Session::get('toko')->code;
        }

        $this->table = 'product_prices_' . $code;
    }

    protected $fillable = [
                            'id',
                            'uuid',
                            'id_store',
                            'id_product',
                            'id_retail_unit',
                            'price',
                            'status',
                            'created_at',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    public static $rules = array();

    public static $messages = array();

    public function products()
    {
        return $this->hasOne('App\Http\Models\Products', 'id','id_product');
    }

    public function retail_units()
    {
        return $this->hasOne('App\Http\Models\Retail_Units', 'id','id_retail_unit');
    }

    public function discounts()
    {
        $datenow = date('Y-m-d H:i:s');
        return $this->hasMany('App\Http\Models\Discounts', 'product_price_id','id')
                    ->select('event_name','value','value_type','discount_type','condition_qty_from','condition_qty_to')
                    ->where('start_date','<=',$datenow)
                    ->where('end_date','>=',$datenow)
                    ->where('status','=',1);
    }

    public function discounts_with_status()
    {
        $datenow = date('Y-m-d H:i:s');
        return $this->hasMany('App\Http\Models\Discounts', 'product_price_id','id')
                    ->select('uuid','event_name','value','value_type','discount_type','condition_qty_from','condition_qty_to','start_date','end_date','status')
                    ->where('start_date','<=',$datenow)
                    ->where('end_date','>=',$datenow);
    }

}