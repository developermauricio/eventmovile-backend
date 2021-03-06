<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'v1', 'middleware' => ['jwt.auth']],function(){    
//Route::group(['prefix' => 'v1'],function(){    
    /**begin web app */    
    //begin administration
    Route::get('lastedIdEvent/','Api\Event\EventController@getLastedEventId');
    Route::post('addBanners','Api\Event\EventController@addBannersWA');
    // begin agenda
    Route::get('showSchedule/{event}','Api\Event\EventController@scheduleGeneral');
    Route::get('searchInSchedule/{event}/{q}','Api\Event\EventController@searchInSchedule');
    Route::post('addScheduleFavorites','Api\Event\EventController@addToFavoriteSchedule');
    Route::put('remveScheduleFavorites','Api\Event\EventController@removeToFavoriteSchedule');
    Route::get('listScheduleFavorites/{user}/{event}','Api\Event\EventController@scheduleFavorites');    
    Route::get('listActivitiesEventOnSchedule/{event}','Api\Event\EventController@getActivitiesEventOnSchedule');  
    //end agenda
    // begin  album
    Route::get('album/{event}','Api\WebApp\Album\AlbumController@getIdAlbum');
    Route::get('listPhotos/{event}/{user}','Api\WebApp\Album\AlbumController@getPhotos');  
    Route::post('uploadPhoto','Api\WebApp\Album\AlbumController@createPhoto');      
    Route::post('interaction','Api\WebApp\Album\AlbumController@interactionPhoto');      
    Route::put('delPhoto','Api\WebApp\Album\AlbumController@deletePhoto');          
    //end album

    //begin sondeos
    Route::get('getIdsActivitiesByEvent/{event}','Api\Activity\ActivityController@getIdsActivitiesByEvent');
    //end sondeos
    //begin speakers
    Route::get('getSpeakers/{event}','Api\Event\EventController@getSpeakersByEvent');  
    //end speakers

    //begin feria comercial
    Route::get('get-companies-feria-comercial/{event}', 'Api\Event\EventController@getCompaniesFair');
    Route::post('createCompany','Api\Event\EventController@createFair');    
    Route::get('getCompanyFair/{event}','Api\Event\EventController@getCompanyFair');  
    Route::put('removeCompany','Api\Event\EventController@destroyCompany');    
    //end feria comercial

    //begin mapa
    Route::get('getMapa/{event}','Api\Event\EventController@getMapaEvent');  
    //end mapa  app/Http/Controllers/Api/WebApp/Attendance/AttendanceController.php

    Route::get('get-user-register/{idEvent}', 'Api\WebApp\Attendance\AttendanceController@getUserRegisterEvent');
    Route::get('get-data-user/{user_id}', 'Api\WebApp\Attendance\AttendanceController@getDataUserForID');
    Route::put('update-data-user', 'Api\WebApp\Attendance\AttendanceController@updateInfoUser');
    Route::put('update-data-register', 'Api\WebApp\Attendance\AttendanceController@updateDataRegister');
    Route::get('get-data-register/{user}/{event}', 'Api\WebApp\Attendance\AttendanceController@getDataRegister');
    Route::get('get-all-attendance/{event_id}', 'Api\WebApp\Attendance\AttendanceController@getAllAttendance');
    Route::post('register-attendance','Api\WebApp\Attendance\AttendanceController@createRegisterAttendance');   
    
    Route::post('create-question-event','Api\WebApp\Event\QuestionEventController@createQuestionEvent');   
    Route::get('get-question-event/{event}/{user}', 'Api\WebApp\Event\QuestionEventController@getQuestionForEvent');

    /** end web app */    

    
    Route::post('auth/register-update', 'Api\Auth\AuthController@updateUser')->name('register');
    
    Route::post('auth/register-bm', 'Api\Auth\AuthController@registerBM')->name('register-bm');

    Route::post('verifyUrl', 'Api\Event\UrlInvitationController@verifyUrl');

    Route::get('validateTokenStaff/{token}', 'Api\Staff\StaffAccessController@validateToken');
    
    Route::get('showToken/{token}','Api\Event\UrlInvitationController@showToken');
        
    
    Route::get('showHallsInLineTime/{event}/{user_id}','Api\Hall\HallController@showHallsInLineTime');    
    
    
    
    
    
    Route::get('activityMessagesExt/{activity}','Api\Activity\ActitivyChatController@activityMessages');
    Route::post('sendMessage', 'Api\Activity\ActitivyChatController@activityMessages');
    Route::resource('questionActivities','Api\Activity\QuestionActivityController');
    Route::get('questionsForActivity/{activity}/{user}','Api\Activity\QuestionActivityController@questionsForActivity');
    Route::put('sendAnswer/{questionActivity}', 'Api\Activity\QuestionActivityController@questionResponse');
    
    Route::post('loginCxsummit', 'Api\Auth\AuthController@loginToken');
    Route::post('emailRestorePw', 'Api\Auth\AuthController@emailRestorePw');
    Route::put('restorePw/{user}', 'Api\Auth\AuthController@updatePassword');
    Route::get('validateTokenRestore/{restore_token}', 'Api\Auth\AuthController@validateTokenRestore');
    Route::get('externalFont/{font}','Api\Font\FontController@show');
    
    
    Route::get('qr-code/{code}', function ($code) {
        $qr = QRCode::text($code)->setSize(15)->setOutfile('../storage/storage/qr-code'.$code.'.png')->png();
        $oldname = 'qr-code'.$code.'.png';
       
       // Storage::disk('public')->download($oldname);
        return  $qr;
    });

    Route::get('authorization-activity','Api\Activity\ActivityController@verifyAuthorization');
    
    Route::get('storage/{filename}', function ($filename)
    {
        $path = storage_path('storage/' . $filename);

        //return var_dump($path);
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    });

    Route::get('storageTracking/{filename}/{id}', function ($filename, $id)
    {
        $path = storage_path('storage/' . $filename);

        $dataUser = App\EventUser::find($id);
       
        if($dataUser){
            $trackValidate = App\EmailTracking::where('event_id', $dataUser->event_id)->where('user_id', $dataUser->user_id)->first();
            if(!$trackValidate){
                $tracking = App\EmailTracking::create([
                    "event_id" => $dataUser->event_id,
                    "user_id" => $dataUser->user_id,
                    "actived" => true
                ]);
            }
        }
        

        

        //return var_dump($path);
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }); 


    Route::group(['middleware' => ['auth:api','permission']], function() {

        //For Assing Permissions to role
        Route::post('role-permissions/{role}/{permissions}','Api\RolePermissions\RolePermissionsController@store');
        //For Delete permissions to role
        Route::delete('role-permissions/{role}/{permissions}','Api\RolePermissions\RolePermissionsController@destroy');

        Route::post('logout', 'Api\Auth\AuthController@logout');
        Route::resource('users','Api\User\UserController');
        Route::resource('roles','Api\Role\RoleController');
        Route::resource('permissions','Api\Permission\PermissionController');
        Route::resource('events','Api\Event\EventController');
        Route::resource('activities','Api\Activity\ActivityController');
        Route::get('activities-event/{id}','Api\Activity\ActivityController@activitiesEvent')->middleware('permission:activities.index');
        Route::get('activities-speakers/{id}','Api\Activity\ActivitySpeakerController@showActivity')->middleware('permission:activities.show');
        
        Route::resource('eventInvitations','Api\Event\EventInvitationController');
        Route::get('invitations/{event}','Api\Event\EventInvitationController@showInvitations')->middleware('permission:documents.show');
        Route::resource('documents','Api\Document\DocumentController');
        Route::get('modelDocuements/{model}/{modelId}','Api\Document\DocumentController@showModelDocuements')->middleware('permission:documents.show');
        Route::get('usersForEvent/{event}','Api\Event\EventController@usersForEvent')->middleware('permission:events.show');
        Route::get('usersEvent/{event}','Api\Event\EventController@usersEvent')->middleware('permission:events.show');
        
        Route::resource('urlInvitations','Api\Event\UrlInvitationController');
        Route::get('activitiesInvitation/{event}','Api\Event\UrlInvitationController@showActivitiesEvent')->middleware('permission:urlInvitations.show');
        Route::get('reportEventUsers/{event}','Api\Event\UrlInvitationController@eventUsers')->middleware('permission:urlInvitations.show');
        Route::resource('guests','Api\Guest\GuestController');
        Route::resource('typesActivities','Api\Activity\TypeActivityController');
        Route::resource('speakers','Api\Speaker\SpeakerController');
        Route::resource('activitySpeakers','Api\Activity\ActivitySpeakerController');
        Route::resource('modeActivities','Api\Activity\ModeActivityController');
        Route::resource('pollQuestions','Api\Poll\PollQuestionController');
        Route::get('pollQuestionsEvent/{id}','Api\Poll\PollQuestionController@showQuestionsEvent')->middleware('permission:pollQuestions.show');
        
        Route::resource('pollAnswers','Api\Poll\PollAnswerController');
        Route::get('exportPoll/{event_id}','Api\Poll\PollAnswerController@exportPoll')->middleware('permission:pollAnswers.show');
        Route::resource('typeQuestions','Api\Poll\TypeQuestionController');
        Route::post('postionQuestionPoll','Api\Poll\PollQuestionController@updatePosition')->middleware('permission:pollQuestions.update');
       
        
        /* Sondeo */
        Route::resource('probe','Api\Probe\ProbeController');
        Route::get('probe-questions-activity/{id}','Api\Probe\ProbeController@showProbes')->middleware('permission:pollQuestions.show');
        Route::resource('probe-questions','Api\Probe\ProbeQuestionController')->middleware('permission:pollQuestions');
        Route::get('probe-questions-probe/{id}','Api\Probe\ProbeQuestionController@showQuestions')->middleware('permission:pollQuestions.show');
        
        Route::post('probe-questions-position','Api\Probe\ProbeQuestionController@updatePosition')->middleware('permission:pollQuestions.update');
        
        //Answers
        Route::get('probe-answers','Api\Probe\ProbeAnswerController@index')->middleware('permission:pollAnswers.index');
        Route::get('probe-answers/{id}','Api\Probe\ProbeAnswerController@show')->middleware('permission:pollAnswers.show');
        Route::post('probe-answers','Api\Probe\ProbeAnswerController@store')->middleware('permission:pollAnswers.store');
        Route::put('probe-answers','Api\Probe\ProbeAnswerController@update')->middleware('permission:pollAnswers.update');
        Route::delete('probe-answers','Api\Probe\ProbeAnswerController@destroy')->middleware('permission:pollAnswers.destroy');
        

        Route::resource('product','Api\Product\ProductController');
        Route::resource('company','Api\Company\CompanyController');
        Route::get('products-by-id/{id}','Api\Product\ProductController@indexById');

        //Route::resource('business-market','Api\BusinessMarket\BusinessMarketController');
        Route::get('business-market','Api\BusinessMarket\BusinessMarketController@index');
        Route::get('bm-report/{business}','Api\BusinessMarket\BusinessMarketController@report');
        Route::post('business-market','Api\BusinessMarket\BusinessMarketController@store');
        Route::put('business-market/{id}','Api\BusinessMarket\BusinessMarketController@update');
        Route::delete('business-market/{id}','Api\BusinessMarket\BusinessMarketController@destroy');

        Route::resource('business-market-rel-user','Api\BusinessMarket\BusinessMarketsRelUsersController');
        Route::get('business-market-user','Api\BusinessMarket\BusinessMarketUserController@index');
        Route::post('business-market-user','Api\BusinessMarket\BusinessMarketUserController@store');
        Route::get('business-market-user/{user}/{bm?}','Api\BusinessMarket\BusinessMarketUserController@show');
        Route::put('business-market-user/{id}','Api\BusinessMarket\BusinessMarketUserController@update');
        Route::delete('business-market-user','Api\BusinessMarket\BusinessMarketUserController@update@destroy');
        Route::get('business-market-user-by-id/{id}','Api\BusinessMarket\BusinessMarketUserController@indexByBusinnesMarket');

        Route::get('deparments', 'Api\City\CitiesController@deparments');
        Route::get('citys-by-deparment/{id}', 'Api\City\CitiesController@citiesByDeparment');

        Route::resource('meeting','Api\Meeting\MeetingController');
        Route::get('meeting-by-user/{id}/{bm_id}/{acceptance?}','Api\Meeting\MeetingController@invitations');
        Route::resource('meeting-rel-users','Api\Meeting\MeetingRelUsersController');
        Route::get('companyToMeet/{meet}','Api\Meeting\MeetingRelUsersController@partnerCompanies')->middleware('permission:meetChat.show');

        Route::get('schedule/{id}/{idBM}/{optional?}','Api\Meeting\MeetingController@schedule');
        Route::get('next-meeting','Api\Meeting\MeetingController@nextMeeting')->middleware('permission:schedule.show');
 
        Route::resource('halls','Api\Hall\HallController');
        Route::resource('habeasData','Api\HabeasData\HabeasDataController');

        Route::resource('registerEvent','Api\RegisterEvent\RegisterEventController');
        Route::get('fieldsEvent/{event}','Api\RegisterEvent\RegisterEventController@showFieldsEvent')->middleware('permission:registerEvent.show');
        
        Route::resource('tickets','Api\Ticket\TicketController');
        Route::get('ticketsEvent/{event}','Api\Ticket\TicketController@showFieldsEvent')->middleware('permission:tickets.show');

        Route::resource('dataRegisters','Api\RegisterEvent\DataRegisterController');
        Route::get('dataRegisterUser/{user}/{event}','Api\RegisterEvent\DataRegisterController@showDataRegister');

        Route::resource('activityChat','Api\Activity\ActitivyChatController');
        Route::post('eventChat','Api\Event\EventController@eventChatController');
        Route::get('eventChat/{event_id}','Api\Event\EventController@getChatMessagesEvent');
        Route::get('userVip/{user_id}','Api\User\UserController@verifyUserVIP');
        Route::get('activityMessages/{activity}','Api\Activity\ActitivyChatController@activityMessages')->middleware('permission:activityChat.show');
        Route::resource('meetChat','Api\Meeting\MeetingChatController');
        Route::get('meetMessages/{meet}','Api\Meeting\MeetingChatController@meetMessages')->middleware('permission:meetChat.show');

        Route::get('participantsActivity/{eventId}/{activityId}/{filter?}','Api\Activity\ActivityController@showParticipants')->middleware('permission:activities.show');


        Route::get('feedback-question','Api\Feedback\FeedbackQuestionController@index');
        Route::get('feedback-question/{id}','Api\Feedback\FeedbackQuestionController@show');
        Route::post('feedback-question','Api\Feedback\FeedbackQuestionController@store');
        Route::put('feedback-question/{id}','Api\Feedback\FeedbackQuestionController@update');
        Route::delete('feedback-question/{id}','Api\Feedback\FeedbackQuestionController@destroy');

        Route::post('feedback-answer','Api\Feedback\FeedbackAnswerController@store');
        Route::get('feedback-answer/{business}','Api\Feedback\FeedbackAnswerController@report');
        Route::get('feedback-report-by-question/{question}','Api\Feedback\FeedbackReportController@AnswersByQuestion')->middleware('permission:feedback-report.show');   

        Route::resource('bm-register-fields', 'Api\BusinessMarket\BMRegisterFieldsController');
        Route::get('bm-register-fields-data', 'Api\BusinessMarket\BMRegisterFieldsDataController@index');  
        Route::get('bm-register-fields-data/{id}', 'Api\BusinessMarket\BMRegisterFieldsDataController@show');
        Route::put('bm-register-fields-data/{id}', 'Api\BusinessMarket\BMRegisterFieldsDataController@update');
        Route::delete('bm-register-fields-data/{id}', 'Api\BusinessMarket\BMRegisterFieldsDataController@destroy');
        Route::resource('bm-habeas','Api\BusinessMarket\HabeasDataController');
                
        Route::get('certificate/{id}','Api\Certificate\CertificateController@show')->middleware('permission:events.show');
        Route::post('certificate','Api\Certificate\CertificateController@store')->middleware('permission:events.store');
        Route::put('certificate/{id}','Api\Certificate\CertificateController@update')->middleware('permission:events.update');
        Route::get('certificate-tracking-report/{id}/{type?}','Api\Certificate\CertificateController@trackingReport')->middleware('permission:events.index');
        Route::get('certificate-tracking/{id}','Api\Certificate\CertificateController@trackingDownload')->middleware('permission:events.show');

        Route::resource('networkings', 'Api\Networking\NetworkingController');
        Route::get('networkingMessages/{networking}','Api\Networking\NetworkingChatController@networkingMessages')->middleware('permission:networkings.show');
        Route::post('networkingMessages','Api\Networking\NetworkingChatController@store')->middleware('permission:networkings.store');

        Route::post('importUsers','Api\RegisterEvent\RegisterEventController@importUser')->middleware('permission:registerEvent.show');
        Route::post('importInvitations','Api\RegisterEvent\RegisterEventController@importUserInv')->middleware('permission:registerEvent.show');
        Route::get('showQRInformation/{id}','Api\Event\UrlInvitationController@showQRInformation')->middleware('permission:urlInvitations.show');
        //token integraci??n con events3d        
        Route::post('createTokenTo3d','Api\RegisterEvent\RegisterEventController@createTokenToExternalEvent');
        //staff
        Route::resource('staffAccess','Api\Staff\StaffAccessController');
        Route::get('staffAccessEvent/{event}','Api\Staff\StaffAccessController@accessEvent')->middleware('permission:staffAccess.show');
        Route::post('staffAccessAssign','Api\Staff\StaffAccessController@staffAssign')->middleware('permission:staffAccess.show');



        //Fonts
        Route::resource('fonts', 'Api\Font\FontController');

        //Stickers
        Route::get('stickers-event/{id}','Api\Sticker\StickerController@index')->middleware('permission:sticker.index');
        Route::get('sticker/{id}','Api\Sticker\StickerController@show')->middleware('permission:sticker.show');
        Route::get('stickerEvent/{event}','Api\Sticker\StickerController@showEventSticker')->middleware('permission:sticker.show');
        Route::post('sticker','Api\Sticker\StickerController@store')->middleware('permission:sticker.store');
        Route::put('sticker/{id}','Api\Sticker\StickerController@update')->middleware('permission:sticker.update');
        Route::delete('sticker/{id}','Api\Sticker\StickerController@destroy')->middleware('permission:sticker.destroy');

        //sticker users
        Route::get('SearchStickerEmail/{email}/{event}','Api\Sticker\StickerUserController@showForEmail')->middleware('permission:sticker.show');
        Route::post('stickerUser','Api\Sticker\StickerUserController@store')->middleware('permission:sticker.store');
        Route::get('showStikerUser/{user}/{event}','Api\Sticker\StickerUserController@showStikerUser')->middleware('permission:sticker.show');
        Route::get('userWithoutSticker/{event}','Api\Sticker\StickerUserController@userWithoutSticker')->middleware('permission:sticker.show');
        Route::get('printedSticker/{stickerUserId}','Api\Sticker\StickerUserController@printStatus')->middleware('permission:sticker.update');
        Route::get('listStickerUsers/{event}','Api\Sticker\StickerUserController@listStickerUsers')->middleware('permission:sticker.index');

        Route::get('email-track-report/{model}/{id}/{type?}/{action?}','Api\EmailTracking\EmailTrackingController@report')->middleware('permission:events.store');

        Route::get('eventMetrics/{id}','Api\Event\EventController@metrics')->middleware('permission:events.show');
        Route::get('showUserEvent/{event}','Api\Event\EventController@showEventUsers')->middleware('permission:events.show');
        Route::post('trackingActivity','Api\Activity\ActivityController@storeTracking')->middleware('permission:activities.show');
        Route::get('activityUsers/{activity}','Api\Activity\ActivityController@activityUsers')->middleware('permission:activities.show');
        Route::get('activityMetrics/{id}','Api\Activity\ActivityController@metrics')->middleware('permission:activities.store');

        //EventStyles
        Route::resource('eventStyle','Api\Event\EventStyleController');

        //EventType
        Route::resource('eventType','Api\Event\EventTypeController');

        //admin Users
        Route::get('showAdminUsers','Api\Auth\AuthController@showAdminUsers')->middleware('permission:activities.store');

        //EventType
        Route::resource('hallType','Api\Hall\HallTypeController');

    });


    Route::get('execute_command','Api\Commands\ExecuteCommand@VerifyTransactionPayu');

    Route::group(['middleware' => 'cxSummit'], function(){
        Route::post('registerUserApp','Api\Event\EventController@appUserExternal');
        Route::post('createEvent','Api\Event\EventController@storeExternal');

        //Para implementacion de api de usuarios
        Route::get('users-event/{user}','Api\User\UserController@minimalData');
    });

    Route::post('token-agora','Api\Agora\AgoraController@genToken');
    
    /* Sondeo */
    Route::get('pollQuestionsEvent-wh/{id}','Api\Poll\PollQuestionController@showQuestionsEvent');
    Route::get('probe-questions-activity-wh/{id}','Api\Probe\ProbeController@showProbesPublic');
    Route::get('probe-questions-probe-wh/{id}','Api\Probe\ProbeQuestionController@showQuestions');
    Route::get('poll-answers-question/{id}','Api\Poll\AnswersController@AnswersByQuestion');
    
   
    Route::get('bm-habeas-outside/{id}','Api\BusinessMarket\HabeasDataController@show');
    Route::get('bmr-fields-business/{id}', 'Api\BusinessMarket\BMRegisterFieldsController@showFieldsBusiness');
    Route::post('bm-register-fields-data', 'Api\BusinessMarket\BMRegisterFieldsDataController@store');
    Route::get('business-market-without-log','Api\BusinessMarket\BusinessMarketController@index');
    Route::get('business-market/{id}','Api\BusinessMarket\BusinessMarketController@show');
    Route::get('participants/{id}/{filter?}','Api\BusinessMarket\BusinessMarketController@participants');
    Route::get('zoom', function () {
        return view('zoom');
    });
    Route::get('editor/index', function (Request $request) {
        if($request->action == "gen-certificate" && $request->route == "certificate")
            return view('download');  
        else
            //return view('editor');
            return view('certificate');
        });
    Route::get('editor/{tipo}/{filename?}/{ruta1?}/{ruta2?}', function ($tipo,$filename,$ruta1 = null,$ruta2 = null)
    {
        if(Cache::has($filename)){
            
            $response = Response::make(Cache::get($filename), 200);
            //response->header("Content-Type", $type);
            return $response;
        }

        
        if($ruta2)
            $path = storage_path('../public/'.$tipo.'/' . $filename.'/'.$ruta1.'/'.$ruta2);
        else if($ruta1)
            $path = storage_path('../public/'.$tipo.'/' . $filename.'/'.$ruta1);
        else if($filename)
            $path = storage_path('../public/'.$tipo.'/' . $filename);
        else
            $path = storage_path('../public/'.$tipo);
        
        //return $path;
        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        Cache::forever($filename, $file);
        $response->header("Content-Type", $type);

        return $response;
    });
    
    Route::get('mail-tracking/{id}/{action?}','Api\EmailTracking\EmailTrackingController@store')->name('mail-tracking');
    
    //--/networking
    Route::group(['prefix' => 'networking-wa'], function (){
        Route::get(
            'get-participants/{idEvent}',
            'Api\WebApp\Networking\NetworkingController@getParticipants'
        );
        Route::post(
            'send-solicitud',
            'Api\WebApp\Networking\NetworkingController@sendSolicitud'
        );
        Route::put(
            'aceptar-solicitud/{id}',
            'Api\WebApp\Networking\NetworkingController@aceptarSolicitud'
        );
        Route::put(
            'rechazar-solicitud/{id}',
            'Api\WebApp\Networking\NetworkingController@rechazarSolicitud'
        );
        Route::delete(
            'eliminar-solicitud/{id}',
            'Api\WebApp\Networking\NetworkingController@eliminarSolicitud'
        );
        Route::get(
            'get-solicitudes-recibidas',
            'Api\WebApp\Networking\NetworkingController@getSolicitudesRecibidas'
        );
        Route::get(
            'get-solicitudes-enviadas',
            'Api\WebApp\Networking\NetworkingController@getSolicitudesEnviadas'
        );
        Route::get(
            'chats-user',
            'Api\WebApp\Networking\NetworkingController@getChatsUsuario'
        );
        Route::get(
            'chat-info/{key}',
            'Api\WebApp\Networking\NetworkingController@chatInfo'
        );
        Route::delete(
            'delete-solicitud/{id}',
            'Api\WebApp\Networking\NetworkingController@deleteSolicitud'
        );
        Route::post(
            'store-message', 'Api\WebApp\Networking\NetworkingController@storeMessage'
        );

        Route::post(
            'messages/{id}', 'Api\WebApp\Networking\NetworkingController@getMessages'
        );

        Route::post(
            'get-notifications',
            'Api\WebApp\NotificationController@getNotifications'
        );

        Route::put(
            'read-notification/{id}',
            'Api\WebApp\NotificationController@readNotification'
        );

        Route::post(
            'add-notification/{idUser}',
            'Api\WebApp\NotificationController@addNotification'
        );
    });
});


Route::group(['prefix' => 'v1'],function(){    
    Route::get('list-activities-for-event/{event}','Api\WebApp\RateActivity\RateActivityController@getActivitiesForEvent');

    // Reporte de la calificacion de las actividades
    Route::get('get-report-rate-for-event/{event}','Api\WebApp\RateActivity\RateActivityController@getReportRateForEvent');
    Route::get('get-participantes/{event}','Api\WebApp\RateActivity\RateActivityController@getParticipantes');

    // Managent Slider logos   
    Route::put('update-slider-logos', 'Api\WebApp\SliderLogos\SliderLogosController@updateStylesLogos');
    Route::get('list-logos/{event}', 'Api\WebApp\SliderLogos\SliderLogosController@getAllSliderLogos');
    Route::post('create-item-logo', 'Api\WebApp\SliderLogos\SliderLogosController@createNewLogo');
    Route::delete('delete-item-logo/{id}', 'Api\WebApp\SliderLogos\SliderLogosController@removeItemLogo');

    // Management On Demand  
    Route::get('on-demand/{event}', 'Api\WebApp\Ondemand\OnDemandController@getAllOnDemandForIdevent');
    Route::post('create-on-demand', 'Api\WebApp\Ondemand\OnDemandController@createNewOndemand');
    Route::put('update-on-demand', 'Api\WebApp\Ondemand\OnDemandController@updateItemOnDemand');
    Route::delete('delete-on-demand/{id}', 'Api\WebApp\Ondemand\OnDemandController@removeItemOnDemand');

    // Management certificate
    Route::get('view-certificate/{event}', 'Api\Certificate\CertificateManagementController@viewCertificate');
    Route::get('download-certificate/{event}/{user?}', 'Api\Certificate\CertificateManagementController@downloadCertificate');
    Route::get('get-certificate-event/{event}', 'Api\Certificate\CertificateManagementController@getCertifictesForEvent');
    Route::post('save-certificate', 'Api\Certificate\CertificateManagementController@saveCertificate');
    Route::post('add-images-certificate', 'Api\Certificate\CertificateManagementController@saveAddImage');
    Route::post('remove-images-certificate', 'Api\Certificate\CertificateManagementController@removeAddImage');

    Route::get('certificate-prueba', function () {
        return view('certificate');
        /* if($request->action == "gen-certificate" && $request->route == "certificate")
            return view('download');  
        else
            return view('certificate');
        } */
    });

    ///Auth not necessary
    Route::post('payment/{type_wallet}','Api\Payment\PaymentController@store');   
    Route::get('payment-callback/{type_wallet}','Api\Payment\PaymentController@callback');
    Route::get('probe-answers-question/{id}','Api\Probe\AnswersController@AnswersByQuestion');
    Route::get('validateUser/{email}', 'Api\Auth\AuthController@validateUser');
    Route::post('auth/register', 'Api\Auth\AuthController@register')->name('register');
    Route::post('dataRegistersExternal','Api\RegisterEvent\DataRegisterController@store');
    Route::post('eventUsers','Api\Event\EventController@eventUsers');
    
    //request external 
    //-landing
    //--/Tracking
    Route::resource('loginTracking', 'Api\Tracking\LoginTrackingController');
    Route::get('showEvent/{event}','Api\Event\EventController@show');
    Route::get('styleEvent/{event}','Api\Event\EventStyleController@show'); 
    Route::get('activitiesEventExternal/{id}/{user_id?}','Api\Activity\ActivityController@activitiesEvent');
    Route::get('hallsEvent/{event}','Api\Hall\HallController@showHallsEvent');
    Route::get('ticketsEventExternal/{event}','Api\Ticket\TicketController@showTicketsEvent');
    Route::get('hallsExternal/{hall}','Api\Hall\HallController@show');
    Route::get('activityExternal/{activity}/{user_id?}','Api\Activity\ActivityController@show');
    Route::get('eventChatext/{event_id}','Api\Event\EventController@getChatMessagesEvent');
    Route::get('activityMessagesExtLand/{activity}','Api\Activity\ActitivyChatController@activityMessages');
    Route::get('questionsForActivityExt/{activity}/{user}','Api\Activity\QuestionActivityController@questionsForActivity');
    Route::get('probe-questions-activity-whExt/{id}','Api\Probe\ProbeController@showProbesPublic');
    Route::post('token-agora','Api\Agora\AgoraController@genToken');

    //--/login
    Route::post('auth/basic', 'Api\Auth\AuthController@login')->name('login');
    Route::put('auth/killer', 'Api\Auth\AuthController@logoutjwt');    
    //--/register
    Route::get('habeasdataExternal/{id}','Api\HabeasData\HabeasDataController@show');
    Route::get('fieldsEventExternal/{event}','Api\RegisterEvent\RegisterEventController@showFieldsEvent');
    Route::get('eventTypesExt', 'Api\Event\EventTypeController@index'); 
    Route::get('peopleLimit/{event}','Api\Event\EventController@getPeopleLimit');

    //--/Califiaciones por actividad
    Route::post('/save-rate-activity', 'Api\WebApp\RateActivity\RateActivityController@saveRateActivity');
    Route::get('/get-rate-activity/{activity_id}/{user_id}', 'Api\WebApp\RateActivity\RateActivityController@getRateActivity');

    //--/Galleria web app
    Route::post('/upload-image-gallery', 'Api\Gallery\GalleryController@uploadImage');
    Route::post('/removed-gallery-picture', 'Api\Gallery\GalleryController@removedImage');
    Route::post('/save-picture-gallery', 'Api\Gallery\GalleryController@saveImage');
    Route::post('/save-like-gallery', 'Api\Gallery\GalleryController@saveLikeGallery');
    Route::post('/remove-like-gallery/{id}', 'Api\Gallery\GalleryController@removeLikeGallery');
    Route::get('/get-data-gallery/{event_id}/{user_id}', 'Api\Gallery\GalleryController@getDataGallery');
    Route::get('/get-data-gallery-home/{id}', 'Api\Gallery\GalleryController@getDataGalleryHome');
    Route::get('/get-data-gallery-like/{id}/{user}', 'Api\Gallery\GalleryController@getDatalLikeGallery');

    //--/Ciudades
    Route::get('/get-countries-event', 'Api\WebApp\CountriesCities\CountriesCitiesController@getCountries')->name('get.data.countries');
    Route::get('/get-cities-event/{code}', 'Api\WebApp\CountriesCities\CountriesCitiesController@getCities')->name('get.data.cities');

    //--/Feria comercial

    //--/Notificaciones por evento
    Route::get('/get-list-notifications-event/{id}', 'Api\Notification\NotificationController@getNotifications');
    Route::post('/save-notification-news', 'Api\Notification\NotificationController@saveNotifications');
    Route::get('/get-notification-users/{event_id}/{user_id}', 'Api\Notification\NotificationController@getNotificationsUser');
    Route::post('/notification-read', 'Api\Notification\NotificationController@readNotification');

    //--/Actividades Favoritas
    Route::get('/get-list-favorites-activities/{event_id}/{user_id}', 'Api\WebApp\Favorites\FavoritesController@getFavoritesActivities');
    Route::post('/save-favorite-activity', 'Api\WebApp\Favorites\FavoritesController@saveIsFavorite');
    Route::post('/remove-favorite-activity/{id}', 'Api\WebApp\Favorites\FavoritesController@removeIsFavorite');

    //--/Agenda web app
    Route::get('/get-schedule-event/{event}/{user_id}', 'Api\WebApp\Schedule\ScheduleController@getSchedule')->name('get.data.schedule');
    Route::get('/get-count-days-schedule/{event}', 'Api\WebApp\Schedule\ScheduleController@getCountDaysSchedule')->name('get.data.count.days.schedule');

    //--/Networking+
    Route::post('/on-call/{id}', 'Api\WebApp\Networking\NetworkingController@onCall');
    Route::post('/out-call/{id}', 'Api\WebApp\Networking\NetworkingController@outCall');
    Route::get('/user-call-available/{id}', 'Api\WebApp\Networking\NetworkingController@userCallAvailable');

    //--/Guardar encuesta+
    Route::post('poll-save-webapp', 'Api\WebApp\Poll\PollController@pollSaveWebApp')->name('poll.save.webapp');
    
    //--/Guardar probe
    Route::post('probe-save-webapp', 'Api\WebApp\Probe\ProbeController@probeSaveWebApp')->name('probe.save.webapp');
    Route::get('probe-verify-user-webapp/{id}', 'Api\WebApp\Probe\ProbeController@verifyUserProbe')->name('probe.verify.webapp');

    //--web app
    //--/login 
    Route::post('auth-wa/basic', 'Api\Auth\AuthController@loginWA')->name('loginWA');
    Route::post('/inactive-online-user/{id}', 'Api\Auth\AuthController@inactiveOnlineUser');
    Route::post('/active-online-user/{id}', 'Api\Auth\AuthController@activeOnlineUser'); 

    // refresh info
    Route::get('/refresh-token', 'Api\Auth\AuthController@refreshToken'); 
    Route::post('/refresh-data-user', 'Api\User\UserController@getDataUser'); 
    Route::post('/upload-photo-user', 'Api\User\UserController@uploadPhotoProfile'); 
    Route::post('/remove-photo-user', 'Api\User\UserController@removedPhotoProfile'); 

    Route::put('restorePassword/{user}', 'Api\Auth\AuthController@updatePassword');
    
    //--/detail event
    Route::post('validPathEvent','Api\Event\EventController@validPathEvent');
});