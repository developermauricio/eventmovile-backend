<?php

namespace App\Http\Controllers\Api\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Activity;
use App\QuestionActivity;
use App\Events\QuestionEvent;
use Illuminate\Support\Facades\Log;

class QuestionActivityController extends Controller
{
    public function index(){
        $questions = QuestionActivity::all();
        return $this->showAll($questions, 200);
    }
    // query desc para traer los ultimos 100 y despues los ordenamos en front
    public function questionsForActivity ($activity, $user){
        if($user != 0){
            $questions = DB::table('question_activities as qa')
            ->select('qa.*', 'u.name', 'u.lastname')
            ->join('users as u', 'u.id', '=', 'qa.user_id')
            ->where('qa.activity_id', $activity)
            ->where('qa.user_id', $user)
            ->orderBy('qa.created_at', 'desc')
            ->take(100)
            ->get();
        }else{
            $questions = DB::table('question_activities as qa')
            ->select('qa.*', 'u.name', 'u.lastname')
            ->join('users as u', 'u.id', '=', 'qa.user_id')
            ->where('qa.activity_id', $activity)
            ->orderBy('qa.created_at', 'desc')
            ->take(100)
            ->get();
        }
        Log::info('QUESTION_ACTIVITIES');
        Log::info($questions);
        $carbon = new \Carbon\Carbon(); 
        $date = $carbon::now()->format('Y-m-d H:i:s');

        $questions->map(function($item) use($date){
            $item->currentTime = $date;
        });
        Log::info('Data');
        Log::info($questions);
        return $this->showAll($questions, 200);

    }
    public function store(Request $request){

        $rules = [
            'question'  => 'required|max:300',
            'activity_id' => 'required|exists:activities,id'
        ];

        $this->validate($request, $rules);
        $toSave = $request->all();

        $activityQuestion = QuestionActivity::create($toSave);
        // broadcast(new QuestionEvent($activityQuestion->activity_id, $activityQuestion->question));

        return $this->successResponse(['data'=> $activityQuestion, 'message'=>'Question sent'], 201);

    }
    public function questionResponse(QuestionActivity $questionActivity, Request $request){

        $rules = [
            'answer'  => 'required|max:300'
        ];
        $this->validate($request, $rules);

        $questionActivity->answer = $request->answer;
        $questionActivity->save();

        // broadcast(new QuestionEvent($questionActivity->activity_id, $questionActivity->question));

        return $this->successResponse(['data'=> $questionActivity, 'message'=>'Answer sent'], 201);

    }
    public function destroy(QuestionActivity $questionActivity){

        $questionActivity->delete();
        // broadcast(new QuestionEvent($questionActivity->activity_id, $questionActivity->question));
        return $this->successResponse(['data' => $questionActivity, 'message' => 'Question Activity Deleted'], 201);
    }
}
