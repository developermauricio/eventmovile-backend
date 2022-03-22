<?php

namespace App\Http\Controllers\Api\WebApp\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\QuestionEvent;

class QuestionEventController extends Controller
{
    public function createQuestionEvent( Request $request ) {
        $question = QuestionEvent::create($request->all());

        return response()->json($question);
    }

    public function getQuestionForEvent( $event, $user ) {
        $questions = QuestionEvent::with("user")
            ->where('event_id', $event)
            ->where('user_id', $user)
            //->orderBy('created_at', 'desc')
            ->take(100)
            ->get();
        
        return response()->json($questions);
    }
}
