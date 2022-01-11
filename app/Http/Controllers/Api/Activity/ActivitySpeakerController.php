<?php

namespace App\Http\Controllers\Api\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\ActivitySpeaker;

class ActivitySpeakerController extends Controller
{
    public function index() {

        $activitySpeaker = ActivitySpeaker::all();

        return $this->showAll($activitySpeaker,200);
    }

    public function show(ActivitySpeaker $activitySpeaker)
    {
    
        return $this->showOne($activitySpeaker);
    }
    public function showActivity($activity)
    {
        $speakers = ActivitySpeaker::select('activity_speakers.speaker_id as id', 
            'activity_speakers.id as table_id', 's.name as name')
            ->where('activity_id', $activity)
            ->join('speakers as s', 's.id', '=', 'activity_speakers.speaker_id')
            ->get();
        return $this->showAll($speakers, 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'speaker_id'   => 'required|exists:speakers,id',
            'activity_id'  => 'required|exists:activities,id',
        ];

        $this->validate($request, $rules); 
        
        $activitySpeaker = ActivitySpeaker::create([
            'speaker_id'   => $request->speaker_id,
            'activity_id'  => $request->activity_id,
        ]);

        return $this->successResponse(['data'=> $activitySpeaker, 'message'=>'Speaker Created'], 201);
    }

    public function update(Request $request, ActivitySpeaker $activitySpeaker){
        
        $rules = [
            'speaker_id'   => 'required|exists:speakers,id',
            'activity_id'  => 'required|exists:activities,id', 
        ];

        $this->validate($request, $rules);
                
        $activitySpeaker->fill($request->all());
        
        if ($activitySpeaker->isClean()) {
            return $this->successResponse(['data' => $activitySpeaker, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $activitySpeaker->save();

        return $this->successResponse(['data' => $activitySpeaker, 'message' => 'Speaker Updated'],201);
    }

    public function destroy($activity)
    {        
        $speakers = ActivitySpeaker::where('activity_id', $activity)->get();
        
        foreach ($speakers as $speaker) {
            $activitySpeaker = ActivitySpeaker::where('activity_id', $activity)
                ->where('speaker_id', $speaker->speaker_id)->first();
            $activitySpeaker->delete(); 
        }
          
        return $this->successResponse(['data' => $speakers, 'message' => 'Speakers Deleted'], 201);
    }
}
