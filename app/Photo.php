<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    //table in db
    protected $table = "photo";
    public $timestamps = false;
    protected $fillable = [
        'id_user','id_album','description','path_photo','privacidad','upload_at','status'
    ];  
}
