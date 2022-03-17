<?php

namespace App\Http\Controllers\Api\WebApp\Schedule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hall;
use App\Activity;
use App\Event;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function getSchedule($event){
    
        $activities = Activity::where('event_id', $event)->with('speakers')->orderBy('start_date','ASC')->get();
        $eventDay = Event::where('id', $event)->get();


        $activitiesHall = collect();
        $eventDayCollect = collect();
    
        $i = 1;
        $dayStartDate = null;

           foreach($activities as $hallActivities){
           $dayFormat = Carbon::parse($hallActivities->start_date)->format('Y-m-d');
           $activitiesHall->push([
                "id" => $hallActivities->id,
                "event_id" => $hallActivities->event_id,
                "name" => $hallActivities->name,
                "sort_description" => $hallActivities->sort_description,
                "unit_price" => $hallActivities->unit_price,
                "duration_minutes" => $hallActivities->duration_minutes,
                "start_date" => $hallActivities->start_date,
                "end_date" => $hallActivities->end_date,
                "code_streaming" => $hallActivities->code_streaming,
                "tags" => $hallActivities->tags,
                "friendly_url" => $hallActivities->friendly_url,
                "location_coordinates" => $hallActivities->location_coordinates,
                "address" => $hallActivities->address,
                "country_id" => $hallActivities->country_id,
                "city_id" => $hallActivities->city_id,
                "type_activity_id" => $hallActivities->type_activity_id,
                "created_at" => $hallActivities->created_at,
                "updated_at" => $hallActivities->updated_at,
                "mode_id" => $hallActivities->mode_id,
                "pic" => $hallActivities->pic,
                "pic_banner" => $hallActivities->pic_banner,
                "voice_participation_check" => $hallActivities->voice_participation_check,
                "onDemand" => $hallActivities->onDemand,
                "payment" => $hallActivities->payment,
                "actived" => $hallActivities->actived,
                // "day" => $hallActivities->dayActivity($dayFormat, $dayStartDate),
                "day" => $dayFormat !== $dayStartDate ? $i : ($i-1),
                "hall" =>$hallActivities->activitiesHall($hallActivities->id, $event),
                "speakers" =>$hallActivities->speakers,
           ]);
           if($dayFormat !== $dayStartDate){
               $i = $i+1;
           }
           $dayStartDate = $dayFormat;
    

        }
        return response()->json($activitiesHall);
    }
    public function getCountDaysSchedule($event){

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

        return response()->json($days);
    }
}
