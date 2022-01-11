<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingRelUsers extends Model
{
    protected $fillable = [
        'user_id', 'meeting_id',  'state', 'acceptance'
    ];

    public function meetings()
    {
        return $this->belongsTo('App\Meeting', 'meeting_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
