<?php

namespace App\Http\Controllers\Api\Meeting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\MeetChat;
use App\Events\ChatMeet;

class MeetingChatController extends Controller
{
    public function index(){

    }

    public function show(){

    }
    public function store(Request $request){
        $rules = [
            'meet_id' => 'required|exists:meetings,id',
            'message'  => 'required',
        ];

        $this->validate($request, $rules);  

        $message = MeetChat::create([
            'meet_id'=> $request->meet_id,
            'user_id'=> Auth()->id(),
            'message'=> $request->message, 
        ]);
        
        broadcast(new ChatMeet($message->meet_id, $message->message));

        return $this->successResponse(['data'=> $message, 'message'=>'Message sent'], 201);
    }
    public function meetMessages($meet){

        $messages = DB::table('meet_chats as mc')
            ->select('mc.*', 'u.name')
            ->where('meet_id', $meet)
            ->join('users as u', 'u.id', '=', 'mc.user_id')
            ->orderBy('mc.created_at')
            ->get();

        return $this->showAll($messages, 200);

    }
}
