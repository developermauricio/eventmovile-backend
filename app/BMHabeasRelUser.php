<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BMHabeasRelUser extends Model
{
    protected $table = 'bm_habeas_rel_user';
    protected $fillable = [
        'user_id', 'habeas_data_id', 
    ];
}
