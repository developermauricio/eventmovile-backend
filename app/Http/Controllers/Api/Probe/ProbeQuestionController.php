<?php

namespace App\Http\Controllers\Api\Probe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProbeAnswer;
use App\Probe;
use App\ProbeQuestion;
use Illuminate\Support\Facades\Auth;
class ProbeQuestionController extends Controller
{
    public function index()
    {
        //
        $probes = ProbeQuestion::all();
        return $this->showAll($probes,200);
    }

    

    public function showQuestions($id)
    {
        if (!Auth::user()){
            $probeAnswer = ProbeQuestion::select('probes_questions.*', 'tq.name as type_question')
            ->where('probe_id', $id)
            
            ->join('type_questions as tq', 'tq.id', '=', 'probes_questions.type_question_id')
            ->orderBy('probes_questions.position', 'desc')
            ->get();

            return $this->showAll($probeAnswer);
        }
        
        $role = auth()->user()->getRoleNames()->first();
        
        $probeAnswer = ProbeQuestion::select('probes_questions.*', 'tq.name as type_question')
            ->where('probe_id', $id)
            
            ->join('type_questions as tq', 'tq.id', '=', 'probes_questions.type_question_id')
            ->orderBy('probes_questions.position', 'desc')
            ->get();
        if($role == "super admin" || $role == 'admin'){
            return $this->showAll($probeAnswer);
        }

        $idUser = Auth()->id();
        $answers = ProbeAnswer::where('probe_id', $id)->where('user_id',$idUser)->get();
        
        if(count($answers) != 0)
            return $this->showOne('answered');
        else    
            return $this->showAll($probeAnswer);
    }
    
    public function store(Request $request)
    {
        $rules = [
            'probe_id'          => 'required|exists:probes,id',
            'description'       => 'required',
            'type_question_id'  => 'required' 
        ];

        $this->validate($request, $rules);

        $position = ProbeQuestion::where('probe_id', $request->probe_id)->count();
        $position++;

        $probe = ProbeQuestion::create($request->all());
        return $this->successResponse(['data'=> $probe, 'message'=>'Probe question Created'], 201);
    }

    
    public function show(ProbeQuestion $probe)
    {
        return $this->showOne($probe);
    }

    public function updatePosition(Request $request) {
    
        $rules =[
            'position' => 'required',
            'probe_id' => 'required'
        ];

        $this->validate($request, $rules);

        if($request->postion != '1'){
            $nextPosition = Probe::where('position', $request->position+1)
                ->where('probe_id', $request->probe_id)
                ->first();
            
            $currentPosition = Probe::where('position', $request->position)
                ->where('probe_id', $request->probe_id)
                ->first();

            $nextPosition->position = $request->position;
            $nextPosition->save();

            $currentPosition->position = $request->position+1;
            $currentPosition->save();


            return $this->successResponse(['data' => $currentPosition, 'message' => 'Probe Question postion updated'],201);
        }
        else{
            return $this->errorResponse('This is the first question', 500);
        }

    }
    public function update(Request $request, ProbeQuestion $probeQuestion)
    {
        //return $probeQuestion;
        $probeQuestion->update($request->all());
        return $this->successResponse(['data'=> $probeQuestion, 'message'=>'Meeting Updated'], 200);
    }

    public function destroy(ProbeQuestion $probeQuestion)
    {
        $probeQuestion->delete();   
        return $this->successResponse(['data' => $probeQuestion, 'message' => 'Question Deleted'], 201);
    }

}
