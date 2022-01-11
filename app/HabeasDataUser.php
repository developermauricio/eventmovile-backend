<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HabeasDataUser extends Model
{
    protected $fillable = [
        'user_id', 'habeas_data_id', 
    ];

    public $timestamps = true;
}
