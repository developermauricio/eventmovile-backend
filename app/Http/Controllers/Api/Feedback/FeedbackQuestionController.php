<?php

namespace App\Http\Controllers\Api\Feedback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\FeedbackQuestion;
use App\FeedbackAnswer;

class FeedbackQuestionController extends Controller
{
    public function index()
    {
        
        $role = auth()->user()->getRoleNames()->first();
        $probes = FeedbackQuestion::all();
        if($role == "super admin" || $role == 'admin'){
            return $this->showAll($probes,200);
        }

        $user = auth()->user();
        $answers = FeedbackAnswer::where('user_id',$user->id)->first();
        $rta = ['questions'=>$probes];
        if($answers){
            $rta['answered']='true';
        }   else {
            $rta['answered']='false';
        }
        
            return $this->showOne($rta,200);
        
    }

    public function store(Request $request)
    {
        $rules = [
            'business_id'          => 'required|exists:business_markets,id',
            'question'       => 'required',
            'type'  => 'required' 
        ];

        $this->validate($request, $rules);

        $probe = FeedbackQuestion::create($request->all());
        return $this->successResponse(['data'=> $probe, 'message'=>'Question Created'], 201);
    }

    
    public function show($id)
    {
        
        $feedBackQuestions = FeedbackQuestion::where('business_id',$id)->get();

        $user = auth()->user();
        $answers = FeedbackAnswer::where('user_id',$user->id)->first();
        $rta = ['questions'=>$feedBackQuestions];
        if($answers){
            $rta['answered']='true';
        }   else {
            $rta['answered']='false';
        }
        return $this->showOne($rta,200);
    }


    public function update(Request $request, $id)
    {
        //return $feedBackQuestion;
        $feedBackQuestion = FeedbackQuestion::find($id);
        $feedBackQuestion->update($request->all());
        return $this->successResponse(['data'=> $feedBackQuestion, 'message'=>'Meeting Updated'], 200);
    }

    public function destroy($id)
    {
        $feedBackQuestion = FeedbackQuestion::find($id);
        $feedBackQuestion->delete();   
        return $this->successResponse(['data' => $feedBackQuestion, 'message' => 'Question Deleted'], 201);
    }


}
