<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class acciones extends Model
{
    protected $table = 'acciones';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
