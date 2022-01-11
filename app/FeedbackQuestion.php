<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedbackQuestion extends Model
{
    protected $fillable = [
        'question', 'business_id', 'type', 'options'

    ];

    public function answers(){
        return $this->hasMany('App\FeedbackAnswer','feedback_question_id','id');
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($question) { // before delete() method call this
             $question->answers()->each(function($answer) {
                $answer->delete(); // <-- direct deletion
             });        
        });
    }
}
