<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'title', 'description',  'start', 'end', 'state', 'creator_id','zoom_meeting_id', 'business_id'
    ];

    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'creator_id');
    }
    public function guests()
    {
        return $this->hasMany('App\MeetingRelUsers', 'meeting_id', 'id');
    }

    public function business()
    {
        return $this->hasMany('App\BusinessMarket', 'id', 'business_id');
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($meeting) { // before delete() method call this
             $meeting->guests()->each(function($guest) {
                $guest->delete(); // <-- direct deletion
             });
             
        });
    }

    public function guestsUser(){
        return $this->belongsToMany('App\User','meeting_rel_users','meeting_id', 'user_id');
    }
}
