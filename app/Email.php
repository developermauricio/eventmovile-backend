<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{   
    protected $table = 'emails';

    protected $fillable = [
        'subject', 'type', 'email', 'model', 'model_id'
    ];

    public $timestamps = true;

    public function tracking()
    {
        return $this->hasMany('App\EmailTracking', 'id', 'email_id');
    }
    
}
