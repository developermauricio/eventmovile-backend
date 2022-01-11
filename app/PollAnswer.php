<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{
    protected $fillable = [
        'event_id', 'poll_question_id', 'value', 'user_id'
    ];


    public $timestamps = true;
}
