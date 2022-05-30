<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Activity extends Model
{
    protected $fillable = [
        'event_id', 'name', 'sort_description', 'unit_price', 'duration_minutes', 'start_date', 
        'end_date', 'pic', 'code_streaming', 'tags', 'friendly_url', 'location_coordinates', 'address', 'country_id', 'city_id',
        'guests_limit', 'type_activity_id', 'mode_id', 'pic', 'pic_banner','voice_participation_check','onDemand', 'payment'
    ];

    public function favoriteActivities(){
        return $this->hasOne(FavoriteActivities::class, 'activies_id');
    }

    public function qualifyActivities(){
        return $this->hasOne(RateActivity::class, 'activity_id');
    }

    public function type_activity() {
        return $this->belongsTo('App\TypeActivity', 'type_activity_id', 'id');
    }

    public function mode() {
        return $this->belongsTo('App\ModeActivity', 'mode_id', 'id');
    }

    public function speakers()
    {
        return $this->belongsToMany('App\Speaker', 'activity_speakers', 'activity_id', 'speaker_id');
    }
    
    public function event() {
        return $this->belongsTo('App\Event', 'event_id');
    }

    public function isQualify($user)
    {
        return $this->qualifyActivities()->where('user_id', $user)->exists();
    }

    public function isFavorite($user)
    {
        return $this->favoriteActivities()->where('user_id', $user)->exists();
    }
    public function isFavoriteId($user)
    {
        return $this->favoriteActivities()->where('user_id', $user)->first();
    }

    static function activitiesHall($hallActivities, $event){
        // $halls = Hall::where('event_id', $event)->get();
        // foreach($halls as $hall){
        
        //    if(in_array($hallActivities, json_decode($hall->activities))){
        //     $activitiesHall = $hall;
        //    }
        
        // }

        // return $activitiesHall;

        $halls = Hall::where('event_id', $event)->get();
        $activitiesHall = collect();
        foreach($halls as $hall){
        
           if(in_array($hallActivities, json_decode($hall->activities))){
            $activitiesHall->push($hall);
           }
        
        }

        return $activitiesHall;

    }

    static function countDays($event){
        $activitiesStartDate = Activity::select('start_date')->where('event_id', $event)->orderBy('start_date','ASC')->get();
        
        $days = collect();
        $i = 1;
        $dayStartDate = null;
        foreach($activitiesStartDate as $activityDay){
           
            // dd(Carbon::parse($activityDay->start_date)->format('M d Y'));
            $dayFormat = Carbon::parse($activityDay->start_date)->format('Y-m-d');
            if($dayFormat !== $dayStartDate){
                
                $days->push([
                    "day" => $i
                ]);
                $i = $i+1;
            }
            $dayStartDate = $dayFormat;
        }

        return $days;
    }

    static function dayActivity($dayFormat, $dayStartDate){
        $i = 1;
        // dd($dayStartDate);
        if($dayFormat === $dayStartDate){
            $i = $i+1;
        }
      
        return $i;
    }

    public $timestamps = true;
}
