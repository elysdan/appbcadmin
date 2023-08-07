<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallets extends Model
{
    protected $table = 'user_wallets';
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
