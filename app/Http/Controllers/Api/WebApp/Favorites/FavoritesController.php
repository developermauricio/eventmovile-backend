<?php

namespace App\Http\Controllers\Api\WebApp\Favorites;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Activity;
use App\FavoriteActivities;

class FavoritesController extends Controller
{
    public function getFavoritesActivities($event_id, $user_id){

        $activities = Activity::where('event_id', $event_id)->where('actived', 1)->with('speakers')->whereHas('favoriteActivities',  function ($q) use ($user_id){
             $q->where('user_id', $user_id);
         })->orderByDesc('created_at')->get();
        
         $activitiesHall = collect();
         foreach($activities as $hallActivities){
            
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
                 "hall" =>$hallActivities->activitiesHall($hallActivities->id, $event_id),
                 "speakers" =>$hallActivities->speakers,
            ]);
         }
        return response()->json($activitiesHall);

    }

    public function saveIsFavorite(Request $request){
        $user_id = $request->user_id;
        $activity_id = $request->activity_id;

        $ActivityIsFavorite = FavoriteActivities::create([
            'user_id' => $user_id,
            'activies_id' => $activity_id
        ]);
        return response()->json(['Like guardado correctamente', 'idFavorite' => $ActivityIsFavorite->id]);
    }

    public function removeIsFavorite($id){
        $ActivityIsFavorite = FavoriteActivities::where('id', $id)->delete();
        return response()->json('Ya no es favorito para este usuario', 201);
    }
}
