<?php

namespace App\Http\Controllers\Api\Probe;

use App\ProbeAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProbeAnswerController extends Controller
{
    
    public function index()
    {
        $answers = ProbeAnswer::all();
        return $this->showAll($answers,200);
    }

   
    public function store(Request $request)
    {
        $rules = [
            'question_id'              => 'required',
            'probe_id'  => 'required',
            'value'               => 'required' ,
            'user_id'               => 'required' 
        ];
        $this->validate($request, $rules);
        
        $toSave = $request->all();
        $data = ProbeAnswer::create($toSave);

        return $this->successResponse(['data'=> $data, 'message'=>'Answer Created'], 201);
    }


    public function show(ProbeAnswer $probeAnswer)
    {
        return $this->showOne($probeAnswer);
    }


    public function update(Request $request, ProbeAnswer $probeAnswer)
    {
        $data = $probeAnswer->update($request->all());
        return $this->successResponse(['data' => $data, 'message' => 'Answer Updated'],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProbeAnswer  $probeAnswer
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProbeAnswer $probeAnswer)
    {
        //
        $probeAnswer->delete();
        return $this->successResponse(['data' => $probeAnswer, 'message' => 'Answer Deleted'], 200);
    }
}
