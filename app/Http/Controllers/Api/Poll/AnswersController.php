<?php

namespace App\Http\Controllers\Api\Poll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PollAnswer;

class AnswersController extends Controller
{
    //
    public function AnswersByQuestion($question){
        $answers = PollAnswer::where('poll_question_id',$question)->get(); 
        return $this->showAll($answers);
    }
}
