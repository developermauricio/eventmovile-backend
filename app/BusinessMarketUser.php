<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessMarketUser extends Model
{
    //
    protected $table = 'business_market_user';

    protected $fillable = [
        'name', 'email', 'phone', 'pic', 'nit', 'products', 'company_id', 'position'
    ];

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }
}
