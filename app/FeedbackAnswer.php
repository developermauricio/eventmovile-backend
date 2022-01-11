<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedbackAnswer extends Model
{
    protected $fillable = [
        'feedback_question_id', 'business_id', 'value', 'user_id'
    ];
}
