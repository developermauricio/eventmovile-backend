<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RateActivity extends Model
{
    protected $fillable = ['user_id', 'activity_id', 'rate', 'event_id'];
    protected $table = "rate_activities";

    public function event(){
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function activity(){
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
