<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fair extends Model
{
    //table in db
    protected $table = "feria_comercial";
    public $timestamps = false;
    protected $fillable = [
        'id','name_company','description_company','logo_company','contact_company','id_event'
    ];        
}
