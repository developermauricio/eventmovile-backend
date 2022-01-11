<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Networking extends Model
{
    protected $fillable = [
        'creator_id', 'guest_id', 'confirmed', 'activity_id', 'zoom_id', 'zoom_pw' 
    ];

    public $timestamps = true;
}
