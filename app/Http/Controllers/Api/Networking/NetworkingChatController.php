<?php

namespace App\Http\Controllers\Api\Networking;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Events\NetworkingChatEvent;
use App\NetworkingChat;
use App\Networking;
use App\Http\Controllers\Api\Meeting\ZoomMeetingController;
use Illuminate\Support\Facades\Log;

class NetworkingChatController extends Controller
{
    public function store(Request $request){

        $rules = [
            'user_id' => 'required|exists:users,id',
            'networking_id' => 'required|exists:networkings,id',
            'message'  => 'required',
        ];  

        $this->validate($request, $rules);  

        if($request->message == "Iniciar Video llamada"){
            $networking = Networking::find($request->networking_id);
            $zoom = new ZoomMeetingController;
            $res = $zoom->createNetworking($networking);
            $res = json_decode($res->body(), true);
            $networking->zoom_pw = "000";//$res['password'];
            $networking->zoom_id = $res['id'];
            $networking->save();
        }
        $networking = Networking::find($request->networking_id);
        $message = NetworkingChat::create([
            'networking_id'=> $request->networking_id,
            'creator_id'=> Auth()->id(),
            'user_id'=> $request->user_id,
            'message'=> $request->message, 
        ]);
        $message->guest_id = $networking->guest_id;
        // broadcast(new NetworkingChatEvent($message->user_id, $message));

        return $this->successResponse(['data'=> $message, 'message'=>'Message sent'], 201);

    }
    // query desc para traer los ultimos 50 y despues los ordenamos en front
    public function networkingMessages($networking){

        $messages = DB::table('networking_chats as nc')
            ->select('nc.*', 'u.name', 'u.lastname')
            ->where('networking_id', $networking)
            ->join('users as u', 'u.id', '=', 'nc.creator_id')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();



        return $this->showAll($messages, 200);


    }
}
