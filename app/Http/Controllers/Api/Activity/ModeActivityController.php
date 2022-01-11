<?php

namespace App\Http\Controllers\Api\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ModeActivity;

class ModeActivityController extends Controller
{
    public function index() {

        $modeActivity = ModeActivity::all();

        return $this->showAll($modeActivity,200);
    }

    public function show(ModeActivity $modeActivity)
    {
    
        return $this->showOne($modeActivity);
    }

    public function store(Request $request)
    {
        $rules = [
            'speaker_id'   => 'required|exists:speakers,id',
            'activity_id'  => 'required|exists:activities,id',
        ];

        $this->validate($request, $rules);
        
        $modeActivity = ModeActivity::create([
            'speaker_id'   => $request->speaker_id,
            'activity_id'  => $request->activity_id,
        ]);

        return $this->successResponse(['data'=> $modeActivity, 'message'=>'Speaker Created'], 201);
    }

    public function update(Request $request, ModeActivity $modeActivity){
        
        $rules = [
            'speaker_id'   => 'required|exists:speakers,id',
            'activity_id'  => 'required|exists:activities,id', 
        ];

        $this->validate($request, $rules);
                
        $modeActivity->fill($request->all());
        
        if ($modeActivity->isClean()) {
            return $this->successResponse(['data' => $modeActivity, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $modeActivity->save();

        return $this->successResponse(['data' => $modeActivity, 'message' => 'Speaker Updated'],201);
    }

    public function destroy(ModeActivity $modeActivity)
    {
        $modeActivity->delete();   
        return $this->successResponse(['data' => $modeActivity, 'message' => 'Speaker Deleted'], 201);
    }
}
