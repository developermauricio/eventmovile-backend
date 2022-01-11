<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersVip extends Model
{
    //
    protected $fillable = [
        'user_id', 'event_id', 'hall_id'
    ];
}
