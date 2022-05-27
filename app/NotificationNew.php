<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationNew extends Model
{
    const NOT_SEND = 1;
    const SEND = 2;

    public function notificationUserRead(){
        return $this->hasOne(NotificationNewRead::class, 'notification_new_id');
    }

    public function isRead($user)
    {
        return $this->notificationUserRead()->where('user_id', $user)->exists();
    }
}
