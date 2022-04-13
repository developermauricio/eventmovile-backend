<?php

namespace App\Http\Controllers\Api\Poll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\PollQuestion;
use App\PollAnswer;
use Illuminate\Support\Facades\Log;
class PollQuestionController extends Controller
{
    public function index() {

        $pollQuestion = PollQuestion::all();

        return $this->showAll($pollQuestion,200);
    }

    public function show(PollQuestion $pollQuestion)
    {
    
        return $this->showOne($pollQuestion);
    }

    public function showQuestionsEvent($eventId)
    {
        //Log::info('showQuestionsEvent: ');
        //Log::info($eventId);
        if (!Auth::user()){
            //Log::info('showQuestionsEvent: ');
            $probeAnswer = PollQuestion::select('poll_questions.*', 'tq.name as type_question')
            ->where('event_id', $eventId)
            ->join('type_questions as tq', 'tq.id', '=', 'poll_questions.type_question_id')
            ->orderBy('poll_questions.position', 'desc')
            ->get();
            return $this->showAll($probeAnswer);
        }
        
        $role = auth()->user()->getRoleNames()->first();
        
        $pollQuestion = PollQuestion::select('poll_questions.*', 'tq.name as type_question')
            ->where('event_id', $eventId)
            ->join('type_questions as tq', 'tq.id', '=', 'poll_questions.type_question_id')
            ->orderBy('poll_questions.position', 'desc')
            ->get();
        if($role == "super admin" || $role == 'admin'){
            return $this->showAll($pollQuestion);
        }

        $id = Auth()->id();
        $answers = PollAnswer::where('event_id', $eventId)->where('user_id',$id)->get();
        
        if(count($answers) != 0)
            return $this->showOne('answered');
        else    
            return $this->showAll($pollQuestion);
    }

    public function store(Request $request){

        $rules = [
            'event_id'          => 'required|exists:events,id',
            'description'       => 'required|min:10',
            'type_question_id'  => 'required' 
        ];

        $this->validate($request, $rules);

        $position = PollQuestion::where('event_id', $request->event_id)->count();
        $position++;

        $pollQuestion = PollQuestion::create([
            'event_id'          => $request->event_id,
            'description'       => $request->description,
            'type_question_id'  => $request->type_question_id,
            'options'           => $request->options,    
            'position'          => $position
        ]);

        return $this->successResponse(['data'=> $pollQuestion, 'message'=>'Poll Created'], 201);
    }
    public function updatePosition(Request $request) {
    
        $rules =[
            'position' => 'required',
            'event_id' => 'required'
        ];

        $this->validate($request, $rules);

        if($request->postion != '1'){
            $nextPosition = PollQuestion::where('position', $request->position+1)
                ->where('event_id', $request->event_id)
                ->first();
            
            $currentPosition = PollQuestion::where('position', $request->position)
                ->where('event_id', $request->event_id)
                ->first();

            $nextPosition->position = $request->position;
            $nextPosition->save();

            $currentPosition->position = $request->position+1;
            $currentPosition->save();


            return $this->successResponse(['data' => $currentPosition, 'message' => 'Poll Question postion updated'],201);
        }
        else{
            return $this->errorResponse('This is the first question', 500);
        }

    }

    public function update(Request $request, PollQuestion $pollQuestion){
        
        $rules = [
            'description'       => 'required|min:10',
            'type_question_id'  => 'required'
        ];

        $this->validate($request, $rules);

                
        $pollQuestion->fill($request->all());
        
        if ($pollQuestion->isClean()) {
            return $this->successResponse(['data' => $pollQuestion, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $pollQuestion->save();

        return $this->successResponse(['data' => $pollQuestion, 'message' => 'Poll Question Updated'],201);
    }

    public function destroy(PollQuestion $pollQuestion)
    {
        $pollQuestion->delete();   
        return $this->successResponse(['data' => $pollQuestion, 'message' => 'Speaker Deleted'], 201);
    }
}
