<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuestActivity extends Model
{
    //
    protected $fillable = [
        'guest_id', 'activity_id', 'payed'
    ];


    public $timestamps = true;
}
