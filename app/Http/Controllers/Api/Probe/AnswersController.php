<?php

namespace App\Http\Controllers\Api\Probe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProbeAnswer;

class AnswersController extends Controller
{   
    public function AnswersByQuestion($question){
        $answers = ProbeAnswer::where('question_id',$question)->with('users')->orderBy('id', 'asc')->get();
        return $this->showAll($answers);
    }

}
