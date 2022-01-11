<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HabeasData extends Model
{
    protected $table = 'habeas_data';

    protected $fillable = [
        'type', 'content', 'position', 'event_id',
    ];
    
    public $timestamps = true;
}
