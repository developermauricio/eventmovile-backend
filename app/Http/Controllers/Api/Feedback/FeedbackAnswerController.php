<?php

namespace App\Http\Controllers\Api\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\FeedbackAnswer;
use App\FeedbackQuestion;
use Illuminate\Support\Facades\DB;
class FeedbackAnswerController extends Controller
{

    public function store(Request $request)
    {
        $rules = [
            'business_id'              => 'required',
            'feedback_question_id'  => 'required',
            'value'               => 'required' ,
            'user_id'               => 'required' 
        ];
        $this->validate($request, $rules);
        
        $toSave = $request->all();
        $data = FeedbackAnswer::create($toSave);

        return $this->successResponse(['data'=> $data, 'message'=>'Answer Created'], 201);
    }

    public function report($business){

        $users = DB::table('business_markets_rel_users as bmu')
        ->select('u.id','u.name', 'u.lastname','u.email','u.phone')
        ->join('users as u', 'u.id', 'bmu.user_id')
        ->where('bmu.business_id',$business)
        ->get();

        $questions = FeedbackQuestion::where('business_id', $business)->get();

        $users->map(function($user) use($questions,$business){
            $questions->map(function($question) use($business, $user){
                $answer = FeedbackAnswer::where('business_id',$business)
                ->where('user_id',$user->id)
                ->where('feedback_question_id',$question->id)
                ->first();

                if($answer)
                    $user->{$question->question} = $answer->value;
                else
                    $user->{$question->question} = "";
            });
        });

        return $this->showAll($users, 201);
    }
}
