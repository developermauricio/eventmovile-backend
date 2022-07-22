<?php

namespace App\Http\Controllers\Api\WebApp\RateActivity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RateActivity;
use App\Activity;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RateActivityController extends Controller
{
    public function saveRateActivity(Request $request){

        $exitsRate = RateActivity::where('activity_id', $request->activity_id)->where('user_id', $request->user_id)->first();

        if ( $exitsRate ) {
            return response()->json('La actividad ya se encuentra calificada.');
        }

        $rate = RateActivity::create([
            'activity_id' => $request->activity_id,
            'user_id' => $request->user_id,
            'rate' => $request->rate,
            'event_id' => $request->event_id
        ]);

        return response()->json('Se guardo la calificación correctamente');
    }

    public function getRateActivity($activity, $user){
        $rating = RateActivity::where('activity_id', $activity)->where('user_id', $user)->first();
        return $rating;

    }

    /**
     * retorna las actividades por realizar a la fecha
     */
    public function getActivitiesForEvent( $eventId ){        
        $activities = Activity::select('id', 'name')
            ->where('event_id',$eventId)
            ->get();
        return response()->json($activities);
    }

    /**
     * Retorna los datos de la calificacion de las actividades de un evento
     */
    public function getReportRateForEvent( $eventId ) {

        $rowsActivity = RateActivity::with([
                "event:id,name",
                "activity:id,name",
                "user:id,name,lastname,email"
            ])
            ->where('event_id', $eventId)
            ->orderBy('id')
            ->get(); 
        
        if( $rowsActivity->count() === 0 ){
            return  response()->json(['dataReport' => [], 'status' => 404]);
        }

        $finalData = collect();

        foreach ($rowsActivity as $row){  
            $fullName = $row->user->name . ' ' . $row->user->lastname;
            $dateRegister = new Carbon($row->created_at, 'America/Bogota');

            $finalData->push([
                "id" => $row->id,
                "Nombre evento" => $row->event->name,
                "id actividad" => $row->activity->id,
                "Nombre actividad" => $row->activity->name,
                "id usuario" => $row->user->id,        
                "Nombre usuario" => $fullName,
                "Correo electronico" => $row->user->email,
                "Calificacion" => $row->rate,
                "Fecha de calificacion" => $dateRegister->toDateTimeString(),
            ]);
        } 

        return response()->json(['dataReport' => $finalData->toArray(), 'status' => 200]);        
    }

    public function getParticipantes($idEvent)
    {
        /* $rowsActivity = RateActivity::with([
                "event:id,name",
                "activity:id,name",
                "user:id,name,lastname,email"
            ])
            ->where('event_id', $eventId)
            ->orderBy('id')
            ->get();  */

        /* $user_id = auth()->user()->id;
        $users = User::select('id', 'name', 'lastname', 'email', 'online', 'pic')->with([
            'requestSent' => function ($requestSend) use ($user_id) {
                return $requestSend->select('id', 'status', 'guest_id', 'chat_id')->where('creator_id', $user_id);
            },
            'requestReceived' => function ($requestSend) use ($user_id) {
                return $requestSend->select('id', 'status', 'creator_id', 'chat_id')->where('guest_id', $user_id);
            },
            'company:id,name' 
        ])->whereHas('eventUsers', function ($q) use ($idEvent) {
            return $q->where('event_id', $idEvent);
        })->where('id', '<>', $user_id)->get();// ->paginate(50);  */

        $users = User::with([
            "empresa:name"
            ])
            ->whereHas('eventUsers', function ($q) use ($idEvent) {
                return $q->where('event_id', $idEvent);
            })
            ->get();

        /* $users = User::select('id', 'name', 'lastname', 'email', 'online', 'pic')
            ->with([
                "company:name"
            ])
            ->whereHas('eventUsers', function ($q) use ($idEvent) {
                return $q->where('event_id', $idEvent);
            })
            ->get(); */

        return response()->json($users);
    }
}
