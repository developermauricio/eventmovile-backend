<?php

namespace App\Http\Controllers\Api\Poll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\PollAnswer;

class PollAnswerController extends Controller
{
    public function index() {

        $pollAnswer = PollAnswer::all();

        return $this->showAll($pollAnswer,200);
    }

    public function show(PollAnswer $pollAnswer)
    {
    
        return $this->showOne($pollAnswer);
    }

    public function store(Request $request){

        $rules = [
            'event_id'              => 'required',
            'poll_question_id'  => 'required',
            'value'               => 'required' ,
            'user_id'               => 'required' 
        ];
        $this->validate($request, $rules);
        
        $toSave = $request->all();
        $pollAnswer = PollAnswer::create($toSave);

        return $this->successResponse(['data'=> $pollAnswer, 'message'=>'Speaker Created'], 201);
    }

    public function update(Request $request, PollAnswer $pollAnswer){
        
                        
        $pollAnswer->fill($request->all());
        
        if ($pollAnswer->isClean()) {
            return $this->successResponse(['data' => $pollAnswer, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $pollAnswer->save();

        return $this->successResponse(['data' => $pollAnswer, 'message' => 'Poll Question Updated'],201);
    }

    public function destroy(PollAnswer $pollAnswer)
    {
        $pollAnswer->delete();   
        return $this->successResponse(['data' => $pollAnswer, 'message' => 'Speaker Deleted'], 201);
    }

    public function exportPoll($event_id){

        $dataexport = DB::table('poll_answers as pa')
            ->select('u.name as user', 'u.email', 'pq.description as question', 'pa.value as answer', 'pq.options')
            ->join('poll_questions as pq', 'pq.id', '=', 'pa.poll_question_id')
            ->join('users as u', 'u.id', '=', 'pa.user_id')
            ->where('pa.event_id', $event_id)
            ->get();
            
        return $this->showAll($dataexport);

        
    }
}
