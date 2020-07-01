<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class Statuses extends Model
{
    use SoftDeletes;

    protected $table = 'statuses';

    protected $fillable = [
                            'id',
                            'foreign_key',
                            'name',
                            'module',
                            'bgcolor',
                            'fontcolor',
                            'created_at',
                            'updated_at'
                        ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at'];

    public static $rules = array();

    public static $messages = array();

}
