<?php

namespace App\Http\Controllers\Api\Networking;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Events\NetworkingEvent;

use App\Networking;
use App\User;
use Illuminate\Support\Facades\Log;

class NetworkingController extends Controller
{
    //

    public function show(Networking $networking){

        return $this->showOne($networking);

    }
    public function store(Request $request){
        
        $rules = [
            'guest_id' => 'required',
            'activity_id' => 'required',
        ];

        $this->validate($request, $rules);

        $networking = Networking::create([
            'creator_id' => Auth()->id(),
            'guest_id' => $request->guest_id,
            'activity_id' => $request->activity_id,
            'confirmed' => 0
        ]);
        $user = User::findOrFail($networking->creator_id);
        $networking->creator_name = $user->name;
        $user = User::findOrFail($networking->guest_id);
        $networking->guest_name = $user->name;

        // broadcast(new NetworkingEvent($networking->creator_id, $networking));
        // broadcast(new NetworkingEvent($networking->guest_id, $networking));

        return $this->successResponse(['data'=> $networking, 'message'=>'Invitation sent'], 201);

    }

    public function update(Request $request, Networking $networking){

        $rules = [
            'guest_id' => 'required',
            'activity_id' => 'required',
            'confirmed' => 'required',
        ];

        $this->validate($request, $rules);

        $networking->guest_id = $request->guest_id;
        $networking->confirmed = $request->confirmed;
        $networking->activity_id = $request->activity_id;
        $networking->save();

        $user = User::findOrFail($networking->creator_id);
        $networking->creator_name = $user->name;
        $user = User::findOrFail($networking->guest_id);
        $networking->guest_name = $user->name;

        // broadcast(new NetworkingEvent($networking->creator_id, $networking));
        // broadcast(new NetworkingEvent($networking->guest_id, $networking));

        return $this->successResponse(['data' => $networking, 'message' => 'Networking Updated'],201);

    }
}
