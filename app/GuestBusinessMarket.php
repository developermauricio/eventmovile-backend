<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuestBusinessMarket extends Model
{
    protected $fillable = [
        'guest_id', 'business_market'
    ];


    public $timestamps = true;
}
