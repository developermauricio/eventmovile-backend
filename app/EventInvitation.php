<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventInvitation extends Model
{
    protected $fillable = [
        'event_id', 'activities', 'email', 'name', 'quantity'
    ];

    public $timestamps = true;
}
