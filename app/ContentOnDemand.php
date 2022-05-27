<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentOnDemand extends Model
{
    protected $fillable = [
        'title_video',
        'iframe_video',
        'description_video',
        'event_id',
    ];

    public function event(){
        return $this->belongsTo(Event::class, 'event_id');
    }
    
}
