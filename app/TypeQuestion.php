<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeQuestion extends Model
{
    protected $fillable = [
        'name', 'actived'
     ];
 
     public $timestamps = true;
}
