<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NetworkingChat extends Model
{
    protected $fillable = [
        'message', 'networking_id', 'creator_id', 'user_id',
    ];
}
