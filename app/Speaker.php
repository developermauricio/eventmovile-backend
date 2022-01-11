<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    protected $fillable = [
        'name', 'sort_description', 'pic'
    ];


    public $timestamps = true;
}
