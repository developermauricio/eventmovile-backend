<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    //table in db
    public $timestamps = false;
    protected $table = "interaction";    
    protected $fillable = [
        'id_user','id_photo','description','register_at'
    ];        
    
}
