<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'name', 'event_id',  'activities', 'description', 'unit_price',
    ];
}
