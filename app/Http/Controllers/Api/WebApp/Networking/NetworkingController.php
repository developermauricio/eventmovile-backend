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
        $event_id = $request->input('event');
        $creator_id = auth()->user()->id;

        $networking = NetworkingWebApp::where('creator_id', $creator_id)
            ->where('event_id', $event_id)
            ->where('guest_id', $guest_id)->first();

        $hasSolicitud = NetworkingWebApp::where('creator_id', $guest_id)
            ->where('guest_id', $creator_id)
            ->where('event_id', $event_id)
            ->first();

        if ($hasSolicitud) {
            if ($hasSolicitud->status !== NetworkingWebApp::ACCEPTED) {
                $hasSolicitud->status = NetworkingWebApp::ACCEPTED;
                $hasSolicitud->save();
            }
            return response()->json($hasSolicitud);
        } else if (!$networking) {
            $networking = new NetworkingWebApp();
            $networking->chat_id = Str::random(4) . $guest_id . '_' . $creator_id . Str::random(4);
            $networking->event_id = $event_id;
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

    public function eliminarSolicitud($id)
    {
        $networking = NetworkingWebApp::findOrFail($id);
        $networking->delete();
        return response()->json([], 204);
    }

    public function getSolicitudesRecibidas(Request $request)
    {
        $event_id = $request->input('event');
        $networking = NetworkingWebApp::with('creator')
            ->where('guest_id', auth()->user()->id)
            ->where('event_id', $event_id)
            ->where('status', NetworkingWebApp::PENDING)
            ->get();
        return response()->json($networking);
    }

    public function getSolicitudesEnviadas(Request $request)
    {
        $event_id = $request->input('event');
        $networking = NetworkingWebApp::with('guest')
            ->where('creator_id', auth()->user()->id)
            ->where('status', NetworkingWebApp::PENDING)
            ->where('event_id', $event_id)
            ->get();

        return response()->json($networking);
    }

    public function getChatsUsuario(Request $request)
    {
        $user_id = auth()->user()->id;
        $event_id = $request->input('event');

        $networking = NetworkingWebApp::select('id', 'chat_id', 'guest_id', 'creator_id')
            ->where('event_id', $event_id)
            ->where(function ($q) use ($user_id) {
                return $q->where('creator_id', $user_id)->orWhere('guest_id', $user_id);
            })
            ->where('status', NetworkingWebApp::ACCEPTED)
            ->get();

        $networking = $networking->map(function ($net) use ($user_id) {
            $user = $user_id == $net->creator_id ? $net->guest_id : $net->creator_id;
            $user = User::select('name', 'lastname', 'id')
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

    public function deleteSolicitud($id)
    {
        $solicitud = NetworkingWebApp::findOrFAil($id);
        $solicitud->delete();

        return response()->json([], 204);
    }

    public function getParticipants($idEvent)
    {
        $user_id = auth()->user()->id;
        $users = User::select('id', 'name', 'lastname')->with([
            'requestSent' => function ($requestSend) use ($user_id) {
                return $requestSend->select('id', 'status', 'guest_id')->where('creator_id', $user_id);
            },
            'requestReceived' => function ($requestSend) use ($user_id) {
                return $requestSend->select('id', 'status', 'creator_id')->where('guest_id', $user_id);
            }
        ])->whereHas('eventUsers', function ($q) use ($idEvent) {
            return $q->where('event_id', $idEvent);
        })->where('id', '<>', $user_id)->paginate(50);

        return response()->json($users);
    }
}
