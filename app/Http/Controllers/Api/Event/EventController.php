<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Event;
use App\EventUser;
use App\EventType;
use App\EventInvitation;
use App\User;
use App\Activity;
use App\EventChat;
use App\Hall;
use App\Fair;
use App\EventStyle;
use App\PaymentGateway;
use App\UrlInvitation;
use App\Album;
use App\Traits\formatRegistrationEmail;
use App\Traits\sendEmail;
use Illuminate\Support\Facades\Log;
use LaravelQRCode\Facades\QRCode;

class EventController extends Controller
{
    //
    use  sendEmail, formatRegistrationEmail;

    public function getPeopleLimit($event){
        
        $type = EventType::where('name', 'Presencial')->first();

        $peopleEvent = EventUser::where('event_id', $event)->where('event_type_id', $type->id)->count();

        $event = Event::find($event);

        $people = $event->people_limit-$peopleEvent;

        return $people;
    }

    public function showEventUsers($event){
        $data = DB::table('event_users as eu')->select('eu.user_id', 'ei.event_id', 'eu.id as event_user_id', 'u.name as user_name', 'e.name as event_name',
            DB::RAW("(SELECT ui.id FROM url_invitations as ui WHERE ui.invitation_id = ei.id AND ui.user_id = eu.user_id) as urlinv"))
            ->join('event_invitations as ei', 'ei.event_id', '=', 'eu.event_id')
            ->join('events as e', 'e.id', '=', 'eu.event_id')
            ->join('users as u', 'u.id', '=', 'eu.user_id')
            ->where('eu.event_id', $event)
            ->get();
        
        $alldata = $data->filter(function($d){
            if($d->urlinv != null) {
                return $d;
            }
        });
        
        return $this->showAll($alldata);
            
    }

    public function index()
    {
        $role = auth()->user()->getRoleNames()->first();
        $user = DB::table('users')->where('id', Auth()->id())->first();

        //return $this->showAll($role,200);
        
        if($role == "super admin"){
            $events =Event::with('activities:id,name')->with('company:id,name')
            ->orderBy('id', 'desc')
            ->get();
        }
        if($role == "admin"){
            $events = Event::where('company_id', $user->company_id)->with('activities:id,name')
                ->with('company:id,name')
                ->orderBy('id', 'desc')
                ->get();

        }
        if($role == "guest"){
            $arrayActivities = Array();
            $invitations = DB::table('url_invitations')->where('user_id', Auth()->id())->get();
            foreach ($invitations as $invitation){
                $eventInv = DB::table('event_invitations')->where('id',$invitation->invitation_id)->first();
                $activities = json_decode($eventInv->activities);
                for($i=0; $i < count($activities); $i++){
                    array_push($arrayActivities, $activities[$i]);
                }
            }
            $events = Event::select('events.*')
            ->with('activities:id,name')
            ->with('company:id,name')
            ->join('activities as a', 'a.event_id', '=', 'events.id' )
            ->whereIn('a.id', $arrayActivities)
            ->groupBy('events.id')
            ->get();
        }
       
        Log::info($events);
        return $this->showAll($events,200);
    }

    public function show($id)
    {
        $activities=Activity::where('event_id',$id)
            ->pluck('id')
            ->toArray();

        $type = EventType::where('name', 'Presencial')->first();

        $peopleEvent = EventUser::where('event_id', $id)->where('event_type_id', $type->id)->count();

        $event = Event::where('id', $id)
            ->with('company:id,name')
            ->with('city:id,name')
            ->with('style')
            ->with('city_event.country_event')
            ->with('type:id,name')
            ->with('tickets')
            ->with('payment')
            ->get();

        if(isset($event[0])){
            $event[0]['activities']=$activities;
            $event[0]['people_event']=$peopleEvent;
        };
        

        return $this->showOne($event);
    }

    public function store(Request $request)
    {

        $city = json_decode($request->city_event_id);
                
        $rules = [
            'name'              => 'required',
            'event_type_id'     => 'required|exists:event_types,id',
            'description'       => 'required',
            // 'address'           => 'required',
            // 'city_event_id'     => 'required',
            // 'city_id'           => 'required|exists:cities,id',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date',
            'friendly_url'      => 'required',
            'duration_minutes'  => 'required',
            'company_id'        => 'required|exists:companies,id',
            'message_email'     => 'required',
            'subject_email'     => 'required',
            'req_networking'     => 'required',
            'req_chat'     => 'required',
            'req_make_question'     => 'required',
            'req_files'     => 'required',
            'req_schedule'     => 'required',
            'req_survey'     => 'required',
            'req_chat_event'     => 'required',
            'req_web_app' => 'required',     
        ];
        $this->validate($request, $rules);
        
        //subir imagenes
        $mapa = '';
        if($request->wa_req_mapa){
            $mapa = $this->saveImg($request->wa_mapa_value);      
        }
       
        $toSave = array (
            'name' => $request->name,
            'event_type_id' => $request->event_type_id,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            //'start_hour' => $request->start_hour,
            //'end_hour' => $request->end_hour,
            'city_id' => $request->city_id === null || $request->city_id === '' ? null : $request->city_id,
            'city_event_id' => $city  === null || $city === '' || $city->id === 0 ? null : $city->id ,
            'address' => $request->address,
            'duration_minutes' => $request->duration_minutes,
            'friendly_url' => $request->friendly_url,
            'company_id' => $request->company_id,
            'message_email' => $request->message_email,
            'subject_email' => $request->subject_email,
            'code_streaming' => $request->code_streaming,
            'password' => $request->password,
            'actived' => $request->actived,
            'image_on_register' => $request->image_on_register,
            'unique_login' => $request->unique_login,
            'req_payment' => $request->req_payment,
            'payment_on_login' => $request->payment_on_login,
            'payment_name' => $request->payment_name,
            'key' => $request->key,
            'token' => $request->token,
            'key_dev' => $request->key_dev,
            'token_dev' => $request->token_dev,
            'mode' => $request->mode,
            'req_networking' => $request->req_networking,
            'req_make_question' => $request->req_make_question,
            'req_files' => $request->req_files,
            'req_schedule' => $request->req_schedule,
            'req_probes' => $request->req_probes,
            'req_survey' => $request->req_survey,
            'req_chat' => $request->req_chat,
            'req_chat_event' => $request->req_chat_event,
            'req_videocall' => $request->req_videocall,
            'person_numbers' => $request->person_numbers,
            'merchantId' => $request->merchantId,
            'accountId' => $request->accountId,
            'api_login' => $request->api_login,
            'merchantId_dev' => $request->merchantId_dev,
            'accountId_dev' => $request->accountId_dev,
            'api_login_dev' => $request->api_login_dev,
            'req_web_app' => $request->req_web_app,
            'wa_req_path' => $request->wa_req_path,
            'wa_path_value' => $request->wa_path_value,
            'wa_req_feria_comercial' => $request->wa_req_feria_comercial,
            'wa_req_mapa' => $request->wa_req_mapa,
            'wa_mapa_value' => $mapa,
        );          
        $event = Event::create($toSave);        
        //si requiere webapp se crea album asociado al event}
        if(isset($request->req_web_app) && $request->req_web_app==1){
            try {                 
                $createAlbum = Album::create([
                    'id_event'=> $event->id,
                    'description'=>''                                    
                ]);
            } catch (\Throwable $th) {
                return $this->errorResponse('Error create album', 500);                   
            }
        }        
        if(isset($request->req_payment) && $request->req_payment==1){
            $toSave['event_id'] = $event->id;
            $payment = PaymentGateway::create($toSave);
        }

        return $this->successResponse(['data'=> $event, 'message'=>'Event Created'], 201);
    }

    public function saveFile($pic, $type, $name){

        $file = $pic;
    
        $nameFile = $file->getClientOriginalName();

        $number = Event::count();

        $explode = explode(".", $nameFile);
        $nameFile ="event_".$type."_".$name."_".$explode[0].$number.".".$explode[1];
        $nameFile = str_replace(' ', '', $nameFile);
        
        Storage::disk('local')->put($nameFile,  \File::get($file));
    
        return $nameFile;
    }

    public function update(Request $request, Event $event)
    {
        Log::info('entro update');
        $city = json_decode($request->city_event_id);
        //
        $rules = [
            'name'              => 'required',
            'event_type_id'     => 'required|exists:event_types,id',
            'description'       => 'required',
            // 'city_id'           => 'required|exists:cities,id',
            // 'address'           => 'required',
            // 'city_event_id'     => 'required',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date',
            'friendly_url'      => 'required',
            'duration_minutes'  => 'required',
            'company_id'        => 'required|exists:companies,id',
            'message_email'     => 'required',
            'subject_email'     => 'required',
            'req_networking'     => 'required',
            'req_make_question'     => 'required',
            'req_files'     => 'required',
            'req_schedule'     => 'required',
            'req_probes'     => 'required',
            'req_survey'     => 'required',
            'req_chat'     => 'required',
            'req_chat_event' => 'required',
            'req_web_app' => 'required',
        ];

        $this->validate($request, $rules);        
        $toSavePay = $request->all();  
        //subir imagenes
        $mapa = '';
        
        if($request->wa_req_mapa){
            $mapa = $this->saveImg($request->wa_mapa_value);      
        }
        
        $toSave = array (
            'name' => $request->name,
            'event_type_id' => $request->event_type_id,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,            
            'city_id' => $request->city_id === null || $request->city_id === '' ? null : $request->city_id,
            'city_event_id' => $city === null || $city === '' || $city->id === 0 ? null : $city->id,
            'address' => $request->address,
            'duration_minutes' => $request->duration_minutes,
            'friendly_url' => $request->friendly_url,
            'company_id' => $request->company_id,
            'message_email' => $request->message_email,
            'subject_email' => $request->subject_email,
            'code_streaming' => $request->code_streaming,
            'password' => $request->password,
            'actived' => $request->actived,
            'image_on_register' => $request->image_on_register,
            'unique_login' => $request->unique_login,
            'req_payment' => $request->req_payment,
            'payment_on_login' => $request->payment_on_login,
            'payment_name' => $request->payment_name,            
            'req_networking' => $request->req_networking,
            'req_make_question' => $request->req_make_question,
            'req_files' => $request->req_files,
            'req_schedule' => $request->req_schedule,
            'req_probes' => $request->req_probes,
            'req_survey' => $request->req_survey,
            'req_chat' => $request->req_chat,
            'req_chat_event' => $request->req_chat_event,
            'req_videocall' => $request->req_videocall,
            'person_numbers' => $request->person_numbers,            
            'req_web_app' => $request->req_web_app,
            'wa_req_path' => $request->wa_req_path,
            'wa_path_value' => $request->wa_path_value,
            'wa_req_feria_comercial' => $request->wa_req_feria_comercial,
            'wa_req_mapa' => $request->wa_req_mapa,
            'wa_mapa_value' => $mapa,
        );                  
        DB::table('events')->where('id',$event->id)->update($toSave);   
             
        if(isset($request->req_payment) && $request->req_payment==1){
            $payment = PaymentGateway::where('event_id',$event->id)->first();
            $toSave['event_id'] = $event->id;
            if($payment){
                $payment->update($toSavePay);
            }   else {
                $payment = PaymentGateway::create($toSave);
            }
  
        }

        return $this->successResponse(['data' => $event, 'message' => 'Event Updated'],201);
    }

    public function destroy(Event $event)
    {
        $event->delete();   
        return $this->successResponse(['data' => $event, 'message' => 'Event Deleted'], 201);
    }

    public function eventUsers(Request $request){
        Log::info('llega... request: ');
        Log::info($request);
        $validate = EventUser::where('user_id', $request->user_id)->where('event_id', $request->event_id)->first();        
        $user = User::where('id', $request->user_id)->first();
        Log::info('validate: ');
        Log::info($validate);
        Log::info('user: ');
        Log::info($user);

        if(!$user->hasRole('guest')) {
            $user->assignRole('guest');
        }            

        if(!$validate){
            $eventUser = EventUser::create([
                'user_id'=>$request->user_id,
                'event_id'=>$request->event_id,
                'event_type_id' => $request->event_type_id,
            ]);
            $this->sendEmailEvent($request);
        }else{
            return $this->successResponse(['data'=> $validate, 'message'=>'User in event exists'], 201);
        }
        
        return $this->successResponse(['data'=> $eventUser, 'message'=>'User in event'], 201);

    }
    public function sendEmailEvent($request){
        $hall= null;
        $message_email = '{"message_email_1":"Registro exito","message_email_2":"Virtual / Presencial","message_email_3":"Ir al sitio del evento","message_email_4":"Salas","message_email_5":"Inicia/", "message_email_6":"Termina/", "message_email_7":"Añadir a calendario"}';
        $event = Event::where('id', $request->event_id)->with('style')->first();
        $user = User::findOrFail($request->user_id);
        // Get Halls of the event
        if(isset($request->event_id)){
            $hall=Hall::where([['event_id','=',$request->event_id],['hall_type_id','=', 2]])->get();
        }

        //verify message for email
        if(empty($request->message_email_1)==false && empty($request->message_email_2)==false && empty($request->message_email_3)==false && empty($request->message_email_4)==false && empty($request->message_email_5)==false && empty($request->message_email_6)==false && empty($request->message_email_7)==false){
            $message_email = '{"message_email_1":"'.$request->message_email_1.'","message_email_2":"'.$request->message_email_2.'","message_email_3":"'.$request->message_email_3.'","message_email_4":"'.$request->message_email_4.'","message_email_5":"'.$request->message_email_5.'","message_email_6":"'.$request->message_email_6.'","message_email_7":"'.$request->message_email_7.'"}';
        }
        Log::debug("message");
        Log::debug($message_email);

        //creacion de invitacion
        $activities = Activity::where('event_id', $request->event_id)->get();
        $arrayAct = Array();
        $qr = false;
        foreach($activities as $act){
            $activity = DB::table('activities as a')
            ->select('a.*', 'm.name as mode_name', 'm.id as mode_id')
            ->join('mode_activities as m', 'm.id', '=', 'a.mode_id')
            ->where('a.id', $act->id)
            ->first();
            if($activity->mode_name == "Presencial" && $activity->type_activity_id ==1 || $activity->mode_id == 1 && $activity->type_activity_id ==1){
                $qr = true;
                array_push($arrayAct, $act->id);
            }
        }
        $validate = EventInvitation::where("event_id", $request->event_id)->where("email", $user->email)->first();
        if(!$validate){
            $eventInv = EventInvitation::create([
                "event_id" => $request->event_id,
                "email" => $user->email,
                "quantity" => 1,
                "name" => $user->name." ".$user->lastname,   
                "activities" => json_encode($arrayAct)
            ]);

            $urlInv = UrlInvitation::create([
                "url" => "Url",
                "token" => "123456",
                "user_id" => $user->id,
                "invitation_id" => $eventInv->id,
                "actived" => 1,
            ]);

            if($qr){
                $qr = $urlInv->id;
            }
        }

        Log::debug("event...");
        Log::debug($event);
    
        $message1 = str_replace("*u", $user->name, $event->message_email);
        $message2 = str_replace("*c", $event->password, $message1);
        $message = str_replace("*e", $event->name, $message2);

        $eventUser = EventUser::where('event_id', $event->id)->where('user_id', $user->id)->first();
        if($eventUser->id){ 
            $tracking = $eventUser->id; 
        }else{ 
            $tracking = false;
        }
        $format = $this-> formatEmailEvent($message, $event, $qr, $tracking, $hall, $message_email);    
        $cadena = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, htmlentities($format['template']));
        $cadena = html_entity_decode($cadena);
        //Storage::disk('local')->put("DumpEventEmail.txt",  $cadena);
       
         $email = $this->sendEmail($user->email, $event->subject_email, $cadena);
        
    }
    // public function sendEmailEvent($request){
        
    //     $event = Event::where('id', $request->event_id)->with('style')->first();
    //     $user = User::findOrFail($request->user_id);
    
    //     $message1 = str_replace("*u", $user->name, $event->message_email);
    //     $message2 = str_replace("*c", $event->password, $message1);
    //     $message = str_replace("*e", $event->name, $message2);

    //     $eventInvitation = DB::table('event_invitations')->where('event_id', $request->event_id)->get();
    //     foreach($eventInvitation as $ei){
    //         $urlInv = UrlInvitation::where('invitation_id', $ei->id)->where('user_id', $request->user_id)->first();
    //         if($urlInv){
    //             $eventInv = DB::table('event_invitations')->where('id', $urlInv->invitation_id)->first();
    //         }
    //     }
    //     $qr = false;
    //     if(isset($eventInv)){
    //         $activities = json_decode($eventInv->activities);
    //         for($i=0; $i < count($activities); $i++){
    //             $activity = DB::table('activities as a')
    //             ->select('a.*', 'm.name as mode_name', 'm.id as mode_id')
    //             ->join('mode_activities as m', 'm.id', '=', 'a.mode_id')
    //             ->where('a.id', $activities[$i])
    //             ->first();

    //             if($activity->mode_name == "Presencial" || $activity->mode_id == 1 ){
    //                 $qr = $urlInv->id;
    //             }
    //         }
    //     }
        

    //     $eventUser = EventUser::where('event_id', $event->id)->where('user_id', $user->id)->first();
    //     if($eventUser->id){ 
    //         $tracking = $eventUser->id; 
    //     }else{ 
    //         $tracking = false;
    //     }
    //     $format = $this-> formatEmailEvent($message, $event, $qr, $tracking);
    //     $email = $this->sendEmail($user->email, $event->subject_email, "'".$format['template']."'");
    // }

    public function usersForEvent($event){

        $carbon = new \Carbon\Carbon(); 
        $date = $carbon->format('Y-m-d');
        
        $eventUser = DB::table('event_users as eu')
        ->select('eu.user_id', 'u.id as user_id', 'u.name','u.lastname', 'u.email',  DB::raw("(SELECT at.revoked FROM oauth_access_tokens as at WHERE at.user_id = eu.user_id 
            AND at.created_at > '".$date." 00:00:00' order by at.created_at desc limit 1 ) as revoked"),
            DB::raw("(SELECT n.creator_id FROM networkings as n WHERE n.creator_id =".Auth()->id()." and n.guest_id=eu.user_id limit 1 ) as creator_id"),
            DB::raw("(SELECT n.guest_id FROM networkings as n WHERE n.guest_id =".Auth()->id()." and n.creator_id=eu.user_id limit 1 ) as guest_id"),
            DB::raw("(SELECT n.confirmed FROM networkings as n WHERE n.guest_id =".Auth()->id()." and n.creator_id = eu.user_id
            OR n.creator_id =".Auth()->id()." and n.guest_id = eu.user_id limit 1 ) as confirmed"),
            DB::raw("(SELECT n.id  FROM networkings as n WHERE n.guest_id =".Auth()->id()." and n.creator_id = eu.user_id
            OR n.creator_id =".Auth()->id()." and n.guest_id = eu.user_id limit 1 ) as networking"),
            DB::raw("(
                select 
                    if(dr.value != '',dr.value,'') as Empresa
                from register_events as re 
                join data_registers as dr ON re.id = dr.register_id  
                WHERE re.event_id  = ".$event." and re.name = 'Empresa' AND dr.user_id = eu.user_id
            )  empresa
            "),
            DB::raw("(
                select 
                    if(dr.value != '',dr.value,'') as Cargo
                from register_events as re 
                join data_registers as dr ON re.id = dr.register_id  
                WHERE re.event_id  =".$event." and  re.name = 'Cargo' AND dr.user_id = eu.user_id
                )  cargo")
            )
        ->join('users as u', 'u.id', '=', 'eu.user_id')
        ->where('eu.event_id', $event)
        ->where('eu.user_id', '!=', Auth()->id())
        ->groupBy('eu.user_id', 'u.id', 'name', 'email', 'revoked', 'creator_id', 'guest_id', 'confirmed', 'networking')
        ->get();
        /* 
        Log::info($eventUser);
                return null;
        */
        return $this->showAll($eventUser, 200);

    }

    public function appUserExternal (Request $request){

        $rules = [
            'name'       => 'required',
            'lastname'   => 'required',
            'email'      => 'required',
            'event_id'   => 'required|exists:events,id',
        ];

        $this->validate($request, $rules);

        $user = User::where('email', $request->email)->first();

        if(!isset($user->email)){
        
            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'lastname' => $request->lastname,
                'password' =>  Hash::make($request->email)
            ]);

            $user->assignRole("guest");
        }

        $request->token= Str::random(10);
        $verifyToken = EventUser::where('token', $request->token)->first();

        while(isset($verifyToken->token)){
            $request->token= Str::random(10);
            $verifyToken = EventUser::select('token')->pluck('token')
                ->where('token', $token)->first();
        }
        $eventUser = EventUser::where('user_id', $user->id)->where('event_id', $request->event_id)->first();

        if(!isset($eventUser->user_id)){
            $eventUser = EventUser::create([
                "user_id" => $user->id,
                "event_id" => $request->event_id,
                'token' => $request->token,
            ]);
        }

        return $this->showOne($eventUser);


    }
    public function storeExternal(Request $request)
    {
                
        $rules = [
            'name'              => 'required',
            'description'       => 'required',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date', 
        ];

        $this->validate($request, $rules);


        

        $event = Event::create([
            'name'              => $request->name,
            'description'       => $request->description,
            'city_id'           => 1,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'friendly_url'      => "cxsummit",
            'duration_minutes'  => 500000,
            'company_id'        => 1,
            'message_email'     => "without email",
            'subject_email'     => "without email",
            'code_streaming'   => "without code",
        ]);

        $activity = Activity::create([
            'name'                  => $event->name,
            'sort_description'      => $request->description,
            'unit_price'            => 0,
            'duration_minutes'      => 50000,
            'event_id'              => $event->id,
            'mode_id'               => 2,
            'start_date'            => $event->start_date,
            'end_date'              => $event->end_date,
            'code_streaming'        => "withoutimg.png",
            'tags'                  => $event->name,
            'friendly_url'          => $event->name,
            'location_coordinates'  => "1231312",
            'address'               => "withoutimg",
            'country_id'            => 1,
            'city_id'               => 1,
            'guests_limit'          => 10000000,
            'type_activity_id'      => 1,
        ]);
        
        
        return $this->successResponse(['data'=> $event, 'message'=>'Event Created'], 201);
    }

    public function metrics($event){

        $data = DB::table("event_users as eu")
        ->distinct('eu.user_id', 'eu.event_id')
        ->where("eu.event_id", $event)
        ->count();

        $registers = DB::table("event_users as eu")
            ->select(DB::raw("'Registrados' as type"),
                DB::raw("'' as cantidad"),
                DB::raw($data),
                DB::raw("'' as porcentaje"))
            ->where("eu.event_id", $event);
       

        $logins = DB::table("login_tracking as lt")
            ->select(DB::raw("'Entradas' as type"),
                DB::raw('(SELECT COUNT(*) FROM login_tracking WHERE event_id = '.$event.' AND actived = true) AS cantidad'),
                DB::raw('(SELECT COUNT(*) FROM event_users WHERE event_id = '.$event.') AS total'),
                DB::raw('(((SELECT COUNT(*) FROM login_tracking WHERE event_id = '.$event.' AND actived = true)*100)/(SELECT COUNT(*) FROM event_users WHERE event_id = '.$event.')) AS porcentaje'))
            ->where("lt.event_id", $event)
            ->where("lt.actived", true);
            

        $certificate = DB::table('certificate as c')
        ->where('c.model_id', $event)
        ->where('c.model','event')
        ->first();

        if($certificate){
            $certificateTracking =  DB::table("certificate-tracking as ct")
                ->select(DB::raw("'Certificados' as type"),
                    DB::raw('(SELECT COUNT(*) FROM `certificate-tracking` WHERE certificate_id = '.$certificate->id.') AS cantidad'),
                    DB::raw('(SELECT COUNT(*) FROM event_users WHERE event_id = '.$event.') AS total'),
                    DB::raw('(((SELECT COUNT(*) FROM `certificate-tracking` WHERE certificate_id = '.$certificate->id.')*100)/(SELECT COUNT(*) FROM event_users WHERE event_id = '.$event.')) AS porcentaje'))
                ->where("ct.certificate_id", $certificate->id);

            

            $emails = DB::table("email_tracking as et")
                ->select(DB::raw("'Email visto' as type"),
                    DB::raw('(SELECT COUNT(*) FROM email_tracking WHERE event_id = '.$event.') AS cantidad'),
                    DB::raw('(SELECT COUNT(*) FROM event_users WHERE event_id = '.$event.') AS total'),
                    DB::raw('(((SELECT COUNT(*) FROM email_tracking WHERE event_id = '.$event.')*100)/(SELECT COUNT(*) FROM event_users WHERE event_id = '.$event.')) AS porcentaje'))
                ->where("et.event_id", $event)
                ->union($logins)
                ->union($certificateTracking)
                ->union($registers)
                ->groupBy('type',  'cantidad', 'total', 'porcentaje')
                ->orderBy('type', 'desc')
                ->get();
        }else{
            $emails = DB::table("email_tracking as et")
                ->select(DB::raw("'Email visto' as type"),
                    DB::raw('(SELECT COUNT(*) FROM email_tracking WHERE event_id = '.$event.') AS cantidad'),
                    DB::raw('(SELECT COUNT(*) FROM event_users WHERE event_id = '.$event.') AS total'),
                    DB::raw('(((SELECT COUNT(*) FROM email_tracking WHERE event_id = '.$event.')*100)/(SELECT COUNT(*) FROM event_users WHERE event_id = '.$event.')) AS porcentaje'))
                ->where("et.event_id", $event)
                ->union($logins)
                ->union($registers)
                ->groupBy('type',  'cantidad', 'total', 'porcentaje')
                ->orderBy('type', 'desc')
                ->get();
        }
        
        return $this->showAll($emails);

    }

    public function usersEvent($event){
        $eventUsers = EventUser::select('u.id as user_id', 'u.name', 'u.lastname', 'u.email')
            ->join('users as u','u.id', '=', 'event_users.user_id')
            ->where('event_id', $event)
            ->get();

        return $this->showAll($eventUsers);
    }
    public function eventChatController(Request $request){
        $messages=null;
        $rules = [
            'event_id' => 'required|exists:events,id',
            'message'  => 'required',
        ];

        $this->validate($request, $rules);  

        $message = EventChat::create([
            'event_id'=> $request->event_id,
            'user_id'=> Auth()->id(),
            'message'=> $request->message, 
        ]);

        return $this->successResponse(['data'=> $message, 'message'=>'Message sent'], 201);
    }
    // query desc para traer los ultimos 100 y despues los ordenamos en front
    public function getChatMessagesEvent($event_id){
        // $messages = DB::table('event_chats as ec')
        // ->select('ec.*', 'u.name', 'u.lastname', 'u.pic')
        // ->where('event_id', $event_id)
        // ->join('users as u', 'u.id', '=', 'ec.user_id')
        // ->orderBy('created_at','desc')
        // ->take(100)
        // ->get();

        // return $this->showAll($messages, 200);
        
        $chatMessages = EventChat::where('event_id', $event_id)->with('user')->latest('created_at')->paginate(10);
        $finalData = collect();
        foreach ($chatMessages as $messages) {
            $finalData->push([
                "id" => $messages->id,
                "event_id" => $messages->event_id,
                "user_id" => $messages->user->id,
                "pic" => $messages->user->pic,
                "name" => $messages->user->name,
                "lastname" => $messages->user->lastname,
                "message" => $messages->message,
                "created_at" => $messages->created_at,
                "updated_at" => $messages->updated_at

            ]);
        }
        return response()->json(["data" => $finalData, "lastPage" => $chatMessages->lastPage()]);
        // return $this->showAll($finalData, 200);
    }

    /**
     * devuelve la cantidad de días de un evento,
     * discriminado por hora ini dia hora fin 
     *  las fechas para filtrar las actividades
     * @param
     * eventId = evento a consultar
     * @return
     * int Cantidad de días
     */
    private function cantDaysEvent($eventId){
        //cantidad de días
        $days=Event::where('id',$eventId)            
            ->select(DB::raw("DATEDIFF(end_date,start_date) AS dias"))
            ->get(); 
        //fecha inicial evento
        $dateIni= Event::where('id',$eventId)            
        ->select("start_date AS ini")
        ->get();
        //fechas para actividades
        $arrDates = [];
        $date_ini =  date_create($dateIni[0]->ini);
        for ($i=1; $i <= $days[0]->dias; $i++) {             
            $arrDates[$i] = array(
                "ini"=>date_format($date_ini,"Y-m-d 00:00:00"),
                "fin"=>date_format($date_ini,"Y-m-d 23:59:00")
            );
            date_add($date_ini,date_interval_create_from_date_string("1 day"));            
        }
        
        return $arrDates;          
    }

    /**
     * devuelve las salas asociadas a una actividad     
     * id, name, description
     * @param
     * eventId = id del evento
     * actId = id de la actividad
     * @return
     * Object(hall)->get() con id, name, description de salas
     */
    private function getHallsEvent($eventId, $actId)
    {            
        try {
            $hall = Hall::select('id','name','description')
            ->where('event_id', $eventId)
            ->where('activities','LIKE','%['.$actId.']%')#cuando esta solo una actividad
            ->orWhere('activities','LIKE','%['.$actId.',%')#cuando esta al inicio
            ->orWhere('activities','LIKE','%, '.$actId.',%')#cuando esta en medio de varias actividades
            ->orWhere('activities','LIKE','%, '.$actId.']%')#cuando esta al final 
            ->get();                   
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al traer salas para la actividad', 500);   
        }

        return $hall;
    }

    /**
     * devuelve las actividades por día
     * se pueden traer actividades activas o inactivas
     * @param
     * eventId = id de evento
     * ini = date inicial
     * fin = date final
     * isActive = activa (true) || inactiva (false)
     * @return
     * Object(Activity)->get() todos los campos de activity
     */
    private function getActivitiesDay($eventId, $ini, $fin, $isActive =true){
        if(!$isActive){
            $activities=Activity::select('id','name','sort_description','start_date','end_date','guests_limit')
                ->where('start_date','>=',$ini)
                ->where('end_date','<=',$fin)
                ->where('event_id','=',$eventId)
                ->get();          
        }else{
            $activities=Activity::select('id','name','sort_description','start_date','end_date','guests_limit')
                ->where('actived','1')
                ->where('start_date','>=',$ini)
                ->where('end_date','<=',$fin)
                ->where('event_id','=',$eventId)
                ->get();            
        }        
        return $activities;
    }

    /**
     * retorna name,sort_description de los speaker asociados a una activdad
     * @param
     * activityId = id de la actividad a buscar speaker
     */
    private function getSpeakersByActivity($activityId){                
        try {

            $speakers = DB::table('activity_speakers as acsp')
                ->select('s.name', 's.sort_description')
                ->join('speakers as s', 's.id', '=', 'acsp.speaker_id')
                ->where('acsp.activity_id','=', $activityId)
                ->get();            
        } catch (\Throwable $th) {            
            return $this->errorResponse('Error obteniendo speakers', 500);            
        }     
        return $speakers;   
    }


    /**
     * returna los valores para mostrar la agenda general del evento
     * @param
     * eventId = evento a consultar
     * @return
     * Array con los campos necesario para renderizar en la vista 
     */
    public function scheduleGeneral($eventId){
        $agendaGeneral;
        //fechas de dias evento     
        $dateDays = $this->cantDaysEvent($eventId);                                 
        //traer actividades por día
        foreach($dateDays as $key => $val){
            $activitiesData = $this->getActivitiesDay($eventId,$val['ini'],$val['fin'],true);            
            $agendaGeneral[$key] = $activitiesData;
            //salas asociadas a cada actividad
            foreach($activitiesData as $keyAct => $val){
                $hallsByAct = $this->getHallsEvent($eventId,$val->id);   
                $activitiesData[$keyAct]['halls'] = $hallsByAct;                
            }
            //speakers asociados a la actividad
            foreach($activitiesData as $keyAct => $val){
                $speakersByAct = $this->getSpeakersByActivity($val->id);
                $activitiesData[$keyAct]['speakers'] = $speakersByAct;                
            }            
            
        }
        return $agendaGeneral;        
    }

    /**
     *Remplazar por busqueda desde el fornt
     * @
     */
    public function searchInSchedule($eventId, $q){        
        $find = DB::table('speakers as s')
            ->select('s.id as speaker_id',
                's.name as speaker_name',
                'as2.activity_id as speaker_to_activity_id',
                'a2.name as speaker_to_activity_name', 
                'a2.id as activity_id',
                'a2.sort_description as activity_description')
                ->join('activity_speakers as as2','s.id', '=', 'as2.speaker_id')
                ->join('activities as a2','a2.id', '=', 'as2.activity_id')
                ->where('a2.event_id', $eventId)
                ->orWhere('s.name','LIKE','%'.$q.'%')
                ->orWhere('s.sort_description','LIKE','%'.$q.'%') //descripcion speaker, sala= nombre, descripcion, mostrar vista actividad salas
                ->orWhere('a2.name','LIKE','%'.$q.'%')
                ->orWhere('a2.sort_description','LIKE','%'.$q.'%')
                ->get();
            return $find;
    }

    /**
     * agregar un item(actividad,speaker, sala, etc) a mi lista de favoritos
     * @param
     * Request = peticion post con:
     * user_id = id usuario a agregar a favoritos
     * type_item = tipo de favorito a agregar
     * item_id = id del favorito a agregar
     * event_id = evento del cual se van a tomar los valores
     * @return
     * success(201) = agregado a favoritos || error(401) = ya esta en favoritos 
     */
    public function addToFavoriteSchedule( Request $request){        
        $userId = $request->user_id;
        $typeItem =$request->type_item;
        $itemId = $request->item_id;
        $eventId = $request->event_id;
        //valid if exists in favorites
        $validate = DB::table('schedule_favorites')
            ->select('id')
            ->where('type_item','=',$typeItem)
            ->where('id_item','=',$itemId)
            ->where('id_user','=',$userId)
            ->where('id_event','=',$eventId)
            ->get();                    
        if(count($validate)==0){
            $add = DB::table('schedule_favorites')
            ->insert([
                'type_item' => $typeItem,
                'id_item' => $itemId,
                'id_user' => $userId,
                'id_event' => $eventId,
            ]);
            return $this->successResponse(['data'=> $add, 'message'=>'Agregado a favorito'], 201);
        }else{
            return $this->errorResponse('Ya se encuentra en tus favoritos', 200);            
        }        
    }

    /**
     * remueve un item de la lista de favoritos para un usuario en un evento
     * @param
     * Request = petición PUT
     * id = id del item (activity, hall, etc) a ser borrado
     */
    public function removeToFavoriteSchedule(Request $request){
        $id = $request->id;        
        $delete = DB::table('schedule_favorites')->where('id', '=', $id)->delete();
        if($delete)
            return $this->successResponse(['data'=> $delete, 'message'=>'Removido de favorito'], 201);            
        else
            return $this->errorResponse('Error al remover de favoritos', 500);            
    }
    /**
     * returna los valores para mostrar la agenda favoritos del invitado para x evento
     * @param
     * userID = usuario a consultar
     * eventId = evento a consultar
     * @return
     * lista para renderizar en vista || 401 = no tienes favoritos
     */
    public function scheduleFavorites($userId, $eventId){        
        $validate = DB::table('schedule_favorites')
            ->where('id_user','=',$userId)
            ->where('id_event','=',$eventId)
            ->get();        
        if(count($validate)>0){
            $favorities = [];
            foreach($validate as $key => $val){
                switch($val->type_item){
                    case 'activity':
                        $fav =Activity::select('id','name','pic','start_date')                            
                            ->where('id','=',$val->id_item)
                            ->get()->toArray();
                        $favorities['data'][$key] = $fav[0];
                        $favorities['data'][$key]['id_fav'] = $val->id;
                    break;
                    case 'hall':
                        $fav =Hall::select('id','name','pic','created_at as start_date')                            
                            ->where('id','=',$val->id_item)
                            ->get();
                        $favorities['data'][$key] = $fav[0];
                        $favorities['data'][$key]['id_fav'] = $val->id;
                    break;
                }                   
            }
            $favorities['message'] = "Lista de favoritos";            
            return json_encode($favorities);
        }else{
            return $this->errorResponse('Parece que no tienes favoritos', 200);            
        }     
    }

    /**
     * retorna las actividades por realizar a la fecha
     */
    public function getActivitiesEventOnSchedule($eventId){        
        $activities = Activity::select('id', 'name', 'start_date', 'end_date', 'pic')
            //->where('start_date','>','date(now())')
            ->where('event_id',$eventId)
            ->orderByRaw('start_date ')
            ->get();
        return $this->successResponse(['data'=> $activities, 'message'=>'Listado de actividades hoy'], 200);
    }

    /**
     * valida el nombre del dominio asociado a un evento con web app habilitado 
     * @param
     * post path_wep_app = nombre del dominio
     */
    public function validPathEvent(Request $request){        
        $valid = Event::select('id')->where('wa_path_value',$request->path_wep_app)->get();
        //Log::info(var_dump($valid));
        if(count($valid)>0){
            return $valid;            
        }else {
            return $this->errorResponse('No existe evento asociado a este dominio',401);
        }
    }

    /**
     * obtiene el id del último evento registrado
     */
    public function getLastedEventId(){
        $id = Event::select('id')->orderBy('id', 'desc')->first();                
        return $this->successResponse(['data'=> $id, 'message'=>'last id event'], 201);  
    }

    /**
     * retorna datos de los speakers registrados
     */
    public function getSpeakersByEvent($eventId){
        $find = DB::table('speakers as s')
            ->select('s.id as speaker_id',
                's.name as speaker_name',
                's.sort_description as speaker_description',
                's.pic as speaker_photo',
                's.country_id as country',
                'ce.name', 'ce.flag'
                )
                ->join('country_events as ce', 'ce.id', '=', 's.country_id')
                ->join('activity_speakers as as2','s.id', '=', 'as2.speaker_id')
                ->join('activities as a2','a2.id', '=', 'as2.activity_id')
                // ->select('ce.name')
                ->where('a2.event_id', $eventId)
                ->distinct() //TODO Cambios necesatios agregar esta linea para consultar spekers no repetidos 
                ->get();
        return $this->successResponse(['data'=> $find, 'message'=>'list all speaker by event'], 201);  
    }

    public function saveImg($file)
    {       
        if(!$file){
            return;
        }
        try {
            # Storage::disk('local')->put($nameFile,  \File::get($file));
            # Storage::disk('digitalocean')->put($nameFile, \File::get($file));
            $path = Storage::disk('digitalocean')->putFile('uploads', $file, 'public');
            return $path;
        } catch (Exception $e) {
            
            return ' Error al subir el archivo ' . $e;
        }
    }


    /**
     * Crea otro registro de empresa en feria comercial 
     * @param POST con la data
     */
    public function createFair(Request $request){        
        $pic = $this->saveImg($request->logo_company);
        try {                 
            $create = Fair::create([                
                'name_company'=>$request->name_company,
                'description_company'=>$request->description_company,
                'logo_company'=>$pic,
                'contact_company'=>$request->contact_company,
                'id_event'=>$request->id_event
            ]);
        } catch (\Throwable $th) {
            return $this->errorResponse('Error create fair', 500);                   
        }
        return $this->successResponse(['data'=> $create, 'message'=>'Fair Created'], 201);
    }

    /**
     * obtiene listado de empresas para un evento
     * @param id_event id evento a listar 
    */
    public function getCompanyFair($id_event){        
        try{
            $list = Fair::where('id_event','=',$id_event)->get();
        }catch (\Throwable $th) {
            return $this->errorResponse('Error list company fair', 500);                   
        }
        return $this->successResponse(['data'=> $list, 'message'=>'Fair list'], 201);
    }

    /**
     * Borra una company apartir de un id dado
    */
    public function destroyCompany(Request $request)
    {
        $id = $request->id;        
        $res=Fair::where('id',$id)->delete();        
        if($res){
            return $this->successResponse(['data'=> [], 'message'=>'company delete'], 201);
        }else{
            return $this->errorResponse('Error delete', 500);  
        }
    }

    /**
     * retorna la imagen de mapa de un evento
     */
    public function getMapaEvent($id_event){        
        $mapa = Event::select('wa_mapa_value')->where('id',$id_event)->get();                        
        return $this->successResponse(['data'=> $mapa[0]->wa_mapa_value, 'message'=>'last id event'], 201); 
    }

    /** 
     * agrega las imgs banner de web app
    */
    public function addBannersWA(Request $request){
        
        $id = $request->id;
        $bannerOneVal = $request->bannerOne;
        $bannerTwoVal = $request->bannerTwo;
        $bannerOne = $this->saveImg($bannerOneVal);
        $bannerTwo = $this->saveImg($bannerTwoVal);
    
        $valid = DB::table('event_styles')->where('event_id', $id)->first();
        if($valid===null){
            $create = EventStyle::create([
                'event_id'=>$id,
                'wa_banner_one' => $bannerOne,
                'wa_banner_two' => $bannerTwo
            ]);
        }else{
            $create = DB::table('event_styles')->where('event_id', $id)
                ->update(
                    ['wa_banner_one' => $bannerOne,
                    'wa_banner_two' => $bannerTwo ]
                );
        }
        
        return $create;
    }
}
