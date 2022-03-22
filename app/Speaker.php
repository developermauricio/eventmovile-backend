<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    protected $fillable = [
        'name', 'sort_description', 'pic', 'country_id'
    ];

    public function country_event(){
        return $this->belongsTo('App\CountryEvent','id','country_id');
    }

    public $timestamps = true;
}
