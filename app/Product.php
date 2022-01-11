<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name', 'description',  'model_id', 'type', 'model', 'pic', 'position'
    ];

    public function documents()
    {
        return $this->hasMany('App\Document', 'model_id', 'id');
    }

    public $timestamps = true;
}
