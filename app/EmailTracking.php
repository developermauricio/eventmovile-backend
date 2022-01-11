<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTracking extends Model
{
    protected $table = 'email_tracking';

    protected $fillable = [
        'event_id', 'user_id', 'actived',
    ];
}
