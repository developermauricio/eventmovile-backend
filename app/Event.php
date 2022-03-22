<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name', 'description', 'city_id', 'address', 'city_event_id','start_date', 'end_date', 'friendly_url', 'duration_minutes', 
        'company_id', 'message_email', 'subject_email', 'code_streaming', 'password', 'actived',
        'event_type_id', 'people_limit', 'image_on_register', 'domain_external', 'unique_login',
        'req_payment', 'payment_on_login', 'payment_name','req_chat','req_networking', 'req_make_question',
        'req_files','req_schedule','req_probes','req_survey','req_videocall', 'person_numbers', 'location', 
        'req_chat_event','req_web_app','wa_path_value','wa_req_path','wa_req_feria_comercial','wa_req_mapa','wa_mapa_value'
    ];

    public function activities()
    {
        return $this->hasMany('App\Activity', 'event_id');
    }

    public function stickers()
    {
        return $this->hasMany('App\Sticker', 'event_id');
    }

    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'event_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo('App\City', 'city_id', 'id');
    }
    public function city_event()
    {
        return $this->belongsTo('App\CityEvent', 'city_event_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo('App\EventType', 'event_type_id', 'id');
    }

    public function style()
    {
        return $this->hasOne('App\EventStyle', 'event_id', 'id');
    }

    public function payment()
    {
        return $this->hasOne('App\PaymentGateway', 'event_id', 'id');
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($data) { // before delete() method call this
             $data->payment()->each(function($payment) {
                $payment->delete(); // <-- direct deletion
             });
             
        });
    }

    public $timestamps = true;
}
