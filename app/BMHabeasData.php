<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BMHabeasData extends Model
{
    protected $table = 'bm_habeas_data';

    protected $fillable = [
        'type', 'content', 'position', 'business_id',
    ];
}
