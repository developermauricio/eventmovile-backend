<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    protected $fillable = [
        'name', 'description', 'activities', 'creator_id', 'event_id', 'pic', 'pic_banner', 'hall_type_id', 'domain_external','location'
    ];

    public function activities1(){
        return $this->hasMany(Activity::class,'event_id', 'event_id');
    }
}
