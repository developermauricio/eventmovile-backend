<?php

namespace App\Http\Controllers\Api\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Events\ChatEvent;
use App\ActivityChat;
use Illuminate\Support\Facades\Log;

class ActitivyChatController extends Controller
{
    public function store(Request $request){
        $messages=null;
        $rules = [
            'activity_id' => 'required|exists:activities,id',
            'message'  => 'required',
        ];

        $this->validate($request, $rules);  

        $message = ActivityChat::create([
            'activity_id'=> $request->activity_id,
            'user_id'=> Auth()->id(),
            'message'=> $request->message, 
        ]);
        Log::info("Hola chat:");
        Log::info($message);
        if(isset($message->id)){
            $messages = DB::table('activity_chats as ac')
            ->select('ac.*', 'u.name', 'u.lastname')
            ->where('activity_id', $message->activity_id)
            ->where('ac.id', $message->id)
            ->join('users as u', 'u.id', '=', 'ac.user_id')
            ->orderBy('created_at')
            ->get();
            Log::info($messages);
        }
        // broadcast(new ChatEvent($message->activity_id, $message->message));
        return $this->successResponse(['data'=> $messages, 'message'=>'Message sent'], 201);

    }
    // query desc para traer los ultimos 100 y despues los ordenamos en front
    public function activityMessages($activity){

        $messages = DB::table('activity_chats as ac')
            ->select('ac.*', 'u.name', 'u.lastname')
            ->where('activity_id', $activity)
            ->join('users as u', 'u.id', '=', 'ac.user_id')
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        return $this->showAll($messages, 200);

    }
}
