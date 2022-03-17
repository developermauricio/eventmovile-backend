<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventChat extends Model
{
    protected $fillable = [
        'message', 'event_id', 'user_id',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
