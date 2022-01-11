<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivitySpeaker extends Model
{
    protected $table = 'activity_speakers';

    protected $fillable = [
        'activity_id', 'speaker_id'
    ];


    public $timestamps = true;
}
