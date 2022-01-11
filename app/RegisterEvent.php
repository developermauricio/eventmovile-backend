<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegisterEvent extends Model
{
    protected $fillable = [
        'name', 'type',  'options', 'event_id', 'required',
    ];

}
