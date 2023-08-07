<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class rankpaid extends Model
{
    protected $guarded = ['id'];
    protected $table = 'rand_paid';
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
