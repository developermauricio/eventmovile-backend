<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityTracking extends Model
{
    protected $table = 'activity_tracking';

    protected $fillable = [
        'activity_id', 'user_id', 
    ];
}
