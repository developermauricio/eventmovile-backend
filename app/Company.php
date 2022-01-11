<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
  
    protected $fillable = [
        'name', 'sort_description', 'email', 'phone', 'pic', 'address',
        'city_id', 'location_coordinates' , 'country_id'
        
    ];

    public function producs()
    {
        return $this->hasMany('App\Product', 'model_id', 'id');
    }

    public function business_user()
    {
        return $this->hasOne('App\BusinessMarketUser', 'company_id', 'id');
    }

    public $timestamps = true;
    
}
