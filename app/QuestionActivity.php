<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionActivity extends Model
{
    protected $fillable = [
        'question', 'user_id',  'activity_id', 'answer','type'
    ];
}
