<?php

namespace App\Http\Controllers\Api\WebApp;

use App\Http\Controllers\Controller;
use App\Notifications\DefaultNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    //

    public function getNotifications()
    {
        $notifications = auth()->user()->unreadNotifications;
        return response()->json($notifications);
    }


    public function readNotification($id)
    {
        auth()->user()->notifications()->findOrFail($id)->markAsRead();
        return response()->json([], 204);
    }

    public function addNotification($idUser, Request $request)
    {
        $data = $request->all();
        $user = User::findOrFail($idUser);
        Notification::send($user, new DefaultNotification($data));

        return response()->json([], 204);
    }
}
