<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffAccess extends Model
{
    protected $table = 'staff_access';

    protected $fillable = [
        'token', 'event_id', 'user_id', 'creator_id', 'actived',
    ];

    public function staff()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }


    public $timestamps = true;
}
