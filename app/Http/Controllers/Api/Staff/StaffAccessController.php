<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\StaffAccess;

class StaffAccessController extends Controller
{
    public function index(){
        $role = auth()->user()->getRoleNames()->first();
        $user = Auth()->id();
        if($role == "admin" || $role == "super admin"){
            $staffAccess = StaffAccess::select('staff_access.id', 'staff_access.token', 'u.name as user_staff', 'e.id as event_id', 'e.name as event', 'staff_access.actived')
                ->leftJoin('users as u', 'u.id', '=', 'staff_access.user_id')
                ->join('events as e', 'e.id', '=', 'staff_access.event_id')
                ->where('staff_access.creator_id', $user)
                ->orderby('staff_access.id', 'desc')
                ->get();
        }else{
            if($role == "staff"){
                $staffAccess = StaffAccess::select('staff_access.id', 'staff_access.token', 'e.id as event_id', 'u.name as user_staff', 'e.name as event', 'staff_access.actived')
                ->join('users as u', 'u.id', '=', 'staff_access.user_id')
                ->join('events as e', 'e.id', '=', 'staff_access.event_id')
                ->where('staff_access.user_id', $user)
                ->where('staff_access.actived', true)
                ->orderby('staff_access.id', 'desc')
                ->get();
            }
        }

        return $this->showAll($staffAccess,200);
    }

    public function accessEvent($event){
        $role = auth()->user()->getRoleNames()->first();
        $user = Auth()->id();
        if($role == "admin" || $role == "super admin"){
            $staffAccess = StaffAccess::select('staff_access.id', 'staff_access.token', 'u.name as user_staff', 'e.id as event_id', 'e.name as event', 'staff_access.actived')
                ->leftJoin('users as u', 'u.id', '=', 'staff_access.user_id')
                ->join('events as e', 'e.id', '=', 'staff_access.event_id')
                ->where('staff_access.creator_id', $user)
                ->where('staff_access.event_id', $event)
                ->orderby('staff_access.id', 'desc')
                ->get();
        }else{
            if($role == "staff"){
                $staffAccess = StaffAccess::select('staff_access.id', 'staff_access.token', 'e.id as event_id', 'u.name as user_staff', 'e.name as event', 'staff_access.actived')
                ->join('users as u', 'u.id', '=', 'staff_access.user_id')
                ->join('events as e', 'e.id', '=', 'staff_access.event_id')
                ->where('staff_access.user_id', $user)
                ->where('staff_access.event_id', $event)
                ->where('staff_access.actived', true)
                ->orderby('staff_access.id', 'desc')
                ->get();
            }
        }

        return $this->showAll($staffAccess,200);
    }

    public function show (StaffAccess $staffAccess){
        
        return $this->showOne($staffAccess->with('staff')->first());
        
    }

    public function store(Request $request){
        
        $rules = [
            'event_id' => 'required|exists:events,id',
        ];

        $this->validate($request, $rules);

        $strToken= Str::random(6);
                
        $verifyToken = StaffAccess::where('token', $strToken)->first();

        while(isset($verifyToken->token)){
            $strToken= Str::random(6);
            $verifyToken = StaffAccess::select('token')->pluck('token')
                ->where('token', $token)->first();
        }

        $staffAccess = StaffAccess::create([
            'token'=> $strToken,
            'event_id'=> $request->event_id,
            'creator_id'=> Auth()->id(), 
        ]);

        return $this->successResponse(['data'=> $staffAccess, 'message'=>'Staff access Created'], 201);
        

    }
    public function update(Request $request, StaffAccess $staffAccess){

        $staffAccess->fill($request->all());
        
        if ($staffAccess->isClean()) {
            return $this->successResponse(['data' => $staffAccess, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $staffAccess->save();

        return $this->successResponse(['data' => $staffAccess, 'message' => 'Staff access update'],201);
    }
    public function staffAssign(Request $request){

        $rules = [
            'token'=> 'required', 
            'user_id'=> 'required|exists:users,id'
        ];

        $this->validate($request, $rules);

        $staffAccess = StaffAccess::where('token', $request->token)->first();
        if($staffAccess){
            if($staffAccess->user_id == null){
                $staffAccess->user_id = $request->user_id;
                $staffAccess->actived = true;
                $staffAccess->save();
            }else{
                return $this->errorResponse("The token is already used", 401);
            }
        }else{
            return $this->errorResponse("The token is invalid", 401);
        }

        return $this->successResponse(['data' => $staffAccess, 'message' => 'Staff access assign'],201);

    }

    public function validateToken($token) {
        $staffAccess =StaffAccess::select('u.email', 'staff_access.*')
            ->leftJoin('users as u', 'u.id', '=', 'staff_access.user_id')
            ->where('token', $token)
            ->first();
        if($staffAccess){
            if($staffAccess->user_id == null){
                return $this->successResponse(['data' => $staffAccess, 'message' => 'The token is valid'],201);
            }else{
                return $this->successResponse(['data' => $staffAccess, 'message' => 'The token is already used'], 201);
            }
        }else{
            return $this->errorResponse("The token is invalid", 401);
        }
    }

    public function destroy(StaffAccess $staffAccess)
    {
        $staffAccess->delete();   
        return $this->successResponse(['data' => $staffAccess, 'message' => 'Staff access Deleted'], 201);
    }

}
