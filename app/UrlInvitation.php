<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UrlInvitation extends Model
{
    protected $fillable = [
        'url', 'token', 'user_id', 'invitation_id', 'actived'
     ];
 
    public $timestamps = true;

    public function invitation()
    {
        return $this->hasOne('App\EventInvitation', 'id', 'invitation_id');
    }
}
