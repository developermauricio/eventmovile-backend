<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventUser extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'token', 'event_type_id',
    ];

    public $timestamps = true;
}
