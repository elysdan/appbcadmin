<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prew_withdraw extends Model
{
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //
}
