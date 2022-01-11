<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Probe extends Model
{
    //
    protected $fillable = [

        'activity_id', 'name',  'required_probe', 'status'



    ];

    public function questions(){
        return $this->hasMany('App\ProbeQuestion','probe_id','id');
    }

    public function answers(){
        return $this->hasMany('App\ProbeAnswer','probe_id','id');
    }

    public static function boot() {
        parent::boot();
        self::deleting(function($probe) { // before delete() method call this
             $probe->questions()->each(function($question) {
                $question->delete(); // <-- direct deletion
             });
             
             
        });
    }
}
