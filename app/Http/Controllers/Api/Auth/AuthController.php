<?php

namespace App\Http\Controllers\Api\Auth;

use App\Mail\Event\NewRegister;
use App\Traits\response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\User;
use App\Event;
use App\Guest;
use PhpMqtt\Client\Facades\MQTT;
use Validator;
use App\BusinessMarketsRelUsers;
use App\Traits\sendEmail;
use Carbon\Carbon;
use App\EventUser;
use App\Activity;
use App\BusinessMarket;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\formatRegistrationEmail;
use App\Company;
use Spatie\Permission\Models\Role;
use App\Traits\imagesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Firebase\JWT\JWT;


class AuthController extends Controller
{
    use HasRoles, sendEmail, formatRegistrationEmail, imagesTrait;

    public $successStatus = 200;

    public function showAdminUsers()
    {
        $users = User::select('users.*', 'c.name as company', 'r.name as role')
            ->join('model_has_roles as mr', 'mr.model_id', '=', 'users.id')
            ->join('roles as r', 'r.id', '=', 'mr.role_id')
            ->leftJoin('companies as c', 'c.id', '=', 'users.company_id')
            ->orderBy('id', 'desc')
            ->get();

        return $this->showAll($users, 201);
    }    

    /**
     * actualiza el tiempo de validación del token
     */
    public function refreshToken()
    {

        $token = JWTAuth::getToken();

        try {
            $token = JWTAuth::refresh($token);
            return response()->json(['success' => true, 'token' => $token], 200);
        } catch (TokenExpiredException $ex) {
            // We were unable to refresh the token, our user needs to login again
            return response()->json([
                'success' => false, 'message' => 'Need to login again, please (expired)!'
            ]);
        } catch (TokenBlacklistedException $ex) {
            // Blacklisted token
            return response()->json([
                'success' => false, 'message' => 'Need to login again, please (blacklisted)!'
            ], 422);
        }

    }

    /**
     * Invalida el token jwt 
     */
    public function logoutjwt()
    {
        //  $this->validate($request, ['token' => 'required']);
        $token = JWTAuth::getToken();          
        try {
            $token = JWTAuth::invalidate($token);            
            return response()->json([
                'code' => 5, 'success' => true, 'message' => "You have successfully logged out."
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'code' => 6, 'success' => false, 'message' => $e->getMessage() 
            ], 422);
        }

    }

    /**
     * Inicia sesión retorna datos del usuario y token JWT
     */
    
    public function login(Request $request)
    {
        $eventOk = false;
        $rules = [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string',
        ];

        $this->validate($request, $rules);

        $user = User::where('email', $request->email)->first();

        if ($request->eventId) {
            $event = Event::where('id', $request->eventId)->first();
            $eventUser = EventUser::where('user_id', $user->id)->where('event_id',$request->eventId)->first();

            if (!$event || !$user || !$eventUser) {
                return $this->errorResponse('Unauthorized: Access is denied due to invalid credentials. 1', 401);    
            }
            if ($event->password == $request->password) {
                $eventOk = true;                
            } 
        }   

        if($eventOk){
            User::where('email', $request->email)->update(['online' => '1']);
            MQTT::publish('online_users_eventmovil', 'onlines users');
            Auth::login($user);
        }else{            
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {
                Auth::login($user);
            }else{
                return $this->errorResponse('Unauthorized: Access is denied due to invalid credentials. 2', 401);
            }
        }   

        $user = Auth::user();
        //return json_encode($user);exit;
        $roles = $user->getRoleNames();        
        $ver = 0;

        foreach ($roles as $role) {
            if ($role == "Super admin") {
                $ver = 1;
            }
        }
        
        if ($ver == 0) {
            if ($request->business_id) {
                $bm = Role::findOrFail(4);
                $user['roles'] = $bm;
                $businessData = BusinessMarket::find($request->business_id);
            }
            if (isset($businessData) && $businessData->unique_login == 1) {
                $this->removeAccessToeken($user);
            }
        }
        
        try{
            $credentials = $request->only('email','password');  
            /* JWTAuth::attempt($credentials, ['exp' => Carbon\Carbon::now()->addDays(7)->timestamp]) */         
            if(!$token = JWTAuth::attempt($credentials)){
                return response()->json(['error' => 'invalid_credentials 3'],401);
            }
        }catch(JWTExeption $e){
            return response()->json(['error' => 'could_not_create_token 4',500]);
        }                      
        $user['token'] = $token;
        return $this->successResponse($user, 200);        
    }

    /**
     * Inicia sesión para la web app retorna datos del usuario y token JWT
     * necesario correo y id de evento asociado
     */
    
    public function loginBorrarDespues(Request $request)
    {                        
        $rules = [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string',
        ];

        $this->validate($request, $rules);
        $user = User::select('id','name','lastname','email','model_id','company_id')->where('email', $request->email)->get();        
        Log::info('aqui va');
        if(count($user)>0){
            $eventUser = DB::table('event_users')
                ->select('id')
                ->where('user_id','=',$user[0]->id)
                ->where('event_id','=',$request->eventId)
                ->get();
                Log::info('aqui va 2');
            Log::info($user[0]->id);
            if(count($eventUser)>0){
                try{

                    $credentials = $request->only('email');            
                    $time = time();
                    $key = 'webappeventmovil2021';
                    //var_dump($user[0]->lastname);
                    $name = $user[0]->name;// + ' ' + $user[0]->lastname;
                    $token = array(
                        'iat' => $time, // Tiempo que inició el token
                        'exp' => $time + (60*60), // Tiempo que expirará el token (+1 hora)
                        'data' => [ // información del usuario
                            'id' => $user[0]->id,
                            'name' => $name
                        ]
                    );
                    
                    $jwt = JWT::encode($token, $key);                    
                }catch(JWTExeption $e){
                    return response()->json(['error' => 'could_not_create_token 4',500]);
                }               
            }else{
                return response()->json(['error' => 'invalid_user 3'],401);
            }
        }               
        $user['token'] = $jwt;
        return $this->successResponse($user, 200);        
    }



    public function removeAccessToeken($user)
    {
        $carbon = new \Carbon\Carbon();
        $date = $carbon->format('Y-m-d');

        $tokens = DB::table('oauth_access_tokens')->select('revoked', 'id')
            ->where('user_id', $user->id)
            //->where('created_at','>',$date.' 00:00:00')
            ->orderBy('created_at', 'desc')
            ->first();

        //var_dump($tokens);

        if ($tokens) {
            $res = DB::table('oauth_access_tokens')
                ->where('id', $tokens->id)
                ->update(['revoked' => '1']);
            //dump($res);

        }

        return true;
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
    public function loginToken(Request $request)
    {

        $rules = ['token' => 'required|exists:event_users,token'];

        $this->validate($request, $rules);

        $eventUser = EventUser::where('token', $request->token)->first();

        $user = User::findOrFail($eventUser->user_id);

        if (Auth::attempt(['email' => $user->email, 'password' => $user->email], true)) {

            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;
            $user['token'] = $token;
            $user['event_id'] = $eventUser->event_id;

            $activity = Activity::where('event_id', $user->event_id)->first();

            $user['activity_id'] = $activity->id;

            $role = $user->getRoleNames();


            return $this->successResponse($user, 200);
        } else {
            return $this->errorResponse('Unauthorized: Access is denied due to invalid credentials.', 401);
        }
    }
    public function validateUser($email)
    {

        $user = User::where('email', $email)->first();
        Log::info($user);
        if (isset($user->id)) {
            if ($user->actived) {
                $user->status = 'registro_exitoso';
                return $this->showOne($user);
            } else {
                $user->status = 'completar_registro';
                return $this->showOne($user);
            }
            // return $this->showOne($user);
        } else {
            return $this->showOne(false);
        }
    }

    public function sendEmailEvent($request)
    {
        $event = Event::where('id', $request->event)->first();

        $message1 = str_replace("*u", $request->name, $event->message_email);
        $message = str_replace("*e", $event->name, $message1);

//        $templete = "
//        <div style='background-color:" . $event->first_color . "; width:50%; border-radius:10px;'>
//            <h4 style='color:" . $event->second_color . "; text-align: center; font-size: 30px; padding-top: 5%;'>" . $event->name . "</h4>
//            <img style='width:80%; padding-left:10%;'  src='" . env('IMAGE_URL') . $event->pic_banner . "'/>
//            <p style='color:" . $event->third_color . "; margin: 5%; padding-bottom:10%; font-size:15px;'>" . $message . "</p>
//            <table style='color:#f7f7f7;margin:5%;padding-bottom: 3%;font-size:15px; width:90%; text-align:center;'>
//                <tbody><tr>
//                    <td style='color:" . $event->second_color . "'><b>Inicia/</b></td>
//                    <td style='color:" . $event->second_color . "'><b>Termina/</b></td>
//                </tr>
//                <tr>
//                    <td style='font-size:15px;'>" . $event->start_date . "</td>
//                    <td>" . $event->end_date . "</td>
//                </tr>
//                <tr>
//                    <td colspan='2' style='padding-top: 5%;'></td>
//                </tr>
//            </tbody></table>
//            <a target='_blank'  href='" . env('FRONT') . "#/login?eventId=" . $event->id . "'>
//            <button style='margin-left: 35%; background-color:" . $event->second_color . "; color:" . $event->third_color . ";margin-bottom: 10%;
//            width: 30%; height: 40px; border-radius: 5px; border-color: #9e3dff;'>Ir al sitio</button><a>
//        </div>";
//
//        $email = $this->sendEmail($request->email, $event->subject_email, $templete);
        dd('holas');
        Mail::to($request->email)->send(new NewRegister($request->name, $request->last_name, $event->name));
    }


    public function register(Request $request)
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required|string',
            'name'     => 'required',
            'lastname' => 'required',
        ];

        $this->validate($request, $rules);

        $toCreate = $request->all();
        $toCreate['password'] = Hash::make($request->password);

        //Si existe usuario
        $userVer = User::where('email', $request->email)->first();
        if (!$userVer) {
            /* if (isset($toCreate['pic']))
                $toCreate['pic'] = $this->convertSaveImageB64($toCreate['pic']); */
            $user = User::create($toCreate);
        } else {
            $user = $userVer;
            $ifUpdate = true;
        }
        if (isset($request->company)) {
            $company = Company::where('email', $request->email)->first();
            if (!$company)
                $company = Company::create([
                    'name'      => $request->company,
                    'email'     => $request->email,
                    'phone'     => $request->phone,
                    'address'   => 'Sin dirección',
                ]);

            $user->company_id = $company->id;
            $user->save();
        }
        if (isset($request->rol)) {
            $user->assignRole($request->rol);
            if ($request->rol == "staff") {
                $user->password = bcrypt($request->password);
                $user->save();
            }
        } else if (isset($request->event)) {

            $user->assignRole('guest');
            //$this->sendEmailEvent($request);

        } else if (isset($request->business_id)) {

            if (!$user->hasRole('business market'))
                $user->assignRole('business market');

            //If user has been create and asocciate already
            $businessVer = BusinessMarketsRelUsers::where('user_id', $user->id)->where('business_id', $request->business_id)->first();
            if ($businessVer) {
                return $this->successResponse(['message' => 'User register already'], 200);
            }
            $businessData = BusinessMarket::find($request->business_id);

            if (isset($ifUpdate)) {
                if (isset($toCreate['pic']))
                    $toCreate['pic'] = $this->convertSaveImageB64($toCreate['pic']);
                if (isset($businessData["segmentation_actived"])) {
                    if ($businessData["segmentation_actived"] == 0) {
                        if ($user->user_type == "claimant" || $user->user_type == "offerer") {
                            $toCreate["user_type"] = $user->user_type;
                        } else {
                            $toCreate["user_type"] = "not_apply";
                        }
                    }
                }
                $user->update($toCreate);
            }

            $business = new BusinessMarketsRelUsers;
            $business->user_id = $user->id;
            $business->business_id = $request->business_id;
            $business->relation = "participant";
            $business->save();

            // $businessData = BusinessMarket::find($request->business_id);
            $format = $this->formatRegistrationEmail($user, $businessData);
            $cadena = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, htmlentities($format['template']));
            $cadena = html_entity_decode($cadena);
            $this->sendEmail($user->email, $format['subject'], $cadena, true, 'registered', "bm", $request->business_id);

            try {
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {

                    $user = Auth::user();
                    $token = $user->createToken('MyApp')->accessToken;
                    $user['token'] = $token;
                    $user->getRoleNames();

                    return $this->successResponse($user, 200);
                } else {
                    return $this->errorResponse('Unauthorized: Access is denied due to invalid credentials.', 401);
                }
            } catch (\Exception $e) {
                return $this->errorResponse("Error", 500);
            }
        }

        return $this->successResponse(['data' => $user, 'message' => 'User created'], 201);
    }


    public function emailRestorePw(Request $request)
    {

        $user = User::where("email", $request->email)->first();
        if (isset($user->id)) {
            $restore_token = Str::random(10);
            $verifyToken = User::where('restore_token', $restore_token)->first();

            while (isset($verifyToken->restore_token)) {
                $restore_token = Str::random(10);
                $verifyToken = User::select('restore_token')->pluck('restore_token')
                    ->where('restore_token', $restore_token)->first();
            }
            $user->restore_token = $restore_token;
            $user->save();
            if (!isset($request->action))
                $url = env('FRONT') . "#/RestorePassword/" . $restore_token;
            else
                $url = env('FRONT') . "#/RestorePassword/" . $restore_token . "/" . $request->action . "/" . $request->id;

            $templete = "<h4>Restablecimiento de contraseña</h4>
            <p>Ingresa al siguiente link para restablecer tu contraseña </p>" . $url;
            $email = $this->sendEmail($user->email, "Restablecimiento de contraseña", $templete, true, "restore-pass", "user", $user->id);
        }
    }
    public function validateTokenRestore($restore_token)
    {

        $user = User::where('restore_token', $restore_token)->first();

        if (!isset($user->id)) return $this->errorResponse("Error", 500);

        return $this->showOne($user);
    }

    public function updatePassword(Request $request, User $user)
    {

        $rules = ['password' => 'required|string'];

        $this->validate($request, $rules);
        Log::info('ingreso password..');
        Log::info($request->all);
        if (isset($request->name)) {
            $user->name = $request->name;
            $user->lastname = $request->lastname;
            $user->actived = 1;
        }

        $user->password = Hash::make($request->password);
        $user->restore_token = null;
        $user->save();


        return $this->successResponse($user, 200);
    }

    public function updateUser(Request $request)
    {   
        $user= User::where('email', $request->email)->first();

        if(isset($user->id)){
            if($user->actived==0){
                $userupdate = User::find($user->id);
                $userupdate->name = $request->name;
                $userupdate->lastname = $request->lastname;
                $userupdate->actived = 1;
                $userupdate->save();
            }
        }
    }

    public function inactiveOnlineUser($id){
        $user = User::where('id', $id)->update(['online' => '0']);
        MQTT::publish('online_users_eventmovil', 'onlines users');
        return response()->json('Se actualizo el estado online correctamente', 201);
    }
    public function activeOnlineUser($id){
        $user = User::where('id', $id)->update(['online' => '1']);
        MQTT::publish('online_users_eventmovil', 'onlines users');
        return response()->json('Se actualizo el estado online correctamente', 201);
    }
}
