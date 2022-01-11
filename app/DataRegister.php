<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataRegister extends Model
{
    protected $fillable = [
        'user_id', 'register_id', 'value',
    ];
}
