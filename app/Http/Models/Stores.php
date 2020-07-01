<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Stores extends Model
{
    use SoftDeletes;

    protected $table = 'stores';

    protected $fillable = [
                            'id',
                            'uuid',
                            'code',
                            'name',
                            'phone',
                            'address',
                            'description',
                            'status',
                            'created_at',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    public static $rules = array();

    public static $messages = array();


    public function users()
    {
        return $this->hasMany('App\Http\Models\Users', 'id_store','id');
    }

    public function statuses()
    {
        return $this->hasOne('App\Http\Models\Statuses', 'foreign_key','status')
                    ->where('module','=','stores');
    }

}
