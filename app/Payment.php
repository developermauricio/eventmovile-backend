<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'quantity', 'id_payment_gateway', 'status', 'ticket_id', 'event_id', 'user_id'
    ];
}
