<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessMarketsRelUsers extends Model
{
    //
    protected $fillable = [
        'user_id', 'business_id', 'relation', 'answer_feedback'
    ];

    public $timestamps = true;

    public function users()
    {
        return $this->belongsTo('App\BusinessMarketUser', 'user_id', 'id');
    }

    public function business()
    {
        return $this->belongsTo('App\BusinessMarket', 'business_id', 'id');
    }
}
