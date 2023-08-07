<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class especial extends Model
{
    protected $guarded = ['id'];
    protected $table = 'sorteo_especial';
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
