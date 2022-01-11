<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetChat extends Model
{
    protected $fillable = [
        'message', 'meet_id', 'user_id',
    ];
}
