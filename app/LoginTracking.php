<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginTracking extends Model
{
    protected $table = 'login_tracking';

    protected $fillable = [
        'event_id', 'user_id', 'actived',
    ];

}
