<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    //table in db
    protected $table = "album";
    public $timestamps = false;
    protected $fillable = [
        'id','id_event','description'
    ];        
}
