<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class kyc extends Model
{
    protected $table = 'kyc';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
