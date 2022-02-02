<?php

namespace App\Http\Controllers\Api\WebApp\Networking;

use App\Http\Controllers\Controller;
use App\NetworkingWebApp;
use App\User;
use App\WebAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NetworkingController extends Controller
{

    public function sendSolicitud(Request $request)
    {
        $guest_id = $request->input('guest');
        $creator_id = auth()->user()->id;

        $networking = NetworkingWebApp::where('creator_id', $creator_id)
            ->where('guest_id', $guest_id)->first();

        $hasSolicitud = NetworkingWebApp::where('creator_id', $guest_id)
            ->where('guest_id', $creator_id)
            ->first();

        if ($hasSolicitud) {
            if ($hasSolicitud->status === NetworkingWebApp::PENDING) {
                $hasSolicitud->status = NetworkingWebApp::ACCEPTED;
                $hasSolicitud->save();
            }
            return response()->json($hasSolicitud);
        } else if (!$networking) {
            $networking = new NetworkingWebApp();
            $networking->chat_id = Str::random(4) . $guest_id . '_' . $creator_id . Str::random(4);
            $networking->creator_id = $creator_id;
            $networking->guest_id = $guest_id;
            $networking->status = NetworkingWebApp::PENDING;
            $networking->save();
        }
        return response()->json($networking, 201);
    }

    public function aceptarSolicitud($id)
    {
        $networking = NetworkingWebApp::findOrFail($id);
        $networking->status = NetworkingWebApp::ACCEPTED;
        $networking->save();
        return response()->json($networking);
    }

    public function rechazarSolicitud($id)
    {
        $networking = NetworkingWebApp::findOrFail($id);
        $networking->status = NetworkingWebApp::REJECTED;
        $networking->save();
        return response()->json($networking);
    }

    public function getSolicitudesRecibidas()
    {
        $networking = NetworkingWebApp::with('creator')
            ->where('guest_id', auth()->user()->id)
            ->where('status', NetworkingWebApp::PENDING)
            ->get();
        return response()->json($networking);
    }

    public function getSolicitudesEnviadas()
    {
        $networking = NetworkingWebApp::with('guest')
            ->where('creator_id', auth()->user()->id)
            ->where('status', NetworkingWebApp::PENDING)
            ->get();

        return response()->json($networking);
    }

    public function getChatsUsuario()
    {
        $user_id = auth()->user()->id;
        $networking = NetworkingWebApp::select('id', 'chat_id', 'guest_id', 'creator_id')
            ->where(function ($q) use ($user_id) {
                return $q->where('creator_id', $user_id)->orWhere('guest_id', $user_id);
            })
            ->where('status', NetworkingWebApp::ACCEPTED)
            ->get();

        $networking = $networking->map(function ($net) use ($user_id) {
            $user = $user_id == $net->creator_id ? $net->guest_id : $net->creator_id;
            $user = User::select('name', 'lastname', 'id', 'email')
                ->where('id', $user)
                ->first();

            $net->user = $user;
            return $net;
        });

        return response()->json($networking);
    }

    public function chatInfo($key)
    {
        $chat = NetworkingWebApp::where('chat_id', $key)
            ->first();

        $users = User::select('id', 'email', 'name', 'lastname')
            ->where('id', $chat->creator_id)
            ->orWhere('id', $chat->guest_id)
            ->get();

        $chat->users = $users;

        return response()->json($chat);
    }


    public function storeMessage(Request $request)
    {

        $message = new WebAppMessage();
        $message->text = $request->input('t');
        $message->user_id = $request->input('u');
        $message->chat_id = $request->input('c');
        $message->created_at = \Carbon\Carbon::now();
        $message->save();

        return response()->json([], 201);
    }

    public function getMessages($id)
    {
        $messages = WebAppMessage::select([
            'text',
            'chat_id',
            'user_id',
            'created_at'
        ])->where('chat_id', $id)
            ->orderBy('id', 'DESC')
            ->paginate(50);
        return response()->json($messages);
    }
}
