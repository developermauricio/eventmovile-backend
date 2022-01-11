<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $table = 'payment_gateways';

    protected $fillable = [
        'event_id', 'payment_name', 'status', 'key', 'token', 'key_dev', 'token_dev', 'mode',
        'merchantId', 'accountId', 'api_login', 'merchantId_dev', 'accountId_dev', 'api_login_dev'
    ];
}
