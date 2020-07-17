<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use SoftDeletes;

    protected $table = 'orders_xxxxx';

    public function __construct()
    {
        parent::__construct();

        if( \Session::has('udata') ) {
            $code = \Session::get('udata')->store_code;
        }elseif( \Session::has('toko') ){
            $code = \Session::get('toko')->code;
        }

        $this->table = 'orders_' . $code;
    }

    protected $fillable = [
                            'id',
                            'uuid',
                            'invoice',
                            'id_customer',
                            'id_user',
                            'total',
                            'json_detail',
                            'status',
                            'created_at',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    public static $rules = array();

    public static $messages = array();

    // public function customer()
    // {
    //     return $this->hasOne('App\Http\Models\Customers', 'id','id_customer');
    // }

    public function order_detail()
    {
        return $this->hasMany('App\Http\Models\Order_Detail', 'id_order','id');
    }

    public function users()
    {
        return $this->hasOne('App\Http\Models\Users', 'id','id_user');
    }

}