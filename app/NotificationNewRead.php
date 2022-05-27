<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationNewRead extends Model
{
    protected $fillable = ['user_id', 'notification_new_id'];
}
