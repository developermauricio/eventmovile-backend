<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CertificateModel extends Model
{
    protected $fillable = [
        'name_certificate',
        'background_image',
        'text_fields',
        'event_id',
    ];

    public function event(){
        return $this->belongsTo(Event::class, 'event_id');
    }
}
