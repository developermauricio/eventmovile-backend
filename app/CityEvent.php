<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CityEvent extends Model
{
    public function country_event(){
        return $this->hasOne('App\CountryEvent','alpha2Code','country_code');
    }
}
