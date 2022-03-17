<?php

namespace App\Http\Controllers\Api\WebApp\Poll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PollAnswer;

class PollController extends Controller
{
    public function pollSaveWebApp(Request $request){
      
        
        $pollAnswer = json_decode($request->poll_answer);
        
        foreach ($pollAnswer as $value) {
        
                $answer = new PollAnswer;
                $answer->event_id = $request->event_id;
                $answer->poll_question_id = $value->id;
                $answer->value = $value->value;
                $answer->user_id = $request->user_id;
                $answer->save();
        
        }
        return response()->json('Las respuestas se guardaron correctamente');
    }
}
