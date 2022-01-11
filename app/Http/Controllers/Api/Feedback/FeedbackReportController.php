<?php

namespace App\Http\Controllers\Api\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\FeedbackAnswer;

class FeedbackReportController extends Controller
{
    public function AnswersByQuestion($question){
        $answers = FeedbackAnswer::where('feedback_question_id',$question)->get(); 
        return $this->showOne($answers);
    }
}
