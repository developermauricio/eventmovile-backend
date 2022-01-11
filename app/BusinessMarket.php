<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessMarket extends Model
{
    protected $fillable = [
        'name', 'sort_description',  'speaker_name', 'start_date', 'background_banner', 'logo',
        'end_date', 'pic', 'tags', 'friendly_url', 'location_coordinates', 'address', 'country_id', 'city_id',
        'guests_limit', 'company_id', 'type', 'mode', 'principal_color', 'secondary_color', 'time_meeting', 'third_color',
        'message_email', 'subject_email', 'password','segmentation_actived'
    ];


    public $timestamps = true;

    public function participants(){
        return $this->belongsToMany('App\User','business_markets_rel_users','business_id', 'user_id')->withPivot('user_id','id','business_id');
    }

    

}
