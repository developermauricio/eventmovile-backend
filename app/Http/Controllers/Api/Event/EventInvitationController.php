<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

use App\EventInvitation;
use App\Event;
use App\Activity;
use App\UrlInvitation;
use App\Traits\sendEmail;
use Illuminate\Support\Facades\Log;

class EventInvitationController extends Controller
{
    use sendEmail;

    public function index()
    {
        //
        $role = auth()->user()->getRoleNames()->first();
        $user = DB::table('users')->where('id', Auth()->id())->first();

        if ($role == "super admin") {
            $invitations = EventInvitation::orderBy('id', 'desc')->all();
        }
        if ($role == "admin") {
            $invitations = EventInvitation::select('event_invitations.*')
                ->join('events as e', 'e.id', '=', 'event_invitations.id')
                ->where('e.company_id', $user->model_id)
                ->orderBy('event_invitations.id', 'desc')
                ->get();
        }
        if ($role == "guest") {
        }


        return $this->showAll($invitations, 200);
    }
    public function show(EventInvitation $eventInvitation)
    {
        return $this->showOne($eventInvitation);
    }

    public function showInvitations($event)
    {
        $invitations = EventInvitation::where('event_id', $event)
            ->orderBy('id', 'desc')
            ->get();

        return $this->showAll($invitations);
    }

    public function store(Request $request)
    {
        $youHaveInvitationsToAnEvent = "Tienes <mtm> invitaciones a un evento :";
        $useInvitation = "Usar invitación";
        $messageUrlShared = "o puedes compartir las urls para cada uno de los invitados:";
        if (isset($request->youHaveInvitationsToAnEvent) && isset($request->useInvitation) && isset($request->messageUrlShared)) {
            if ($request->youHaveInvitationsToAnEvent !== "" && $request->useInvitation !== "" && $request->messageUrlShared !== "") {
                $youHaveInvitationsToAnEvent = $request->youHaveInvitationsToAnEvent;
                $useInvitation = $request->useInvitation;
                $messageUrlShared = $request->messageUrlShared;
            }
        }
        $rules = [
            'event_id' => 'required|exists:events,id',
            'email'  => 'required|max:100',
            'name'  => 'required|max:100',
            'activities' => 'required',
            'quantity'  => 'required|min:1',
        ];

        $this->validate($request, $rules);

        $event = Event::where('id', $request->event_id)->with('style')->first();
        Log::info("Evento hecho:");
        Log::info($event);
        $arrayActivities = json_decode($request->activities);
        Log::info($arrayActivities);
        if (is_array($arrayActivities) == false) {
            return $this->errorResponse('Activities not found', 500);
        }
        for ($i = 0; $i < count($arrayActivities); $i++) {
            $activity = Activity::find($arrayActivities[$i]);

            if (!$activity) {
                return $this->errorResponse('Activity does not exist, id:' . $arrayActivities[$i], 500);
            }

            //aqui validar las actividades que han sido compradas para no exceder el limite de asistentes

            if ($activity->guests_limit > 0) {
                if ($request->quantity > $activity->guests_limit) {
                    return $this->errorResponse('This invitation execeds the number of guests for this activity name:' . $activity->name . ', there are ' . ($activity->guests_limit - $guestAccept) . ' invitations available', 500);
                }
            }
        }

        $eventInvitation = EventInvitation::create([
            'event_id' => $request->event_id,
            'email'  => $request->email,
            'name'  => $request->name,
            'activities' => $request->activities,
            'quantity'  => $request->quantity,
        ]);

        $tokens = '';
        $urls = "";
        $paramsOfStyles=['email_btn_color','email_btn_text_color'];
        foreach($paramsOfStyles as $styles){
            if(isset($event->style->$styles)==false){
                return $this->errorResponse('Porfavor edite los estilos de la plantilla de correo!', 400);
            }
        }
        for ($i = 0; $i < $eventInvitation->quantity; $i++) {
            $token = Str::random(5);

            $verifyToken = UrlInvitation::select('token')
                ->where('token', $token)->first();

            while (isset($verifyToken->token)) {
                $token = Str::random(5);
                $verifyToken = UrlInvitation::select('token')->pluck('token')
                    ->where('token', $token)->first();
            }

            $urlInvitation = UrlInvitation::create([
                'url' => 'Url',
                'token' => $token,
                'user_id' => null,
                'invitation_id' => $eventInvitation->id,
                'actived' => false,
            ]);
            $urls = $urls . "<br>" . env('FRONT') . '#/Register-Event-Token?token=' . $token;
            $tokens = $tokens . "<br><a target='_blank'  href='" . env('FRONT') . '#/Register-Event-Token?token=' . $token . "'>
            <button style='background-color:" . $event->style->email_btn_color . "; color:" . $event->style->email_btn_text_color . ";padding: 10px 20px;
            width: 30%; max-width: 200px; height: 40px; border:none; border-radius: 5px;margin-top:10px;'>" . $useInvitation . "</button><a>";
        }

        if ($eventInvitation->quantity > 1) {
            $txtEmail = str_replace('<mtm>', strval($eventInvitation->quantity), $youHaveInvitationsToAnEvent);
            // $txtEmail = "Tienes ".$eventInvitation->quantity." invitaciones a un evento :";
            // $urlsText = "<br><br>o puedes compartir las urls para cada uno de los invitados:".$urls;
            $urlsText = "<br><br>" . $messageUrlShared . $urls;
        } else {
            // $txtEmail = "Tienes ".$eventInvitation->quantity." invitación a un evento:";
            $txtEmail = str_replace('<mtm>', strval($eventInvitation->quantity), $youHaveInvitationsToAnEvent);
            $urlsText = "";
        }

        $tokens = $txtEmail . $tokens . $urlsText;
        $templete = view('events.standar', ["event" => $event, "message" => $tokens]);
        $templete = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, htmlentities($templete));
        $templete = html_entity_decode($templete);

        $email = $this->sendEmail($eventInvitation->email, $event->name, $templete, true, "formInvitations", "EventInvitation", $eventInvitation->id);

        if ($email) {
            return $this->successResponse(['data' => $eventInvitation, 'message' => 'Event invitation Created'], 201);
        }

        return $this->errorResponse('Invitation created but It was not sent email', 501);
    }



    public function update(Request $request, EventInvitation $eventInvitation)
    {

        $rules = [
            'event_id'  => 'required|exists:events,id',
            'email'  => 'required|max:100',
            'activities' => 'required',
            'quantity'  => 'required|min:1',
        ];

        $this->validate($request, $rules);

        $eventInvitation->fill($request->all());

        if ($eventInvitation->isClean()) {
            return $this->successResponse(['data' => $eventInvitation, 'message' => 'At least one different value must be specified to update'], 201);
        }

        $eventInvitation->save();

        return $this->successResponse(['data' => $eventInvitation, 'message' => 'Event invitation Updated'], 201);
    }

    public function destroy(EventInvitation $eventInvitation)
    {
        $eventInvitation->delete();
        return $this->successResponse(['data' => $eventInvitation, 'message' => 'Event invitation Deleted'], 201);
    }
}
