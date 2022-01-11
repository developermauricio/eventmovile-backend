<?php

namespace App\Http\Controllers\Api\Ticket;

use App\Activity;
use App\EventInvitation;
use App\EventUser;
use App\Http\Controllers\Controller;
use App\Payment;
use App\payments_payu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MercadoPago;
use App\Ticket;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function index()
    {

        $role = auth()->user()->getRoleNames()->first();
        $user = DB::table('users')->where('id', Auth()->id())->first();

        $ticket = Ticket::orderBy('id', 'desc')->get();

        if ($role == "admin") {
            $ticket = DB::table('tickets as t')
                ->select('t.*', 'e.name as event')
                ->join('events as e', 'e.id', '=', 't.event_id')
                ->where('e.company_id', $user->model_id)
                ->orderBy('id', 'desc')
                ->get();
        }
        return $this->showAll($ticket, 200);
    }

    public function show(Ticket $ticket)
    {

        return $this->showOne($ticket);
    }

    public function showTicketsEvent($event)
    {
        $ticket = Ticket::where('event_id', $event)->orderBy('id', 'desc')->get();

        return $this->showAll($ticket);
    }

    public function store(Request $request)
    {

        $rules = [
            'event_id'   => 'required|exists:events,id',
            'name'       => 'required|max:200',
            'description' => 'required|max:200',
            'activities' => 'required',
            'unit_price' => 'required',
        ];

        $this->validate($request, $rules);
        $activities = json_decode($request->activities);
        //validate if it is array
        if (gettype($activities) !== 'array') {
            return $this->successResponse(['data' => '', 'message' => 'Error in the activities.'], 400);
        }
        //update for payment in activities
        foreach ($activities as $value) {
            $activity = Activity::find($value);
            $activity->payment = 1;
            $activity->save();
        }
        $ticket = Ticket::create([
            'event_id'   => $request->event_id,
            'name'       => $request->name,
            'description' => $request->description,
            'activities' => $request->activities,
            'unit_price' => $request->unit_price
        ]);

        return $this->successResponse(['data' => $ticket, 'message' => 'Ticket Created'], 201);
    }

    public function update(Request $request, Ticket $ticket)
    {

        $rules = [
            'event_id'   => 'required|exists:events,id',
            'name'       => 'required|max:200',
            'description' => 'required|max:200',
            'activities' => 'required',
            'unit_price' => 'required',
        ];


        $this->validate($request, $rules);
        $existsEventInvitation = false;
        $modifyActivities = false;
        $activities = json_decode($request->activities);
        $activities_of_ticket = json_decode($ticket->activities);
        sort($activities);
        sort($activities_of_ticket);
        if (count(array_diff($activities_of_ticket, $activities)) > 0) {
            $modifyActivities = true;
        }
        //verify of ticket was buy
        $existPayment = false;
        $payments_payu = payments_payu::where('ticket_id', $ticket->id)->get();
        $payments = Payment::where('ticket_id', $ticket->id)->get();
        if (count($payments_payu) > 0) {
            foreach ($payments_payu as $value) {
                if($value->status=='APPROVED'){
                    $existPayment = true;
                }
            }
        }
        if (count($payments) > 0) {
            foreach($payments as $value){
                if($value->status=='approved'){
                    $existPayment = true;
                }
            }
        }

        if ($modifyActivities == true && $existPayment) {
            Log::info('si');
            $eventsInvitations = EventInvitation::where('event_id', $ticket->event_id)->get();
            if (count($eventsInvitations) > 0) {
                foreach ($activities_of_ticket as $activitie_of_ticket) {
                    foreach ($eventsInvitations as $eventInvitation) {
                        $activity_event_invitation = json_decode($eventInvitation->activities);
                        if (in_array($activitie_of_ticket, $activity_event_invitation)) {
                            $existsEventInvitation = true;
                        }
                    }
                }
            }
            if ($existsEventInvitation) {
                return $this->successResponse(['data' => '', 'message' => 'Ya hay usuarios que han comprado este ticket, no lo puede modificar'], 400);
            }
        }
        //validate if it is array
        if (gettype($activities) !== 'array') {
            return $this->successResponse(['data' => '', 'message' => "Error in the activities. It isn't array"], 400);
        }
        $tickets = Ticket::where('event_id', $request->event_id)->get();
        if (isset($ticket->id)) {
            $activities_of_ticket = json_decode($ticket->activities);
            if (gettype($activities_of_ticket) == 'array') {
                foreach ($activities_of_ticket as $activity_of_ticket) {
                    $count = 0;
                    foreach ($tickets as  $tick) {
                        if ($tick->id != $ticket->id) {
                            $activities_of_ticket2 = json_decode($tick->activities);
                            if (gettype($activities_of_ticket2) == 'array') {
                                foreach ($activities_of_ticket2 as $activity_of_ticket2) {
                                    if ($activity_of_ticket2 == $activity_of_ticket) {
                                        $count++;
                                    }
                                }
                            }
                        }
                    }
                    if ($count == 0) {
                        $modActivity = Activity::find($activity_of_ticket);
                        $modActivity->payment = 0;
                        $modActivity->save();
                    } else {
                        Log::info($count);
                        Log::info($activity_of_ticket);
                    }
                }
            }
        }

        //update for payment in activities
        foreach ($activities as $value) {
            $activity = Activity::find($value);
            $activity->payment = 1;
            $activity->save();
        }

        $ticket->fill($request->all());

        if ($ticket->isClean()) {
            return $this->successResponse(['data' => $ticket, 'message' => 'At least one different value must be specified to update'], 201);
        }

        $ticket->save();

        return $this->successResponse(['data' => $ticket, 'message' => 'Ticket Updated'], 201);
    }

    public function destroy(Ticket $ticket)
    {
        $existPayment = false;
        $payments_payu = payments_payu::where('ticket_id', $ticket->id)->get();
        $payments = Payment::where('ticket_id', $ticket->id)->get();
        if (count($payments_payu) > 0) {
            foreach ($payments_payu as $value) {
                if($value->status=='APPROVED'){
                    $existPayment = true;
                }
            }
        }
        if (count($payments) > 0) {
            foreach($payments as $value){
                if($value->status=='approved'){
                    $existPayment = true;
                }
            }
        }
        if($existPayment){
            return $this->successResponse(['data' => '', 'message' => 'No se puede eliminar el ticket, tiene compras asociadas'], 400);
        }
        $tickets = Ticket::where('event_id', $ticket->event_id)->get();
        $activities_of_ticket_to_delete = json_decode($ticket->activities);
        if (gettype($activities_of_ticket_to_delete) == 'array') {
            foreach ($activities_of_ticket_to_delete as $activity_of_ticket_to_delete) {
                $count = 0;
                foreach ($tickets as $all_ticket) {
                    $activities_of_ticket_the_allEvent = json_decode($all_ticket->activities);
                    foreach ($activities_of_ticket_the_allEvent as $activity_of_ticket_the_allEvent) {
                        if ($ticket->id != $all_ticket->id) {
                            if ($activity_of_ticket_the_allEvent == $activity_of_ticket_to_delete) {
                                $count++;
                            }
                        }
                    }
                }
                if ($count == 0) {
                    Log::info('Entro a cambiar payment');
                    $activity_to_modify = Activity::find($activity_of_ticket_to_delete);
                    $activity_to_modify->payment = 0;
                    $activity_to_modify->save();
                } else {
                    Log::info('No tiene que cambiar payment');
                }
            }
        }
        $ticket->delete();
        return $this->successResponse(['data' => $ticket, 'message' => 'Ticket Deleted'], 201);
    }
}
