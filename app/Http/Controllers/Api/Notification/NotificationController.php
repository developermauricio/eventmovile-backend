<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\NotificationNew;
use App\NotificationNewRead;

class NotificationController extends Controller
{
    public function getNotifications($id){
        $notifications = NotificationNew::where('event_id', $id)->get();
        return response()->json(['data' => $notifications]);
    }

    public function readNotification(Request $request)
    {
        $user_id = $request->user_id;
        $notification_id = $request->notification_id;

        $notificationRead = NotificationNewRead::create([
            'user_id' => $user_id,
            'notification_new_id' => $notification_id,
        ]);
        return response()->json('Notificación leída');

    }

    public function getNotificationsUser($event_id, $user_id){
        $notifications = NotificationNew::where('event_id', $event_id)->where('send', 2)->with(['notificationUserRead' => function ($q) use ($user_id){
            $q->where('user_id', $user_id);
        }])->orderByDesc('created_at')->get();
        $finalData = collect();
        foreach ($notifications as $notification) {
            $finalData->push([
                "id" => $notification->id,
                "event_id" => $notification->event_id,
                "typeNotification" => $notification->type_notification,
                "title" => $notification->title,
                "link" => $notification->link_event_action,
                "description" => $notification->description,
                "created_at" => $notification->created_at,
                "end_time" => $notification->end_time,
                "is_read" => $notification->isRead($user_id),

            ]);
        }
        return response()->json($finalData);
    }

    public function saveNotifications(Request $request){
        $notications = new NotificationNew;
        $notications->event_id = $request->eventId;
        $notications->title = $request->notificationTitle;
        $notications->description = $request->notificationDescription;
        $notications->link_event_action = $request->notificationLink;
        $notications->type_notification = $request->notificationType === "1" ? 'direct' : 'programmed';
        $notications->send = $request->notificationType === "1" ? \App\NotificationNew::SEND : \App\NotificationNew::NOT_SEND;
        if($request->notificationType === "2"){
            $notications->end_time = $request->scheduleDate;
        }
        $notications->save();
        return response()->json('Notificación creada');
    }
}
