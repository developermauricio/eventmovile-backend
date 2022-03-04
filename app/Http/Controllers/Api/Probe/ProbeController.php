<?php

namespace App\Http\Controllers\Api\Probe;

use App\Probe;
use App\ProbeAnswer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\ProbeEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class ProbeController extends Controller
{
    public function index()
    {
        //
        $probes = Probe::all();
        return $this->showAll($probes,200);
    }

    public function showProbes($id)
    {
        Log::info('showProbes: ');
        Log::info($id);

        if (!Auth::user()){
            Log::info('Auth::user() ingreso');
            $probe = Probe::where('activity_id',$id)
            ->with('questions','answers')
            ->get();
            return $this->showAll($probe);
        }

        $role = auth()->user()->getRoleNames()->first();
        Log::info('role: ');
        Log::info($role);
        
        if($role == "super admin" || $role == 'admin'){
            $probe = Probe::where('activity_id',$id)
            ->with('questions','answers')
            ->get();
            return $this->showAll($probe);
        }

        $probe = Probe::where('activity_id',$id)
            ->where('status','launched')
            ->with('questions','answers')
            ->get();
        Log::info('llego al final..');
        return $this->showAll($probe);
    }
    
    public function showProbesPublic($id)
    {
            $probe = Probe::where('activity_id',$id)
            ->with('questions','answers')
            ->get();
            return $this->showAll($probe);

        return $this->showAll($probe);
    }

    public function store(Request $request)
    {
        $rules = [
            'activity_id'          => 'required|exists:activities,id',
            'name'       => 'required', 
        ];

        $this->validate($request, $rules);

        $probe = Probe::create($request->all());
        return $this->successResponse(['data'=> $probe, 'message'=>'Probe Created'], 201);
    }

    
    public function show(Probe $probe)
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
    public function update(Request $request, Probe $probe)
    {

        $probe->update($request->all());
        if(isset($request->status)){
            $event =  new ProbeEvent($probe->activity_id, $request->status);
            // broadcast($event);
        }
        return $this->successResponse(['data'=> $probe, 'message'=>'Meeting Updated'], 200);
    }

    public function destroy(Probe $probe)
    {
        $probe->delete();   
        return $this->successResponse(['data' => $probe, 'message' => 'Question Deleted'], 201);
    }
    }
