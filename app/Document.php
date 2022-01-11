<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'model_type', 'model_id', 'url', 'name', 'type'
    ];

    public $timestamps = true;
}
