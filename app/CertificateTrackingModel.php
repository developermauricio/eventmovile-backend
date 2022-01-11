<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CertificateTrackingModel extends Model
{
    protected $table = 'certificate-tracking';

    protected $fillable = [
        'certificate_id', 'user_id', 'action'
    ];

    public $timestamps = true;
}
