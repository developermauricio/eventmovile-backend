<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Activity;
use App\EventInvitation;
use App\UrlInvitation;

class UrlInvitationController extends Controller
{
    public function showQRInformation($id){

        $data =  DB::table('users as u')
            ->select('u.id as user_id', 'u.name', 'u.lastname', 'u.email', 'ei.activities', 'e.name as event')
            ->rightJoin('url_invitations as ui', 'ui.user_id', '=', 'u.id')
            ->join('event_invitations as ei', 'ei.id', '=', 'ui.invitation_id')
            ->join('events as e', 'e.id', '=', 'ei.event_id')
            ->where('ui.id', $id)
            ->first();


        $arrayActivities = Array();
        $activities = json_decode($data->activities);
        for($i=0; $i < count($activities); $i++){
        
            $activity = DB::table('activities as a')
                ->select('a.name as activity', 'a.id as activity_id')
                ->where('id', $activities[$i])
                ->first();
        
            array_push($arrayActivities, $activity);
        }
        $data->activities = $arrayActivities;
        return $this->showOne($data);


    }
    public function index() {

        $role = auth()->user()->getRoleNames()->first();
        $user = DB::table('users')->where('id', Auth()->id())->first();

        
    }

    public function verifyUrl(Request $request){
        $urlInvitation = UrlInvitation::where('token', $request->token)
            ->first();
        
        if(!isset($urlInvitation->token)){
            return $this->errorResponse('Does not valid this invitation');
        }

        $urlInvitation->user_id = $request->user_id;
        $urlInvitation->actived = true;
        $urlInvitation->save();

        $invitation = DB::table('url_invitations as ui')
        ->select('ui.*', 'ei.event_id')
        ->join('event_invitations as ei', 'ei.id', '=', 'ui.invitation_id')
        ->where('ui.id', $urlInvitation->id)
        ->get();

        
        
        return $this->showOne($invitation);
    
    }

    public function show(UrlInvitation $urlInvitation){
        return $this->showOne($urlInvitation);
    }

    public function showToken($token){

        $url = UrlInvitation::where('token', $token)
            ->with('invitation:id,event_id')
            ->first();

        if(isset($url->id)){
            return $this->showOne($url);
        }
        return $this->errorResponse('Does not exist invitation', 500);
    }

    public function store(Request $request){

        $rules = [
            'url'=> 'required',
            'invitation_id'=>'required|exists:event_invitations,id',       
        ];

        
        $request->token= Str::random(5);
        $verifyToken = UrlInvitation::where('token', $request->token)->first();

        while(isset($verifyToken->token)){
            $request->token= Str::random(5);
            $verifyToken = UrlInvitation::select('token')->pluck('token')
                ->where('token', $token)->first();
        }

        $this->validate($request, $rules);

        $urlInvitation = UrlInvitation::create([
            'url'=> $request->url,
            'token'=> $request->token,
            'invitation_id'=> $request->invitation_id, 
        ]);

        return $this->successResponse(['data'=> $urlInvitation, 'message'=>'Url Created'], 201);
        
    }
    public function showActivitiesEvent($event){
        $arrayActivities = Array();
        $invitations = DB::table('url_invitations')->where('user_id', Auth()->id())->get();
        foreach ($invitations as $invitation){
            $eventInv = DB::table('event_invitations')->where('id',$invitation->invitation_id)->first();
            $activities = json_decode($eventInv->activities);
            for($i=0; $i < count($activities); $i++){
                array_push($arrayActivities, $activities[$i]);
            }
        }
        $activitiesEvent = DB::table('activities as a')
            ->where('a.event_id', $event)
            ->whereIn('a.id', $arrayActivities)
            ->orderBy('a.start_date')
            ->get();
        
        return $this->showAll($activitiesEvent);
    }
    public function update(Request $request){
        $rules = [
            'url'           => 'required',
            'token'         => 'required',
            'user_id'       => 'required|exists:users,id',
            'invitation_id' => 'required|exists:event_invitations,id',
            'actived'       => 'required',
        ];

        $this->validate($request, $rules);

        $urlInvitation = UrlInvitation::where('token', $request->token)
            ->where('invitation_id', $request->invitation_id)
            ->first();

        $urlInvitation->fill($request->all());
        
        if ($urlInvitation->isClean()) {
            return $this->successResponse(['data' => $urlInvitation, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $urlInvitation->save();

        return $this->successResponse(['data' => $urlInvitation, 'message' => 'Url Invitation Updated'],201);
    }
    
    public function eventUsers($event){
        $dynamicCols = DB::table('register_events as re')
        ->select('re.*', 'dr.user_id', 'dr.value')
        ->join('data_registers as dr', 'dr.register_id', 're.id')
        ->where('re.event_id', $event)
        ->get();

        $activities = Activity::where('event_id', $event)->get();
        
        
        $trackingAct = DB::table('activity_tracking as at')->select('at.*', 'a.name')
            ->join('activities as a', 'a.id', '=', 'at.activity_id')
            ->where('a.event_id', $event)
            ->get();

        $arrayCols = DB::table('register_events')->select('name')->where('event_id', $event)->get();
                
        $loginTracking = DB::table('event_users as eu')
            ->select('u.id as user_id', 'u.name as Nombre', 'u.lastname as Apellido', 'u.email as Email', 'e.name as Evento', 
                DB::raw('(CASE WHEN lt.actived = 0  THEN "No" 
                    WHEN lt.actived = 1 THEN lt.updated_at  WHEN lt.actived IS NULL THEN "No" END) AS En_el_evento'),
                'lt.created_at as Fecha_registro', 
                DB::raw('(CASE WHEN et.actived = 0  THEN "No" 
                    WHEN et.actived = 1 THEN et.created_at WHEN et.actived IS NULL THEN "No"  END) as Email_visualizado'))
            ->join('users as u', 'u.id', '=', 'eu.user_id')
            ->join('events as e', 'e.id', '=', 'eu.event_id')
            ->leftJoin('login_tracking as lt', 'lt.user_id', '=', 'u.id')
            ->leftJoin('email_tracking as et', 'et.user_id', '=', 'u.id')
            //->where('lt.event_id', $event)
            ->where('e.id', $event)
            ->groupBy('user_id', 'Nombre', 'Apellido', 'Email', 'Evento', 'En_el_evento', 'Fecha_registro', 'Email_visualizado')
            ->get();
        
        
       
        $loginTracking->map(function($item) use ($dynamicCols, $arrayCols, $activities, $trackingAct){
            foreach($arrayCols as $column){
                $colName = $column->name;
                $item->$colName = '';
            }
            foreach($dynamicCols as $col){
                if($item->user_id == $col->user_id){
                    $name = $col->name;
                    $item->$name = $col->value;
                }
            }
            foreach($activities as $act){
                $actName = $act->name;
                $item->$actName = '';
            }
            foreach($trackingAct as $col){
                if($item->user_id == $col->user_id){
                    $name = $col->name;
                    $item->$name = $col->created_at;
                }
            }
        });
        
        

        return $this->showAll($loginTracking, 201);

    }
}
