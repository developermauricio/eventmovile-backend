<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event(){
        return $this->belongsTo(Event::class, 'event_id');
    }
}
