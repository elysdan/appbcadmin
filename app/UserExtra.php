<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserExtra extends Model
{
    protected $table = 'user_extras';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
