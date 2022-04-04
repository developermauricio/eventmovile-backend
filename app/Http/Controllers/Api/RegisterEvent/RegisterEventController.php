<?php

namespace App\Http\Controllers\Api\RegisterEvent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\RegisterEvent;
use App\User;
use App\Event;
use App\EventUser;
use App\Activity;
use App\DataRegister;
use App\EventInvitation;
use App\Hall;
use App\UrlInvitation;
use App\Traits\sendEmail;
use App\Traits\formatRegistrationEmail;
use Firebase\JWT\JWT;

class RegisterEventController extends Controller
{
    use sendEmail, formatRegistrationEmail;
    public function index() {

        $registerEvent = RegisterEvent::all();

        return $this->showAll($registerEvent,200);
    }

    public function show(RegisterEvent $registerEvent)
    {
    
        return $this->showOne($registerEvent);
    }

    public function showFieldsEvent($event)
    {
        $registerEvent = RegisterEvent::where('event_id', $event)->orderBy('id', 'desc')->get();

        return $this->showAll($registerEvent);
    }

    public function store(Request $request){

        $rules = [
            'event_id'   => 'required|exists:events,id',
            'name'       => 'required',
            'type'       => 'required' 
        ];

        $this->validate($request, $rules);

        $registerEvent = RegisterEvent::create([
            'event_id'  => $request->event_id,
            'name'      => $request->name,
            'type'      => $request->type,
            'options'   => $request->options,    
            'required'  => $request->required
        ]);

        return $this->successResponse(['data'=> $registerEvent, 'message'=>'Poll Created'], 201);
    }

    public function update(Request $request, RegisterEvent $registerEvent){
        
        $rules = [
            'event_id'   => 'required|exists:events,id',
            'name'       => 'required',
            'type'       => 'required' 
        ];


        $this->validate($request, $rules);

                
        $registerEvent->fill($request->all());
        
        if ($registerEvent->isClean()) {
            return $this->successResponse(['data' => $registerEvent, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $registerEvent->save();

        return $this->successResponse(['data' => $registerEvent, 'message' => 'Field Updated'],201);
    }

    public function destroy(RegisterEvent $registerEvent)
    {
        $registerEvent->delete();   
        return $this->successResponse(['data' => $registerEvent, 'message' => 'Field Deleted'], 201);
    }

    public function importUser(Request $request){
        $hall= null;
        $message_email = '{"message_email_1":"Registro exito","message_email_2":"Virtual / Presencial","message_email_3":"Ir al sitio del evento","message_email_4":"Salas","message_email_5":"Inicia/", "message_email_6":"Termina/", "message_email_7":"Añadir a calendario"}';
        //data user
        $user = $request->data;
        //Find event
        $event = Event::with('style')->findOrFail($request->event_id);
        // Get Halls of the event
        if(isset($request->event_id)){
            $hall=Hall::where([['event_id','=',$request->event_id],['hall_type_id','=', 2]])->get();
        }
        //verify message for email
        if(empty($request->message_email_1)==false && empty($request->message_email_2)==false && empty($request->message_email_3)==false && empty($request->message_email_4)==false && empty($request->message_email_5)==false && empty($request->message_email_6)==false && empty($request->message_email_7)==false){
            $message_email = '{"message_email_1":"'.$request->message_email_1.'","message_email_2":"'.$request->message_email_2.'","message_email_3":"'.$request->message_email_3.'","message_email_4":"'.$request->message_email_4.'","message_email_5":"'.$request->message_email_5.'","message_email_6":"'.$request->message_email_6.'","message_email_7":"'.$request->message_email_7.'"}';
        }
        //extrac presencial activity to qr code
        $activities = Activity::where('event_id', $request->event_id)->get();
        $arrayAct = Array();
        $qr = false;
        foreach($activities as $act){
            $activity = DB::table('activities as a')
            ->select('a.*', 'm.name as mode_name', 'm.id as mode_id')
            ->join('mode_activities as m', 'm.id', '=', 'a.mode_id')
            ->where('a.id', $act->id)
            ->first();
            if($activity->mode_name == "Presencial" || $activity->mode_id == 1 ){
               $qr = true;
            }
            array_push($arrayAct, $act->id);
        }
        $i = 1;
        // validation static fields
        Validator::make($user, [
            'name' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
        ])->validate();
        // Validations dynamic fields
        $fields = RegisterEvent::where('event_id', $event->id)->get();

        foreach($fields as $field){
            if($field->type == 'text') $typeField = 'string';
            if($field->type == 'number') $typeField = 'integer';
            if($field->type == 'select') $typeField = 'string';
            if($field->type == 'textarea') $typeField = 'string';
            $required = ($field->required == true)? 'required|' : '';

            Validator::make($user,[
                $field->name => $required.$typeField,
            ])->validate();

        };
        
        DB::beginTransaction();
        try{
            $userCreate = User::where('email', $user['email'])->first();
            if(!isset($userCreate->id)){
                $token= Str::random(10);
                $userCreate = User::create([
                    "email" => $user['email'],
                    "name" => $user['name'],
                    "lastname" => $user['lastname'],
                    "password" => Hash::make($token),
                ]);
            }else{
                $token = 0;
            }

            $colsDataReg = array_keys($user);
            foreach($colsDataReg as $col){
                if($col != "name" && $col != "lastname" && $col != "email"){
                    $registerEvent = RegisterEvent::where('event_id', $request->event_id)->where('name', $col)->first();
                    $validate = DataRegister::where('register_id', $registerEvent->id)->where('user_id', $userCreate->id)->first();
                    if(!$validate){
                        $dataRegister = DataRegister::create([
                            'user_id'  => $userCreate->id,
                            'register_id' => $registerEvent->id,
                            'value'      => $user[$col]
                        ]);
                    }
                    
                }
            }

            if(!$userCreate->hasRole('guest'))
                $userCreate->assignRole('guest');

            $validate = EventUser::where('user_id', $userCreate->id)->where('event_id', $request->event_id)->first();
            if(!$validate){
                $eventUser = EventUser::create([
                    "user_id" => $userCreate->id,
                    "event_id" => $request->event_id,
                ]);
            }else{
                $e[0] = 'El usuario ya fue registrado a este evento';
                return $this->errorResponse(['error'=>$e], 422);
            }

            $validate = EventInvitation::where("event_id", $request->event_id)->where("email", $userCreate->email)->first();
            if(!$validate){
                $eventInv = EventInvitation::create([
                    "event_id" => $request->event_id,
                    "email" => $userCreate->email,
                    "quantity" => 1,
                    "name" => $userCreate->name." ".$userCreate->lastname,   
                    "activities" => json_encode($arrayAct)
                ]);

                $urlInv = UrlInvitation::create([
                    "url" => "Url",
                    "token" => "123456",
                    "user_id" => $userCreate->id,
                    "invitation_id" => $eventInv->id,
                    "actived" => 1,
                ]);

                if($qr){
                    //TODO: modificar la generacion del qr, con los datos del usuario y el evento
                    $qr = $urlInv->id;
                }
                if($token == 0){
                    $tokenTxt = "";
                }else{
                    $tokenTxt =  "tu contreseña de ingreso es ".$token;
                }
                $message1 = str_replace("*u", $userCreate->name, $event->message_email);
                $message = str_replace("*e", $event->name, $message1);
    
                $message = $message." ".$tokenTxt;
                            
                $format = $this-> formatEmailEvent($message, $event, $qr, $eventUser->id, $hall, $message_email);
    
                $email = $this->sendEmail($userCreate->email, $event->subject_email, "'".$format['template']."'");
    
                // $email = $this->sendEmail($event->email, $event->subject_email, $templete);
            }else{
                $e[0] = 'El usuario ya fue registrado a este evento y tiene invitación';
                return $this->errorResponse(['error'=>$e], 422);
            }
           
            DB::commit();
            $i++;
        }catch (Exception $e) {
            DB::rollback();
            return $this->errorResponse("Error en el registro número , corrija el error y vuelva a cargar el archivo".$e->getMessage(), 500);
        }
 
        return $this->successResponse(['data' => $userCreate, 'message' => 'Usuario creado correctamente'], 201);

    }
    public function importUserInv(Request $request){
        
        $user = $request->data;

        $event = Event::where('id',$request->event_id)->with('style')->first(); 
        

        $activities = Activity::where('event_id', $request->event_id)->get();
        $arrayAct = Array();
        foreach($activities as $act){
            array_push($arrayAct, $act->id);
        }
        $i = 1;
        
        DB::beginTransaction();
        try{
            
            $eventInv = EventInvitation::create([
                "event_id" => $request->event_id,
                "email" => $user['email'],
                "quantity" => 1,
                "name" => $user['name'],
                "activities" => json_encode($arrayAct)
            ]);
            
            
            $token = Str::random(5);

            $verifyToken = UrlInvitation::select('token')
                ->where('token', $token)->first();

            while(isset($verifyToken->token)){
                $token= Str::random(5);
                $verifyToken = UrlInvitation::select('token')->pluck('token')
                    ->where('token', $token)->first();
            }
            
            $urlInvitation = UrlInvitation::create([
                'url' => 'Url',
                'token'=> $token,
                'user_id' => null,
                'invitation_id'=>$eventInv->id,
                'actived' => false,
            ]);

            $tokens ='<br>'.env('FRONT').'#/Register-Event-Token?token='.$token;
            $templete = "<p>Tienes una invitación a un evento (copia y pega la url en el navegador): </p>".$tokens;

            $templete = view('events.standar', ["event" => $event, "message" => $templete]);
            $templete = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, htmlentities($templete));
            $templete = html_entity_decode($templete);

            $email = $this->sendEmail($eventInv->email, "Invitacion a ". $event->name, $templete, true, "importInvitations", "EventInvitation", $eventInv->id);
            //$email = $this->sendEmail($eventInv->email, 'HeartOnline', $templete);
                            
            //$email = $this->sendEmail($event->email, $event->subject_email, $templete);
            DB::commit();
            $i++;
        }catch (Exception $e) {
            DB::rollback();
            return $this->errorResponse("Error en el registro número  corrija el error y vuelva a cargar el archivo".$e->getMessage(), 500);
        }
            

        
        
        return $this->successResponse(['data' => $eventInv, 'message' => 'Invitación  creada correctamente'], 201);


    }
    
    /**
     * genera la información de un usuario para un evento y retorna token con data encriptada
     * @param
     * Request POST con:
     * id_user = id de usuario invitado 
     * id_event = id de evento
     * @return
     * token || error generando el token 
     */
    public function createTokenToExternalEvent(Request $request){        
        $userId = $request->id_user;
        $eventId = $request->id_event; 
        //validar si el usuario lleno todos los campos

        try{              
            $valid = DB::table('data_registers as dr')
                ->where('dr.user_id','=', $userId)
                ->get();
            if(count($valid)>0){
                $data = DB::table('data_registers as dr')          
                    ->select('u.id as userId', 
                        'u.name as userName', 
                        'u.email as userEmail', 
                        're.id as fieldId', 
                        're.name as fieldName', 
                        'dr.id as answerId', 
                        'dr.value as answerValue')  
                    ->join('register_events as re', 're.id', '=', 'dr.register_id')                
                    ->join('users as u', 'u.id', '=', 'dr.user_id')                            
                    ->where('re.event_id','=', $eventId)
                    ->where('dr.user_id','=', $userId)
                    ->get()->toArray();            
                //construcción del array
                $arrayData = [];            
                $arrayData['userId'] = $data[0]->userId;
                $arrayData['user'] = $data[0]->userName;
                $arrayData['email'] = $data[0]->userEmail;
            
                foreach($data as $fields){
                    //campos que pueden ser solicitados
                    //$arrayData['fieldId'] = $fields->fieldId;
                    //$arrayData['fieldName'] = $fields->fieldName;
                    //$arrayData['answerId'] = $fields->answerId;
                    $arrayData[$fields->fieldName] = $fields->answerValue;            
                }
            }else{
                return $this->errorResponse('El usuario no tiene respuestas que mostrar', 500);
            }
        }catch(Exception $e){
            return $this->errorResponse('Parce que hubo un error recopilando la información' . $e, 500);
        }    
        //return json_encode($arrayData); 

        //encriptando la información
        $time = time();
        $key = '4pp3v3ntm0v1lC0m'.$eventId.'*f2ec0h421'; //firma oficial
        //$key = 'app.eventmovil.com';
        $token = array(
            'iat' => $time, // Tiempo que inició el token
            'exp' => $time + (60*60), // Tiempo que expirará el token (+1 hora)
            'data' => $arrayData //info uer
        );

        $jwt = JWT::encode($token, $key);
        return $jwt;
        //para decodificar la información 
        $data = JWT::decode($jwt, $key, array('HS256'));
    }
}
