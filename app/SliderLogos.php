<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SliderLogos extends Model
{
    protected $fillable = [
        'title_logo',
        'name_logo',
        'event_id',
    ];

    public function event(){
        return $this->belongsTo(Event::class, 'event_id');
    }
}
