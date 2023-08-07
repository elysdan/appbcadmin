<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pointpaid extends Model
{
    protected $table = 'point_paid';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
