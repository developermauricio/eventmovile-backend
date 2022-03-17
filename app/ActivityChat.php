<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityChat extends Model
{
    protected $fillable = [
        'message', 'activity_id', 'user_id',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
