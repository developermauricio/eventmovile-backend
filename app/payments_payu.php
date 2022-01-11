<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class payments_payu extends Model
{
    protected $fillable = [
        'quantity',
        'status',
        'referenceCode',
        'signature',
        'processingDate',
        'ticket_id',
        'event_id',
        'user_id',
        'number_verify_transaction'
    ];
}
