<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NetworkingCall extends Model
{

    const ACCEPT = 1;
    const REJECTED = 2;

    protected $fillable = ['channel', 'type', 'event_id', 'creator_id', 'guest_id' ];

}
