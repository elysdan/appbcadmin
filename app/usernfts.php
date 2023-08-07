<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class usernfts extends Model
{
    protected $guarded = ['id'];
    protected $table = 'user_nfts';
}
