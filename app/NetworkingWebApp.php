<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NetworkingWebApp extends Model
{
    const PENDING = 0;
    const ACCEPTED = 1;
    const REJECTED = 2;

    public function creator(){
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function guest(){
        return $this->belongsTo(User::class, 'guest_id');
    }

}
