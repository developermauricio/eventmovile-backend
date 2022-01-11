<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTrackingBM extends Model
{
    protected $table = 'email-tracking';

    protected $fillable = [
        'email_id', 'action'
    ];

    public $timestamps = true;

    public function email()
    {
        return $this->belongsTo('App\Email', 'id', 'email_id');
    }
}
