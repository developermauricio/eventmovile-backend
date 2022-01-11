<?php

namespace App\Http\Controllers\Api\Tracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LoginTracking;
use Illuminate\Support\Facades\DB;

class LoginTrackingController extends Controller
{
    public function index () {

        $loginTracking = DB::table('login_tracking as lt')
            ->select('lt.*', 'e.name as event', 'u.name', 'u.lastanme')
            ->join('users as u', 'u.id', '=', 'lt.user_id')
            ->join('events as e', 'e.id', '=', 'lt.event_id')
            ->get();

        $this->showAll($loginTracking);

    }
    public function store(Request $request){
        
        $rules = [
           "event_id" => "required",
           "user_id"  => "required",
        ];

        $this->validate($request, $rules);

        $exists = LoginTracking::where('event_id', $request->event_id)
            ->where('user_id', $request->user_id)->first();
        
        if($exists){
            $tracking = $exists;
            $tracking->actived = true;
            $tracking->save();
        }else{
            $tracking = loginTracking::create([
                'event_id' => $request->event_id,
                'user_id' => $request->user_id,
                'actived'  => true
            ]);
        }

        return $this->successResponse(['data'=> $tracking, 'message'=>'Login Tracking realized'], 201);
    }

    public function update(Request $request, LoginTracking $loginTracking){

        $rules = ['actived' => 'required'];

        $this->validate($request, $rules);

        $loginTracking->fill($request->all());
        
        if ($loginTracking->isClean()){
            return $this->successResponse(['data' => $loginTracking, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $loginTracking->save();

        return $this->successResponse(['data' => $loginTracking, 'message' => 'Login tracking updated'],201);

    }

    public function show($event){
        $loginTracking = DB::table('login_tracking as lt')
            ->select('lt.id', 'u.name as Nombre', 'u.lastname as Lastname', 'u.email as Email', 'e.name as Evento', 'lt.actived as Estado')
            ->join('users as u', 'u.id', '=', 'lt.user_id')
            ->join('events as e', 'e.id', '=', 'lt.event_id')
            ->where('e.id', $event)
            ->get();

        return $this->showAll($loginTracking, 201);

    }
}
