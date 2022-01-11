<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'event_id', 'name', 'sort_description', 'unit_price', 'duration_minutes', 'start_date', 
        'end_date', 'pic', 'code_streaming', 'tags', 'friendly_url', 'location_coordinates', 'address', 'country_id', 'city_id',
        'guests_limit', 'type_activity_id', 'mode_id', 'pic', 'pic_banner','voice_participation_check','onDemand', 'payment'
    ];

    public function type_activity() {
        return $this->belongsTo('App\TypeActivity', 'type_activity_id', 'id');
    }

    public function mode() {
        return $this->belongsTo('App\ModeActivity', 'mode_id', 'id');
    }

    public function speakers()
    {
        return $this->belongsToMany('App\Speaker', 'activity_speakers', 'activity_id', 'speaker_id');
    }
    
    public function event() {
        return $this->belongsTo('App\Event', 'event_id');
    }

    public $timestamps = true;
}
