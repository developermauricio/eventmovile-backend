<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModeActivity extends Model
{
    protected $table = 'mode_activities';

    protected $fillable = [
        'name', 'actived',
    ];


    public $timestamps = true;
}
