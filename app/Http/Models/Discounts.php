<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Discounts extends Model
{
    use SoftDeletes;

    protected $table = 'discounts';

    protected $fillable = [
                            'id',
                            'uuid',
                            'product_price_id',
                            'event_name',
                            'description',
                            'value',
                            'value_type',
                            'discount_type',
                            'status',
                            'start_date',
                            'end_date',
                            'created_at',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    public function __construct()
    {
        parent::__construct();

        if( \Session::has('udata') ) {
            $code = \Session::get('udata')->store_code;
        }elseif( \Session::has('toko') ){
            $code = \Session::get('toko')->code;
        }

        $this->table = 'discounts_' . $code;
    }

    public static $rules = array();

    public static $messages = array();

    public function product_prices()
    {
        return $this->hasOne('App\Http\Models\Product_Prices', 'id','product_price_id');
    }

}