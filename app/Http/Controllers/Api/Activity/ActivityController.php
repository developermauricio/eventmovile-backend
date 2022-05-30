<?php

namespace App\Http\Controllers\Api\Activity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Activity;
use App\Hall;
use App\EventInvitation;
use App\UrlInvitation;
use App\ActivityTracking;
use App\ActivitySpeaker;
use App\PaymentGateway;
use App\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PaymentGateways;
use SebastianBergmann\Environment\Console;
use App\Traits\HelperApp;

class ActivityController extends Controller
{
    use HelperApp;
    public function index()
    {
        //
        $role = auth()->user()->getRoleNames()->first();
        $user = DB::table('users')->where('id', Auth()->id())->first();

        if ($role == "super admin") {
            $activities = Activity::with('event:id,name,code_streaming')->with('type_activity:id,name')->get();
        }
        if ($role == "admin") {
            $activities = Activity::select('activities.*')
                ->join('events', 'events.id', '=', 'activities.event_id')
                ->with('event:id,name,code_streaming')
                ->with('mode:id,name')
                ->with('speaker:id,name,sort_description')
                ->with('type_activity:id,name')
                ->where('events.company_id', $user->model_id)
                ->get();
        }
        if ($role == "guest") {
            $activities = Activity::select('activities.*')
                ->with('event:id,name,code_streaming')
                ->with('mode:id,name')
                ->with('type_activity:id,name')
                ->with('speaker:id,name,sort_description,pic')
                ->join('guest_activities as ga', 'ga.activity_id', '=', 'activities.id')
                ->where('ga.guest_id', $user->model_id)
                ->where('ga.payed', null)
                ->orWhere('ga.payed', true)
                ->where('ga.guest_id', $user->model_id)
                ->get();
        }


        return $this->showAll($activities, 200);
    }

    public function show($activity, $user_id = null)
    {
        $activity = Activity::with('event:id,name,code_streaming')
            ->with('type_activity:id,name')
            ->with('mode:id,name')
            ->where('activities.id', $activity)->first();

        $speakers = DB::table('activities as a')
            ->select('s.*')
            ->join('activity_speakers as as', 'as.activity_id', '=', 'a.id')
            ->join('speakers as s', 's.id', '=', 'as.speaker_id')
            ->where('a.id', $activity->id)
            ->get();

        if ($user_id != 'null' && is_numeric($user_id)) {

            Log::info($user_id);
            $invitations = DB::table('url_invitations')->where('user_id', $user_id)->get();
            Log::info('Esta entrando');
            Log::info($invitations);
            $activity->invited = false;
            foreach ($invitations as $invitation) {
                $eventInv = DB::table('event_invitations')->where('id', $invitation->invitation_id)->first();
                $activitiesjson = json_decode($eventInv->activities);
                for ($i = 0; $i < count($activitiesjson); $i++) {

                    if ($activitiesjson[$i] == $activity->id) {
                        $activity->invited = true;
                        break;
                    } else {
                        $activity->invited = false;
                    }
                }

                if ($activity->invited == true) {
                    break;
                }
            }   // do what you need to do
        } else {
            $activity->loggued = false;
        }
        $activity->speakers = $speakers;
        $activity->authorization = false;
        if ($user_id != null) {
            $verifyYourInvitation = $this->verifyYourInvitation($activity->event_id, $user_id, $activity->id);
            if ($verifyYourInvitation['status'] == 'TRUE') {
                $activity->authorization = true;
            } else {
                $activityIsFree = $this->verifyActivityIfFree($activity->event_id, $activity->id);
                if ($activityIsFree['status'] == 'TRUE') {
                    $activity->authorization = true;
                }
            }
        } else {
            $activity->authorization = false;
        }
        return $this->showOne($activity);
    }

    public function activitiesEvent($eventId, $user_id = null)
    {
        $activities = Activity::with('event:id,name,code_streaming')
            ->with('mode:id,name')
            ->with('type_activity:id,name')
            ->where('event_id', $eventId)
            ->orderBy('start_date', 'desc')
            ->get();

        foreach ($activities as $act) {
            $speakers = DB::table('activities as a')
                ->select('s.*')
                ->join('activity_speakers as as', 'as.activity_id', '=', 'a.id')
                ->join('speakers as s', 's.id', '=', 'as.speaker_id')
                ->where('a.id', $act->id)
                ->get();

            $act->speakers = $speakers;

            if ($user_id != 'null') {
                $invitations = DB::table('url_invitations')->where('user_id', Auth()->id())->get();
                foreach ($invitations as $invitation) {
                    $eventInv = DB::table('event_invitations')->where('id', $invitation->invitation_id)->first();
                    $activitiesjson = json_decode($eventInv->activities); 
                    // dd($activitiesjson);              
                    for ($i = 0; $i < count($activitiesjson); $i++) {
                        //  dd($act->id);
                        if ($activitiesjson[$i] === $act->id) {
                            $act->invited = true;
                        } else {
                            $act->invited = false;
                        }
                    }
                }   // do what you need to do
            } else {
                $act->loggued = false;
            }
        }

        return $this->showAll($activities, 200);
    }

    public function store(Request $request)
    {

        $rules = [
            'name'                  => 'required',
            'sort_description'      => 'required',
            'unit_price'            => 'required',
            'duration_minutes'      => 'required',
            'event_id'              => 'required|exists:events,id',
            'mode_id'               => 'required|exists:mode_activities,id',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date',
            'pic'                   => 'required|file',
            'tags'                  => 'required',
            'friendly_url'          => 'required',
            'location_coordinates'  => 'required',
            'address'               => 'required',
            'country_id'            => 'required|exists:countries,id',
            'city_id'               => 'required|exists:cities,id',
            'type_activity_id'      => 'required',
            'onDemand'      => 'required',
        ];

        $this->validate($request, $rules);

        $pic = $this->saveFile($request->pic, 'pic', $request->name);
        $banner = $this->saveFile($request->pic, 'banner', $request->name);


        $activity = Activity::create([
            'name'                  => $request->name,
            'sort_description'      => $request->sort_description,
            'unit_price'            => $request->unit_price,
            'duration_minutes'      => $request->duration_minutes,
            'event_id'              => $request->event_id,
            'mode_id'               => $request->mode_id,
            'start_date'            => $request->start_date,
            'end_date'              => $request->end_date,
            'code_streaming'        => $request->code_streaming,
            'tags'                  => $request->tags,
            'friendly_url'          => $request->friendly_url,
            'location_coordinates'  => $request->location_coordinates,
            'address'               => $request->address,
            'country_id'            => $request->country_id,
            'city_id'               => $request->city_id,
            'guests_limit'          => $request->guests_limit,
            'type_activity_id'      => $request->type_activity_id,
            'pic'                   => $pic,
            'pic_banner'            => $banner,
            'voice_participation_check' => $request->voice_participation_check,
            'onDemand' => $request->onDemand

        ]);

        return $this->successResponse(['data' => $activity, 'message' => 'Activity Created'], 201);
    }

    public function saveFile($pic, $type, $name)
    {
        
        $file = $pic;
        try {
        # Storage::disk('local')->put($nameFile,  \File::get($file));
            # Storage::disk('digitalocean')->put($nameFile, \File::get($file));
            $path = Storage::disk('digitalocean')->putFile('uploads', $file, 'public');
            return $path;
        } catch (Exception $e) {
            return ' Error al subir el archivo '.$file;
        }

    }

    public function update(Request $request, Activity $activity)
    {
        $rules = [
            'name'                  => 'required',
            'sort_description'      => 'required',
            'unit_price'            => 'required',
            'duration_minutes'      => 'required',
            'event_id'              => 'required|exists:events,id',
            'mode_id'               => 'required|exists:mode_activities,id',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date',
            'pic'                   => 'required',
            'tags'                  => 'required',
            'friendly_url'          => 'required',
            'location_coordinates'  => 'required',
            'address'               => 'required',
            'country_id'            => 'required|exists:countries,id',
            'city_id'               => 'required|exists:cities,id',
            'guests_limit'          => 'required',
            'type_activity_id'      => 'required',
            'onDemand'      => 'required',
        ];

        $pic = $request->pic;
        $banner = $request->pic_banner;

        if (is_file($request->pic)) {
            $file = $request->pic;
            $nameFile = $file->getClientOriginalName();
            $pic = $nameFile;
        }

        if ($activity->pic <> "" && $activity->pic != $pic) {
            $request->pic = $this->saveFile($request->pic, 'logo', $request->event_id);
        }

        if (is_file($request->pic_banner)) {
            $file = $request->pic_banner;
            $nameFile = $file->getClientOriginalName();
            $banner = $nameFile;
        }
        if ($activity->pic_banner <> "" && $activity->pic_banner != $banner) {
            $request->pic_banner = $this->saveFile($request->pic_banner, 'banner', $request->event_id);
        }



        $activity->name = $request->name;
        $activity->sort_description  = $request->sort_description;
        $activity->unit_price = $request->unit_price;
        $activity->duration_minutes = $request->duration_minutes;
        $activity->event_id = $request->event_id;
        $activity->mode_id = $request->mode_id;
        $activity->start_date = $request->start_date;
        $activity->end_date = $request->end_date;
        $activity->code_streaming = $request->code_streaming;
        $activity->tags = $request->tags;
        $activity->friendly_url   = $request->friendly_url;
        $activity->location_coordinates  = $request->location_coordinates;
        $activity->address = $request->address;
        $activity->country_id = $request->country_id;
        $activity->city_id  = $request->city_id;
        $activity->guests_limit = $request->guests_limit;
        $activity->type_activity_id = $request->type_activity_id;
        $activity->pic = $request->pic;
        $activity->pic_banner = $request->pic_banner;
        $activity->voice_participation_check = $request->voice_participation_check;
        $activity->onDemand = $request->onDemand;

        $activity->save();

        return $this->successResponse(['data' => $activity, 'message' => 'Activity Updated'], 201);
    }

    public function destroy(Activity $activity)
    {
        $speakers = ActivitySpeaker::where('activity_id', $activity->id)->get();
        foreach ($speakers as $speaker) {
            $speaker->delete();
        }

        $activity->delete();
        return $this->successResponse(['data' => $activity, 'message' => 'Activity Deleted'], 201);
    }

    public function storeTracking(Request $request)
    {

        $rules = ["activity_id" => "required|exists:activities,id"];
        $this->validate($request, $rules);

        $activityTracking = ActivityTracking::where('activity_id', $request->activity_id)
            ->where('user_id', Auth()->id())
            ->first();

        if ($activityTracking) {
            return $this->successResponse(['data' => $activityTracking, 'message' => 'Activity tracking exists'], 201);
        } else {
            $activityTracking = ActivityTracking::create([
                "activity_id" => $request->activity_id,
                "user_id" => Auth()->id()
            ]);
        }

        return $this->successResponse(['data' => $activityTracking, 'message' => 'Activity tracking created'], 201);
    }

    public function metrics($activity)
    {

        $activity = Activity::findOrFail($activity);
        $eventInv = EventInvitation::where('event_id', $activity->event_id)->get();
        $invitations = 0;
        $registers = 0;
        foreach ($eventInv as $data) {
            foreach (json_decode($data->activities) as $act) {
                if ($act == $activity->id) {
                    $invitations += $data->quantity;
                    $urlInv = UrlInvitation::where('invitation_id', $data->id)
                        ->where('actived', true)->count();
                    $registers += $urlInv;
                }
            }
        }
        $tracking  = ActivityTracking::where('activity_id', $activity->id)->count();
        $arrayResponse = ["registers" => $registers, "invitations" => $invitations, "tracking" => $tracking];

        return $this->showOne($arrayResponse, 201);
    }

    public function activityUsers($activity)
    {

        $activity = Activity::findOrFail($activity);
        $eventInv = EventInvitation::where('event_id', $activity->event_id)->get();
        $invitations = 0;
        $registers = 0;
        $arrayUser = array();
        foreach ($eventInv as $data) {
            foreach (json_decode($data->activities) as $act) {
                if ($act == $activity->id) {
                    $urlInv = UrlInvitation::select('url_invitations.id', 'u.id as user_id', 'u.name', 'u.lastname', 'u.email')
                        ->join('users as u', 'url_invitations.user_id', '=', 'u.id')
                        ->where('url_invitations.invitation_id', $data->id)
                        ->where('actived', true)
                        ->get();
                    if (count($urlInv) > 0) {
                        foreach ($urlInv as $ui) {
                            array_push($arrayUser, $ui);
                        }
                    }
                }
            }
        }
        $arrayResponse = ["data" => $arrayUser];
        return $this->showOne($arrayResponse, 201);
    }

    public function verifyAuthorization(Request $request){
        $authorization = false;
        if ($request->user_id != null) {
            $verifyYourInvitation = $this->verifyYourInvitation($request->event_id, $request->user_id, $request->activity);
            if ($verifyYourInvitation['status'] == 'TRUE') {
                $authorization = true;
            } else {
                $activityIsFree = $this->verifyActivityIfFree($request->event_id, $request->activity);
                if ($activityIsFree['status'] == 'TRUE') {
                    $authorization = true;
                }
            }
        }
        return $authorization;
    }

    /**
     * devuleve los ids de actividades asociados a un evento
     */
    public function getIdsActivitiesByEvent($eventId){
        try {
            $activities = Hall::select('activities')
                ->where('event_id','=',$eventId)
                ->get();
            $arrAct = [];
            foreach($activities as $key => $val){   
                $activitiesArr = json_decode($val->activities);         
                for ($i = 0; $i < count($activitiesArr); $i++) {                                
                    array_push($arrAct,$activitiesArr[$i]);

                }            
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error obtiendo actividades!', 400);
        }
        return $arrAct;
    }

}
