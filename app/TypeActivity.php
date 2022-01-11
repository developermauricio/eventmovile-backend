<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeActivity extends Model
{
    //
    protected $fillable = [
       'name', 'actived'
    ];

    public $timestamps = true;
}
