<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    protected $fillable = [
        'name', 'event_id',  'file', 'custom_fields'
    ];

    public function event()
    {
        return $this->belongsTo('App\Event', 'event_id');
    }
}
