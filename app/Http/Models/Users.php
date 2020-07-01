<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Model
{
    use SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
                            'id',
                            'uuid',
                            'title',
                            'firstname',
                            'lastname',
                            'nameshow',
                            'username',
                            'email',
                            'password',
                            'phone',
                            'image_path',
                            'image_file',
                            'status',
                            'id_store',
                            'pin_store',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at','token'];

    public static $rules = array();

    public static $messages = array();

    public function stores()
    {
        return $this->hasOne('App\Http\Models\Stores', 'id','id_store');
    }

    public function statuses()
    {
        return $this->hasOne('App\Http\Models\Statuses', 'foreign_key','status')
                    ->where('module','=','users');
    }

}
