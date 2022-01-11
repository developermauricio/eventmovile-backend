<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollQuestion extends Model
{
    protected $fillable = [
        'event_id', 'description',  'options', 'required', 'type_question_id', 'required_poll', 'position'
    ];


    public $timestamps = true;
}
